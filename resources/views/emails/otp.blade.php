<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f5f7; margin: 0; padding: 0; }
        .wrap { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: #0f172a; padding: 32px; text-align: center; }
        .brand { font-size: 22px; font-weight: 900; color: #fff; letter-spacing: -.02em; }
        .brand span { color: #f59e0b; }
        .body { padding: 36px 40px; }
        h2 { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 12px; }
        p { font-size: 14px; color: #64748b; line-height: 1.6; margin: 0 0 20px; }
        .otp-box { background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 24px; text-align: center; margin: 24px 0; }
        .otp-code { font-size: 42px; font-weight: 900; color: #0f172a; letter-spacing: 12px; font-family: monospace; }
        .otp-sub { font-size: 12px; color: #94a3b8; margin-top: 8px; }
        .warning { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #92400e; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <div class="brand">Robo<span>Tech</span> Academy</div>
        </div>
        <div class="body">
            <h2>{{ app()->getLocale()=='km' ? 'លេខកូដ OTP របស់អ្នក' : 'Your OTP Verification Code' }}</h2>
            <p>
                {{ app()->getLocale()=='km'
                    ? 'សួស្តី ' . ($name ?: 'អ្នកប្រើប្រាស់') . '! ប្រើលេខកូដខាងក្រោមដើម្បីបញ្ជាក់គណនីរបស់អ្នក:'
                    : 'Hello ' . ($name ?: 'there') . '! Use the code below to verify your account:' }}
            </p>
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-sub">
                    {{ app()->getLocale()=='km' ? 'សុពលភាព 10 នាទី' : 'Valid for 10 minutes' }}
                </div>
            </div>
            <div class="warning">
                ⚠️ {{ app()->getLocale()=='km'
                    ? 'កុំចែករំលែកលេខកូដនេះជាមួយអ្នកណាឡើយ!'
                    : 'Never share this code with anyone!' }}
            </div>
            <p style="margin-top:20px;font-size:13px;">
                {{ app()->getLocale()=='km'
                    ? 'បើអ្នកមិនបានស្នើសុំ — មិនចាំបាច់ធ្វើអ្វីទេ។'
                    : "If you didn't request this code, please ignore this email." }}
            </p>
        </div>
        <div class="footer">
            © {{ date('Y') }} RoboTech Academy. All rights reserved.
        </div>
    </div>
</body>
</html>