<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Mail\CustomerInvoiceMail;
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

        // 2. Authorize with your Stripe Secret Test Key
        Stripe::setApiKey(env('STRIPE_SECRET'));

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
                'success_url' => route('payment.success', ['invoice' => $invoice->id]),
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
    public function paymentSuccess(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);
        return redirect()->route('invoices.index')->with('success', "Invoice #{$invoice->invoice_number} paid successfully!");
    }

    /**
     * Handle payment cancel.
     */
    public function paymentCancel(Invoice $invoice)
    {
        return redirect()->route('invoices.index')->with('error', "Payment for invoice #{$invoice->invoice_number} was cancelled.");
    }
}