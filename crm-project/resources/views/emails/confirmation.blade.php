<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Received</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f9fafb; color: #374151; padding: 40px; margin: 0; }
        .container { max-width: 600px; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 32px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .logo { font-size: 24px; font-weight: bold; color: #4f46e5; margin-bottom: 24px; }
        h1 { font-size: 22px; font-weight: 700; color: #111827; margin-top: 0; }
        p { font-size: 15px; line-height: 1.6; color: #4b5563; }
        .receipt-card { background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; margin: 24px 0; }
        .receipt-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .receipt-label { color: #166534; font-weight: 600; }
        .receipt-value { color: #14532d; font-weight: 700; font-family: monospace; }
        .total-row { border-top: 1px dashed #bbf7d0; margin-top: 12px; padding-top: 12px; font-size: 16px; }
        .footer { font-size: 12px; color: #9ca3af; text-align: center; margin-top: 32px; border-top: 1px solid #f3f4f6; padding-top: 16px; }
    </style>
</head>
<body>

    <div class="container">
        <div class="logo">⚡ CRM Platform</div>
        
        <h1>Payment Successfully Confirmed!</h1>
        <p>Hi {{ $transaction->invoice->customer->name }},</p>
        <p>Thank you for your payment. Stripe has successfully processed your credit/debit card session transaction, and your invoice statement has been officially marked as settled.</p>
        
        <div class="receipt-card">
            <div class="receipt-row">
                <span class="receipt-label">Invoice Settled: </span>
                <span class="receipt-value">{{ $transaction->invoice->invoice_number }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Stripe Reference: </span>
                <span class="receipt-value">{{ substr($transaction->stripe_session_id, 0, 20) }}...</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Date Processed: </span>
                <span class="receipt-value">{{ $transaction->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div class="receipt-row total-row">
                <span class="receipt-label" style="font-size: 16px;">Amount Captured: </span>
                <span class="receipt-value" style="font-size: 18px; color: #15803d;">
                    Rs. {{ number_format($transaction->amount_paid, 2) }} LKR
                </span>
            </div>
        </div>

        <p>If you have any operational queries regarding this transaction receipt log or your platform subscriptions, please do not hesitate to contact our administration team.</p>
        
        <div class="footer">
            Sent automatically by Isula | CRM System Administrator.<br>
            This email serves as an official electronic payment validation voucher.
        </div>
    </div>

</body>
</html>