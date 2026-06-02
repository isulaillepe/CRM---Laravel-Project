<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Mail\CustomerInvoiceMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Transaction;
use Illuminate\Http\Request;
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

        // 2. Authorize with your Stripe Secret Test Key
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
                        'unit_amount' => (int) ($invoice->amount * 100), // convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                // Update status automatically when redirected back
                'success_url' => route('payment.success', ['invoice' => $invoice->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel', ['invoice' => $invoice->id]),
                'customer_email' => $invoice->customer->email,
            ]);

            // 4. GET THE UNIQUE STRIPE URL WE GENERATED
            $stripeCheckoutUrl = $session->url;

            // 5. DISPATCH THE EMAIL LIVE TO THE CLIENT!
            Mail::to($invoice->customer->email)->send(
                new CustomerInvoiceMail($invoice, $stripeCheckoutUrl)
            );

            return back()->with('success', 'Invoice email containing secure payment gateway dispatched!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gateway Error: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment success.
     */
    public function paymentSuccess(Request $request, Invoice $invoice)
    {
        // 1. Double check the invoice isn't already paid to prevent accidental duplicate logging
        if ($invoice->status !== 'paid') {
            
            // 2. Flip database status flags
            $invoice->update(['status' => 'paid']);

            // 3. Log a detailed audit record to your Transactions ledger
            Transaction::create([
                'invoice_id' => $invoice->id,
                'stripe_session_id' => $request->get('session_id') ?? 'MOCK_STRIPE_SESSION_ID',
                'amount_paid' => $invoice->amount,
                'currency' => 'LKR',
                'payment_status' => 'completed'
            ]);
        }

        // 4. Redirect back to your single-page Vue component index while flashing a notice
        return redirect()->route('invoices.index')->with('success', "Payment successfully processed for Invoice #{$invoice->invoice_number}!");
    }

    /**
     * Handle payment cancel.
     */
    public function paymentCancel(Invoice $invoice)
    {
        return redirect()->route('invoices.index')->with('error', "Payment for invoice #{$invoice->invoice_number} was cancelled.");
    }

}