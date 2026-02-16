<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Newsletter' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #76D7A4 0%, #5bc48a 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #09090b;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            color: #374151;
            margin-top: 8px;
            font-size: 14px;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #09090b;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .content p {
            margin-bottom: 16px;
            font-size: 16px;
            line-height: 1.8;
            color: #4b5563;
        }
        .content a {
            color: #76D7A4;
            text-decoration: none;
            font-weight: 600;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #76D7A4 0%, #5bc48a 100%);
            color: #09090b !important;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 30px 0;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .footer a {
            color: #76D7A4;
            text-decoration: none;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #6b7280;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .header { padding: 30px 20px; }
            .header h1 { font-size: 24px; }
            .content { padding: 30px 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Stay updated with our latest content</p>
        </div>

        <!-- Content -->
        <div class="content">
            {!! $content !!}
        </div>

        <hr class="divider">

        <!-- Footer -->
        <div class="footer">
            <p>You're receiving this because you subscribed to our newsletter.</p>

            <p>
                <a href="{{ $unsubscribeUrl }}">Unsubscribe</a> |
                <a href="{{ config('app.url') }}">Visit Website</a>
            </p>

            <p style="margin-top: 20px; color: #9ca3af;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
