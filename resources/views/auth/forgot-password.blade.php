<!DOCTYPE html>
<html
    x-data="{
        lang: 'ar',
        copy: {
            ar: {
                title: 'نسيت كلمة المرور؟',
                subtitle: 'أدخل بريدك الإلكتروني أدناه وسنرسل لك رابطاً لإعادة تعيين كلمة المرور.',
                email: 'البريد الإلكتروني',
                submit: 'إرسال رابط الاستعادة',
                back: 'العودة لتسجيل الدخول',
                toggle: 'EN',
            },
            en: {
                title: 'Forgot Password?',
                subtitle: 'Enter your email below and we will send you a password reset link.',
                email: 'Email',
                submit: 'Send Reset Link',
                back: '← Back to Login',
                toggle: 'AR',
            },
        },
    }"
    :lang="lang"
    :dir="lang === 'ar' ? 'rtl' : 'ltr'"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fluent — {{ __('Forgot Password') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        @keyframes fluent-float-a {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(-16px,14px) scale(1.06); }
        }
        @keyframes fluent-float-b {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(18px,-10px) scale(1.04); }
        }
        @keyframes fluent-twinkle {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 0.75; }
        }
        .fluent-blob-a { animation: fluent-float-a 14s ease-in-out infinite; }
        .fluent-blob-b { animation: fluent-float-b 16s ease-in-out infinite; }
        .fluent-blob-c { animation: fluent-float-a 11s ease-in-out infinite; }
        .fluent-blob-d { animation: fluent-float-b 13s ease-in-out infinite; }
        .fluent-star {
            position: absolute;
            border-radius: 9999px;
            background: rgba(255,255,255,0.5);
            animation: fluent-twinkle 4s ease-in-out infinite;
        }

        /* إعدادات الحقل الأساسية */
        .fluent-input { 
            background: rgba(255, 255, 255, 0.05); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
        }

        /* تأثير "الإضاءة" (Glow) عند الضغط داخل الحقل */
        .fluent-input:focus-within { 
            background: rgba(255, 255, 255, 0.08); 
            border-color: rgba(168, 232, 249, 0.5); 
            box-shadow: 0 0 15px rgba(168, 232, 249, 0.25); 
        }

        /* منع اللون الأزرق التلقائي للمتصفح وتوحيد اللون مع الكارد */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff !important;
            -webkit-box-shadow: 0 0 0px 1000px #012A3F inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* منع أي إطار (Outline) افتراضي من المتصفح */
        input:focus {
            outline: none !important;
        }
    </style>
</head>
<body class="antialiased text-white min-h-screen" :class="lang === 'ar' ? 'font-tajawal' : 'font-poppins'">

    <div class="relative min-h-screen flex items-center justify-center overflow-hidden p-4"
         style="background: linear-gradient(155deg,#012A3F 0%, #013C58 32%, #00537A 62%, #012E46 100%);">

        <!-- ambient background -->
        <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden">
            <div class="fluent-blob-a absolute -left-44 -top-52 w-[680px] h-[680px] rounded-full blur-2xl bg-fluent-orange/20"></div>
            <div class="fluent-blob-b absolute -right-36 -bottom-44 w-[600px] h-[600px] rounded-full blur-2xl bg-fluent-sky/20"></div>
            <div class="fluent-blob-c absolute right-[10%] top-[4%] w-[460px] h-[460px] rounded-full blur-2xl bg-fluent-yellow/15"></div>
            <div class="fluent-blob-d absolute left-[8%] -bottom-[6%] w-[380px] h-[380px] rounded-full blur-2xl bg-fluent-lightOrange/15"></div>
            @for ($i = 0; $i < 18; $i++)
                <div class="fluent-star"
                    style="
                        top: {{ rand(0, 100) }}%;
                        left: {{ rand(0, 100) }}%;
                        width: {{ rand(8, 18) / 10 }}px;
                        height: {{ rand(8, 18) / 10 }}px;
                        animation-duration: {{ rand(30, 70) / 10 }}s;
                        animation-delay: {{ rand(0, 40) / 10 }}s;
                    "
                ></div>
            @endfor
        </div>

        <div class="relative z-10 w-full max-w-[420px] bg-white/[0.04] backdrop-blur-xl border border-white/[0.08] rounded-[32px] p-8 sm:p-10 shadow-[0_20px_60px_rgba(0,0,0,0.5)]">
            
            <div class="absolute top-6" :class="lang === 'ar' ? 'left-6' : 'right-6'">
                <button type="button" @click="lang = lang === 'ar' ? 'en' : 'ar'"
                    class="font-poppins text-[11px] font-semibold tracking-wider text-white/50 hover:text-white border border-white/10 hover:bg-white/5 rounded-full px-3 py-1.5 transition">
                    <span x-text="copy[lang].toggle"></span>
                </button>
            </div>

            <div class="flex flex-col items-center text-center mt-4 mb-8">
                <span class="font-poppins font-extrabold text-[36px] tracking-tight mb-2">
                    <span class="text-[#A8E8F9]">Flu</span><span class="text-[#FFD35B]">ent</span>
                </span>
                <h1 class="font-poppins font-bold text-[20px] text-white/90 tracking-wide mt-2" x-text="copy[lang].title"></h1>
                <p class="mt-2 text-[13px] text-white/40 leading-relaxed max-w-[320px]" x-text="copy[lang].subtitle"></p>
            </div>

            <form method="POST" action="{{ route('password.email') }}" novalidate>
                @csrf

                <div class="mb-6">
                    <label class="block text-[12px] font-medium text-white/70 mb-2 ml-1" x-text="copy[lang].email"></label>
                    <div class="fluent-input flex items-center gap-3 rounded-2xl px-4 py-3.5">
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="flex-1 bg-transparent border-none outline-none focus:ring-0 text-white text-[14px]"
                            placeholder="name@fluent.com">
                        <span class="text-[#A8E8F9]/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </span>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-2xl bg-gradient-to-r from-[#A8E8F9] via-[#FCECB0] to-[#FFD35B] text-[#012A3F] font-poppins font-bold text-[14.5px] shadow-[0_10px_20px_rgba(255,211,91,0.2)] hover:shadow-[0_15px_25px_rgba(255,211,91,0.35)] transition-all">
                    <span x-text="copy[lang].submit"></span>
                </button>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="font-poppins text-[13px] text-white/40 hover:text-white transition">
                        <span x-text="copy[lang].back"></span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>