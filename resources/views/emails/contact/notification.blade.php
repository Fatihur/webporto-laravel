<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>
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
            font-size: 24px;
            font-weight: 800;
        }
        .content {
            padding: 40px 30px;
        }
        .panel {
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .panel strong {
            color: #09090b;
            display: inline-block;
            width: 80px;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 New Contact Message</h1>
        </div>

        <div class="content">
            <p>You have received a new contact message from your website.</p>

            <div class="panel">
                <p><strong>Name:</strong> {{ $contact->name }}</p>
                <p><strong>Email:</strong> {{ $contact->email }}</p>
                <p><strong>Subject:</strong> {{ $contact->subject }}</p>
                <p><strong>Date:</strong> {{ $contact->created_at->format('F j, Y g:i A') }}</p>
            </div>

            <div class="panel">
                <strong>Message:</strong><br><br>
                {{ $contact->message }}
            </div>

            <div style="text-align: center;">
                <a href="{{ $adminUrl }}" class="button">View in Admin Panel</a>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated message from {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
