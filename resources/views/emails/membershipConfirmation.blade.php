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
        .content strong {
            color: #dc2626;
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
            <h1>Welcome to PulseOne!</h1>
            <h2>Hi {{ $user->first_name }} {{ $user->last_name }},</h2>
        </div>
        <div class="content">
            <p>
                Thank you for purchasing the 
                <strong>{{ $membershipType->type_name }}</strong> 
                membership for <strong>Rs.{{ $membershipType->price }}</strong>!
            </p>
            <p>
                Your membership is active from today until 
                <span class="highlight">{{ now()->addDays($membershipType->duration)->toFormattedDateString() }}</span>.
            </p>
        </div>
        <div class="cta">
            <p>Have questions? We're here to help!</p>
            <a href="mailto:support@pulseone.com">Contact Us</a>
        </div>
        <div class="footer">
            <p>Best regards,<br><span>PulseOne Team</span></p>
        </div>
    </div>
</body>
</html>