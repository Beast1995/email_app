<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $recipient['name'] ?? 'Valued Customer' }}</title>
    <style>
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
        }
        
        .email-wrapper {
            width: 100%;
            background-color: #f4f4f4;
            padding: 20px 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        
        .email-body {
            padding: 30px 20px;
        }
        
        .email-content {
            line-height: 1.8;
            color: #555555;
        }
        
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .unsubscribe-link {
            color: #6c757d;
            text-decoration: none;
            font-size: 12px;
        }
        
        .unsubscribe-link:hover {
            text-decoration: underline;
        }
        
        .company-info {
            margin-top: 15px;
            font-size: 12px;
            color: #6c757d;
        }
        
        /* Responsive design */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0 10px;
            }
            
            .email-header,
            .email-body,
            .email-footer {
                padding: 20px 15px;
            }
            
            .email-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <h1>{{ config('app.name', 'Bulk Email App') }}</h1>
            </div>
            
            <!-- Body -->
            <div class="email-body">
                <div class="email-content">
                    {!! $content !!}
                </div>
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                <p>
                    <a href="{{ $unsubscribeUrl }}" class="unsubscribe-link">
                        Unsubscribe from this mailing list
                    </a>
                </p>
                <div class="company-info">
                    <p>
                        This email was sent to {{ $recipient['email'] }}<br>
                        If you have any questions, please contact us at {{ config('mail.from.address') }}
                    </p>
                    <p>
                        &copy; {{ date('Y') }} {{ config('app.name', 'Bulk Email App') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 