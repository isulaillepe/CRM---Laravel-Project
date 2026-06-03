<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to CRM Dashboard!</title>
    <style>
        body {
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0 auto;
        }
        img {
            border: 0;
            outline: none;
            text-decoration: none;
        }
        .wrapper {
            background-color: #f3f4f6;
            padding: 40px 20px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            max-width: 600px;
            margin: 0 auto;
            overflow: hidden;
        }
        .header-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header-title {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 40px 30px;
            color: #374151;
            line-height: 1.6;
        }
        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .intro {
            font-size: 16px;
            margin-bottom: 24px;
        }
        .card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .card-title {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            margin-top: 0;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .feature-item {
            margin-bottom: 10px;
            font-size: 14px;
            color: #4b5563;
        }
        .feature-icon {
            color: #4f46e5;
            margin-right: 8px;
            font-weight: bold;
        }
        .cta-container {
            text-align: center;
            margin: 32px 0 16px 0;
        }
        .cta-button {
            background-color: #4f46e5;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            border-radius: 6px;
            color: #ffffff !important;
            display: inline-block;
            font-size: 16px;
            font-weight: 600;
            padding: 14px 28px;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
            transition: all 0.2s ease;
        }
        .footer {
            text-align: center;
            padding: 24px 30px 40px 30px;
            font-size: 12px;
            color: #9ca3af;
        }
        .footer a {
            color: #6b7280;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header-gradient">
                <h1 class="header-title">Welcome to Your CRM Dashboard!</h1>
            </div>

            <!-- Content -->
            <div class="content">
                <h2 class="greeting">Hi {{ $user->name }},</h2>
                <p class="intro">We're absolutely thrilled to welcome you aboard!  CRM Dashboard is fully prepared to grow your business.</p>

                <!-- Features Card -->
                <div class="card">
                    <h3 class="card-title">What you can do next:</h3>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span> Track and manage your sales pipeline and key accounts.
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span> Send digital proposals and review customer replies.
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span> Analyze performance reports and daily sales summaries.
                    </div>
                </div>

                <p>To get started, click the button below to sign in and explore your new workspace:</p>

                <!-- CTA Button -->
                <div class="cta-container">
                    <a href="{{ url('/dashboard') }}" class="cta-button">Go to Dashboard</a>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>This is an automated message sent from your CRM application.</p>
                <p>&copy; {{ date('Y') }} Custom CRM App. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
