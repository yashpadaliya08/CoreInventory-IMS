<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { 
            font-family: 'Helvetica Neue', Arial, sans-serif; 
            background-color: #f1f5f9; 
            margin: 0; 
            padding: 40px 0; 
        }
        .container { 
            max-width: 480px; 
            margin: 0 auto; 
            background: #ffffff; 
            border-radius: 12px; 
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header { 
            background: linear-gradient(135deg, #4f46e5, #6366f1); 
            padding: 30px; 
            text-align: center; 
        }
        .header h1 { 
            color: #ffffff; 
            font-size: 22px; 
            margin: 0; 
            font-weight: 700; 
            letter-spacing: -0.3px;
        }
        .header p { 
            color: rgba(255,255,255,0.8); 
            font-size: 13px; 
            margin: 6px 0 0;
        }
        .body { 
            padding: 35px 30px; 
        }
        .greeting { 
            font-size: 16px; 
            color: #1e293b; 
            font-weight: 600; 
            margin-bottom: 16px; 
        }
        .text { 
            font-size: 14px; 
            color: #64748b; 
            line-height: 1.6; 
            margin-bottom: 25px; 
        }
        .otp-box { 
            background: #f8fafc; 
            border: 2px dashed #e2e8f0; 
            border-radius: 10px; 
            text-align: center; 
            padding: 25px; 
            margin-bottom: 25px; 
        }
        .otp-label { 
            font-size: 11px; 
            color: #94a3b8; 
            text-transform: uppercase; 
            letter-spacing: 1.5px; 
            font-weight: 700; 
            margin-bottom: 10px; 
        }
        .otp-code { 
            font-size: 36px; 
            font-weight: 800; 
            color: #4f46e5; 
            letter-spacing: 8px; 
            font-family: 'Courier New', monospace; 
        }
        .warning { 
            background: #fef3c7; 
            border-left: 4px solid #f59e0b; 
            padding: 12px 16px; 
            border-radius: 0 6px 6px 0; 
            font-size: 12px; 
            color: #92400e; 
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .footer { 
            background: #f8fafc; 
            padding: 20px 30px; 
            text-align: center; 
            border-top: 1px solid #f1f5f9; 
        }
        .footer p { 
            font-size: 11px; 
            color: #94a3b8; 
            margin: 0; 
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CoreInventory IMS</h1>
            <p>Password Reset Verification</p>
        </div>
        <div class="body">
            <div class="greeting">Hello, {{ $userName }}!</div>
            <div class="text">
                We received a request to reset the password for your CoreInventory account. 
                Please use the verification code below to complete the process.
            </div>
            <div class="otp-box">
                <div class="otp-label">Your Verification Code</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>
            <div class="warning">
                ⚠️ This code will expire in <strong>10 minutes</strong>. 
                If you did not request a password reset, please ignore this email — your account is safe.
            </div>
            <div class="text" style="margin-bottom: 0; font-size: 13px;">
                For security reasons, never share this code with anyone. 
                CoreInventory staff will never ask for your verification code.
            </div>
        </div>
        <div class="footer">
            <p>This is an automated message from CoreInventory IMS.<br>
            Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
