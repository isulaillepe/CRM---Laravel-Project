<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Mail\CustomerInvoiceMail;
use App\Mail\PaymentConfirmation; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripePaymentController extends Controller
{
    /**
     * Generate the Stripe session link AND email it directly to the customer.
     */
    public function sendInvoiceEmail($invoiceId)
    {
        // 1. Fetch invoice data along with the client relationship properties
        $invoice = Invoice::with('customer')->findOrFail($invoiceId);

        // Safety block: Don't send emails for things that are already settled!
        if ($invoice->status === 'paid') {
            return back()->with('error', 'This invoice has already been fully paid.');
        }

        // 2. Authorize with your Stripe Secret Test Key from services config
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // 3. Request a Hosted Checkout Link from Stripe
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'lkr',
                        'product_data' => [
                            'name' => "Invoice Settle: #" . $invoice->invoice_number,
                        ],
                        'unit_amount' => (int) ($invoice->amount * 100), // convert to cents for Stripe
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                // When payment passes, Stripe appends the real session ID to this query parameter string
                'success_url' => route('payment.success', ['invoice' => $invoice->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel', ['invoice' => $invoice->id]),
                'customer_email' => $invoice->customer->email,
            ]);

            // 4. Extract the unique URL returned by the Stripe Cloud API
            $stripeCheckoutUrl = $session->url;

            // 5. DISPATCH THE INVOICE EMAIL LIVE TO THE CLIENT!
            Mail::to($invoice->customer->email)->send(
                new CustomerInvoiceMail($invoice, $stripeCheckoutUrl)
            );

            return back()->with('success', 'Invoice email containing secure payment gateway link dispatched!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gateway Error: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment success redirect loop from Stripe.
     */
    public function paymentSuccess(Request $request, Invoice $invoice)
    {
        // 1. Double check the status to prevent accidental multi-click transaction duplicates
        if ($invoice->status !== 'paid') {
            
            // 2. Flip database status flags cleanly to paid
            $invoice->update(['status' => 'paid']);

            // 3. Log a permanent record to your Audited Transactions ledger
            $transaction = Transaction::create([
                'invoice_id' => $invoice->id,
                'stripe_session_id' => $request->get('session_id') ?? 'MOCK_STRIPE_SESSION_ID',
                'amount_paid' => $invoice->amount,
                'currency' => 'LKR',
                'payment_status' => 'completed'
            ]);

            // 4.  AUTOMATED TRIGGER: Email the thank you receipt straight to the customer profile!
            if ($invoice->customer && $invoice->customer->email) {
                Mail::to($invoice->customer->email)->send(new PaymentConfirmation($transaction));
            }
        }

        // 5. Hand control back over to your single-page Vue component view while triggering a flash notice
        return redirect()->route('invoices.index')->with('success', "Payment successfully processed for Invoice #{$invoice->invoice_number}!");
    }

    /**
     * Handle payment cancel loops.
     */
    public function paymentCancel(Invoice $invoice)
    {
        return redirect()->route('invoices.index')->with('error', "Payment for invoice #{$invoice->invoice_number} was cancelled.");
    }
}