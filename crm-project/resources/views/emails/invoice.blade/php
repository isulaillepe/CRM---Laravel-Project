<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Settle Notification</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f7; color: #51545e; margin: 0; padding: 40px; }
        .email-wrapper { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        h1 { color: #333333; font-size: 22px; margin-top: 0; }
        .amount-display { font-size: 32px; font-weight: bold; color: #111111; margin: 20px 0; font-family: monospace; }
        .btn-payment { display: inline-block; background-color: #059669; color: #ffffff !important; text-decoration: none; padding: 12px 30px; font-weight: bold; border-radius: 6px; margin: 25px 0; font-size: 16px; text-align: center; }
        .btn-payment:hover { background-color: #047857; }
        .footer { font-size: 12px; color: #a8a8a8; margin-top: 40px; border-top: 1px solid #eef2f5; padding-top: 20px; }
    </style>
</head>
<body>

    <div class="email-wrapper">
        <h1>Hi {{ $invoice->customer->name }},</h1>
        <p>A new invoice has been generated for your account regarding your ongoing project subscriptions.</p>
        
        <table width="100%" style="margin: 20px 0; font-size: 14px;">
            <tr><td><strong>Invoice Reference:</strong></td><td>#{{ $invoice->invoice_number }}</td></tr>
            <tr><td><strong>Payment Due Date:</strong></td><td>{{ \Carbon\Carbon::parse($invoice->due_date)->toFormattedDateString() }}</td></tr>
        </table>

        <p>Total Balance Due:</p>
        <div class="amount-display">${{ number_format($invoice->amount, 2) }} USD</div>

        <div style="text-align: center;">
            <a href="{{ $checkoutUrl }}" class="btn-payment" target="_blank">💳 Pay Invoice Online</a>
        </div>

        <p>Clicking the button above will redirect you to Stripe's completely secure, encrypted processing network to settle your statement balance instantly via Credit/Debit card.</p>

        <div class="footer">
            Sent automatically by your custom CRM Platform Core. If you have any billing inquiries, contact administration support channels.
        </div>
    </div>

</body>
</html>