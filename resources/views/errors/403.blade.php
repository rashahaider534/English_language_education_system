<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لا تملك الصلاحية الكافية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: #DFF2F9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            width: 100%;
            max-width: 460px;
            background: #EFFAFD;
            border: 1.5px solid rgba(0,83,122,0.16);
            border-radius: 26px;
            padding: 44px 36px;
            text-align: center;
            box-shadow: 0 24px 55px rgba(1,60,88,0.14);
        }
        .icon {
            width: 74px;
            height: 74px;
            border-radius: 20px;
            background: linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%);
            color: #FFD35B;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 22px;
            box-shadow: 0 14px 30px rgba(1,60,88,0.25);
        }
        h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 20px;
            color: #013C58;
            margin-bottom: 10px;
        }
        p {
            font-size: 13.5px;
            color: rgba(1,60,88,0.6);
            line-height: 1.8;
            margin-bottom: 28px;
        }
        a.btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 26px;
            border-radius: 12px;
            background: linear-gradient(90deg,#F5A201,#FFBA42);
            color: #013C58;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 13.5px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        </div>
        <h1>ما عندك صلاحية كافية</h1>
        <p>هالصفحة محتاجة صلاحية إضافية مش موجودة بحسابك حاليًا. إذا بتحسّي إنو هاد غلط، تواصلي مع مسؤول النظام لإضافة الصلاحية المطلوبة لحسابك.</p>
        <a href="{{ url('/dashboard') }}" class="btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            رجوع للوحة التحكم
        </a>
    </div>
</body>
</html>
