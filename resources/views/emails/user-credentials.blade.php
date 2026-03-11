<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account Credentials</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .credentials-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .credential-item {
            margin: 10px 0;
        }
        .credential-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 100px;
        }
        .credential-value {
            color: #333;
            font-family: 'Courier New', monospace;
            background: #fff;
            padding: 5px 10px;
            border-radius: 3px;
            display: inline-block;
        }
        .info-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Welcome to {{ config('app.name') }}!</h1>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            
            <p>Your account has been successfully created. Below are your login credentials:</p>
            
            <div class="credentials-box">
                <div class="credential-item">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Password:</span>
                    <span class="credential-value">{{ $plainPassword }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Role:</span>
                    <span class="credential-value">{{ $user->role }}</span>
                </div>
            </div>
            
            <div class="info-box">
                <strong>⚠️ Important Security Notice:</strong>
                <p style="margin: 10px 0 0 0;">Please change your password after your first login for security purposes. Keep your credentials safe and do not share them with anyone.</p>
            </div>
            
            <center>
                <a href="{{ url('/login') }}" class="btn">Login to Your Account</a>
            </center>
            
            <p style="margin-top: 30px;">If you have any questions or need assistance, please don't hesitate to contact us.</p>
            
            <p style="margin-top: 20px;">
                Best regards,<br>
                <strong>{{ config('app.name') }} Team</strong>
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
