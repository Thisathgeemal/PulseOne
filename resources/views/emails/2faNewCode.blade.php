<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PULSEONE</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    <style>
        body {
            background-color: #f3f4f6;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            margin: 0;
        }
        .container {
            max-width: 448px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 32px;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            margin: 0;
        }
        .header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin-top: 8px;
        }
        .content {
            background-color: #fef2f2;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .content p {
            font-size: 16px;
            color: #4b5563;
            margin: 0 0 16px;
        }
        .content .code {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin: 20px 0;
        }
        .content .highlight {
            font-weight: 500;
            color: #16a34a;
        }
        .cta {
            text-align: center;
        }
        .cta p {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 16px;
        }
        .cta a {
            display: inline-block;
            background-color: #dc2626;
            color: #ffffff;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .cta a:hover {
            background-color: #b91c1c;
        }
        .footer {
            margin-top: 32px;
            text-align: center;
        }
        .footer hr {
            margin-bottom: 16px;
            border: 0;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }
        .footer span {
            font-weight: 600;
            color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your New 2FA Verification Code</h1>
            <h2>Hello {{ $user->first_name ?? 'User' }},</h2>
        </div>
        <div class="content">
            <p>Your new two-factor authentication (2FA) verification code is:</p>
            <div class="code">{{ $code }}</div>
            <p>This code will expire in <span class="highlight">3 minutes</span>.</p>
            <p>If you didn't request this, please ignore this email.</p>
        </div>
        <div class="cta">
            <p>Have questions? We're here to help!</p>
            <a href="mailto:pulseone.app@gmail.com">Contact Us</a>
        </div>
        <div class="footer">
            <hr>
            <p>© {{ date('Y') }} <span>PulseOne</span>. All rights reserved.</p>
        </div>
    </div>
</body>
</html>