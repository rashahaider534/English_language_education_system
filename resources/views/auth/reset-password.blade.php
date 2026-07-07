<!DOCTYPE html>
<html
    x-data="{
        lang: 'ar',
        copy: {
            ar: {
                title: 'إنشاء كلمة مرور جديدة',
                subtitle: 'أنت على وشك الانتهاء! أدخل كلمة المرور الجديدة لحسابك.',
                email: 'البريد الإلكتروني المسترد',
                password: 'كلمة المرور الجديدة',
                confirm_password: 'تأكيد كلمة المرور',
                submit: 'حفظ وتغيير كلمة المرور',
                toggle: 'EN',
            },
            en: {
                title: 'Create New Password',
                subtitle: 'You are almost there! Enter a new password for your account.',
                email: 'Recovered Email',
                password: 'New Password',
                confirm_password: 'Confirm Password',
                submit: 'Reset Password',
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
    <title>Fluent — {{ __('Reset Password') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        @keyframes fluent-fade-up { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        .fluent-card { animation: fluent-fade-up 0.8s cubic-bezier(0.16,1,0.3,1) both; }

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

        /* إزالة أي إطار عند التركيز */
        input:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        .fluent-input { 
            background: rgba(0, 0, 0, 0.2); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            transition: all 0.3s ease;
        }

        .fluent-input:focus-within { 
            border-color: rgba(168,232,249,0.4); 
            background: rgba(255,255,255,0.08); 
        }

        /* حل مشكلة لون الـ Autofill ليطابق لون الكارد */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff !important;
            -webkit-box-shadow: 0 0 0px 1000px #012A3F inset !important;
            transition: background-color 5000s ease-in-out 0s;
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

        <div class="fluent-card relative z-10 w-full max-w-[480px] bg-white/[0.03] backdrop-blur-xl border border-white/[0.08] rounded-[32px] p-8 sm:p-10 shadow-[0_20px_60px_rgba(0,0,0,0.5)]">
            
            <div class="absolute top-6" :class="lang === 'ar' ? 'left-6' : 'right-6'">
                <button type="button" @click="lang = lang === 'ar' ? 'en' : 'ar'"
                    class="font-poppins text-[11px] font-semibold tracking-wider text-white/50 hover:text-white border border-white/10 hover:bg-white/5 rounded-full px-3 py-1.5 transition">
                    <span x-text="copy[lang].toggle"></span>
                </button>
            </div>

            <div class="flex flex-col items-center text-center mt-4 mb-10">
                <span class="font-poppins font-extrabold text-[36px] tracking-tight">
                    <span class="text-[#A8E8F9]">Flu</span><span class="text-[#FFD35B]">ent</span>
                </span>
                <h1 class="font-poppins font-bold text-[20px] text-white/90 tracking-wide mt-2" x-text="copy[lang].title"></h1>
                <p class="mt-2 text-[13px] text-white/40 leading-relaxed max-w-[320px]" x-text="copy[lang].subtitle"></p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-5">
                    <label class="block text-[12px] font-medium text-white/70 mb-2 ml-1" x-text="copy[lang].email"></label>
                    <div class="flex items-center gap-3 bg-white/5 border border-white/10 rounded-2xl px-4 py-3.5 opacity-70">
                        <input type="email" name="email" value="{{ old('email', $request->email) }}" readonly
                            class="w-full bg-transparent border-none outline-none p-0 text-white/60 text-[14px]">
                    </div>
                </div>

                <div x-data="{ show: false }" class="mb-5">
                    <label class="block text-[12px] font-medium text-white/70 mb-2 ml-1" x-text="copy[lang].password"></label>
                    <div class="fluent-input flex items-center gap-3 rounded-2xl px-4 transition">
                        <input :type="show ? 'text' : 'password'" name="password" required
                            class="flex-1 bg-transparent border-none outline-none py-3.5 text-white placeholder-white/20 text-[14px]">
                        <button type="button" @click="show = !show" class="text-[#A8E8F9]/60 hover:text-[#A8E8F9] transition-colors">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="12" r="3" />
                                <path d="M12 4C7 4 2.73 7.11 1 12c1.73 4.89 6 8 11 8s9.27-3.11 11-8c-1.73-4.89-6-8-11-8zm0 13c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-center text-xs" />
                </div>

                <div x-data="{ show: false }" class="mb-6">
                    <label class="block text-[12px] font-medium text-white/70 mb-2 ml-1" x-text="copy[lang].confirm_password"></label>
                    <div class="fluent-input flex items-center gap-3 rounded-2xl px-4 transition">
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                            class="flex-1 bg-transparent border-none outline-none py-3.5 text-white placeholder-white/20 text-[14px]">
                        <button type="button" @click="show = !show" class="text-[#A8E8F9]/60 hover:text-[#A8E8F9] transition-colors">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="12" r="3" />
                                <path d="M12 4C7 4 2.73 7.11 1 12c1.73 4.89 6 8 11 8s9.27-3.11 11-8c-1.73-4.89-6-8-11-8zm0 13c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-[#A8E8F9] via-[#FCECB0] to-[#FFD35B] text-[#012A3F] font-poppins font-bold text-[14.5px] shadow-[0_10px_20px_rgba(255,211,91,0.2)] hover:shadow-[0_15px_25px_rgba(255,211,91,0.35)] transition-all">
                    <span x-text="copy[lang].submit"></span>
                </button>
            </form>
        </div>
    </div>
</body>
</html>