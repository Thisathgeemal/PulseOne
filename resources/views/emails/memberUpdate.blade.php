<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PulseOne</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Instrument Sans', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            margin: 0;
        }
        .container {
            max-width: 480px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #dc2626;
            margin: 0;
        }
        .header h2 {
            font-size: 22px;
            font-weight: 600;
            color: #1f2937;
            margin-top: 12px;
        }
        .content {
            background-color: #fef2f2;
            border-radius: 8px;
            padding: 28px;
            margin-bottom: 28px;
        }
        .content p {
            font-size: 16px;
            color: #4b5563;
            margin: 0 0 20px;
            line-height: 1.6;
        }
        .content strong {
            color: #dc2626;
        }
        .highlight {
            font-weight: 500;
            color: #16a34a;
        }
        .credentials {
            background-color: #f9fafb;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px dashed #d1d5db;
        }
        .credentials p {
            margin: 0;
            font-size: 15px;
            color: #1f2937;
        }
        .credentials .label {
            font-weight: 600;
            color: #dc2626;
        }
        .cta {
            text-align: center;
        }
        .cta p {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 20px;
        }
        .cta a {
            display: inline-block;
            background-color: #dc2626;
            color: #ffffff;
            font-weight: 500;
            padding: 10px 24px;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .cta a:hover {
            background-color: #b91c1c;
            transform: translateY(-2px);
        }
        .footer {
            margin-top: 32px;
            text-align: center;
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
            <h1>PulseOne Account Updated</h1>
            <h2>Hi {{ $user->first_name }} {{ $user->last_name }},</h2>
        </div>
        <div class="content">
            <p>Your account details have been successfully updated in <strong>PulseOne</strong>.</p>
            <div class="credentials">
                <p><span class="label">Email:</span> {{ $user->email }}</p>
                <p><span class="label">Password:</span> {{ $defaultPassword }}</p>
                <p><span class="label">Mobile Number:</span> {{ $user->mobile_number }}</p>
            </div>
            <p>Please <span class="highlight">change your password</span> after your first login to ensure your account security.</p>
        </div>
        <div class="cta">
            <p>Access your account using the link below:</p>
            <a href="{{ $url }}">Log In to PulseOne</a>
        </div>
        <div class="footer">
            <p>Best regards,<br><span>PulseOne Team</span></p>
        </div>
    </div>
</body>
</html>