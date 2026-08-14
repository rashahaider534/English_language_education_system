
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .content {
            padding: 30px;
        }
        .original-message {
            background-color: #f0f0f0;
            border-right: 4px solid #999;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }
        .reply-box {
            background-color: #f0f8ff;
            border-right: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
        }
        .section-title {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ تم الرد على رسالتك</h1>
            <p>فريق الدعم يرد على استفسارك</p>
        </div>

        <div class="content">
            <p>مرحباً {{ $studentName }}</p>

            <p>شكراً لتواصلك معنا. لقد تلقينا رسالتك وقمنا بمراجعتها. إليك رد الفريق:</p>

            <div class="original-message">
                <div class="section-title">📝 رسالتك الأصلية:</div>
                <p>{{ $originalMessage }}</p>
            </div>

            <div class="reply-box">
                <div class="section-title">💬 الرد من فريق الدعم:</div>
                <p>{!! nl2br(e($replyText)) !!}</p>
            </div>

            <p>إذا كان لديك أي استفسارات إضافية، لا تتردد في التواصل معنا مجدداً.</p>

            <p style="margin-top: 20px; color: #666; font-size: 14px;">
                <strong>ملاحظة:</strong> هذا البريد تم إرساله تلقائياً. يرجى عدم الرد على هذا البريد مباشرة.
            </p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.</p>
            <p>
                <a href="{{ url('/') }}" style="color: #667eea; text-decoration: none;">
                    زيارة الموقع
                </a>
            </p>
        </div>
    </div>
</body>
</html>
