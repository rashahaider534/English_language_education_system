<!DOCTYPE html>
<html
    x-data="{
        lang: 'ar',
        copy: {
            ar: {
                title: 'أهلاً بك مجدداً',
                subtitle: 'سررنا برؤيتك ! سجّل الدخول وكمّل رحلتك معنا',
                email: 'البريد الإلكتروني',
                password: 'كلمة المرور',
                forgot: 'نسيت كلمة المرور؟',
                submit: 'تسجيل الدخول',
                tagline: 'منصّة Fluent لإدارة تعلّم اللغة الإنجليزية',
                reviewer: 'مدقق',
                owner: 'صاحب معهد',
                toggle: 'EN',
            },
            en: {
                title: 'Welcome Back',
                subtitle: 'Great to see you again — sign in to continue',
                email: 'Email',
                password: 'Password',
                forgot: 'Forgot password?',
                submit: 'Log In',
                tagline: 'Fluent platform for managing English language learning',
                reviewer: 'Reviewer',
                owner: 'Institute Owner',
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
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Fluent — {{ __('Log in') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
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
            @keyframes fluent-fade-up {
                from { opacity: 0; transform: translateY(18px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes fluent-logo-bob {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-7px); }
            }
            @keyframes fluent-card-bob {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-14px); }
            }
            @keyframes fluent-spin {
                to { transform: rotate(360deg); }
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
            .fluent-mini-star {
                position: absolute;
                border-radius: 9999px;
                background: rgba(255,255,255,0.6);
                animation: fluent-twinkle 3.6s ease-in-out infinite;
            }
            .fluent-card { animation: fluent-fade-up 0.7s cubic-bezier(0.16,1,0.3,1) both; }
            .fluent-brand-wrap { animation: fluent-fade-up 0.8s 0.15s cubic-bezier(0.16,1,0.3,1) both; }
            .fluent-brand-card { animation: fluent-card-bob 6s ease-in-out infinite; }
            .fluent-logo-stage { animation: fluent-logo-bob 5s ease-in-out infinite; }
            .fluent-spinner {
                display: inline-block; width: 15px; height: 15px; border-radius: 9999px;
                border: 2px solid rgba(1,60,88,0.35); border-top-color: #013C58;
                animation: fluent-spin 0.7s linear infinite;
            }

            .fluent-input-icon { transition: color 0.2s; }
            .fluent-input:focus-within .fluent-input-icon { color: #FFBA42; }
            .fluent-input:focus-within { border-color: rgba(255,186,66,0.55); background: rgba(255,255,255,0.08); }
            .fluent-input, .fluent-input * { outline: none !important; box-shadow: none !important; }

            .fluent-input input:-webkit-autofill,
            .fluent-input input:-webkit-autofill:hover,
            .fluent-input input:-webkit-autofill:focus {
                -webkit-text-fill-color: #ffffff;
                caret-color: #ffffff;
                -webkit-box-shadow: 0 0 0px 1000px rgba(0, 83, 122, 0.35) inset;
                box-shadow: 0 0 0px 1000px rgba(0, 83, 122, 0.35) inset;
                transition: background-color 9999s ease-in-out 0s;
            }

            input[type="password"]::-ms-reveal,
            input[type="password"]::-ms-clear {
                display: none;
            }
        </style>
    </head>
    <body
        class="antialiased text-white min-h-screen"
        :class="lang === 'ar' ? 'font-tajawal' : 'font-poppins'"
    >
        <div class="relative min-h-screen flex items-center justify-center overflow-hidden p-4 sm:p-10"
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

            <div class="relative z-10 w-full max-w-4xl flex flex-wrap items-start gap-[14px]">

                <!-- Login card -->
                <div class="fluent-card relative flex-1 basis-[440px] min-h-[660px] bg-fluent-dark/40 backdrop-blur-xl border border-fluent-sky/[0.16] rounded-[32px] p-10 overflow-hidden shadow-[0_40px_80px_rgba(0,0,0,0.4),0_0_70px_rgba(245,162,1,0.05)] flex flex-col justify-start">

                    <div class="pointer-events-none absolute top-0 left-6 right-6 h-px bg-gradient-to-r from-transparent via-fluent-yellow/60 to-transparent"></div>

                    <div class="grid grid-cols-3 items-center mb-[30px]">
                        <div></div>
                        <div class="flex items-center justify-center">
                            <span class="font-poppins font-extrabold text-3xl tracking-wide"
                                style="background: linear-gradient(90deg,#A8E8F9 0%, #A8E8F9 32%, #FFD35B 55%, #FFBA42 100%); -webkit-background-clip: text; background-clip: text; color: transparent;">Fluent</span>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="lang = lang === 'ar' ? 'en' : 'ar'"
                                class="font-poppins text-xs font-bold tracking-wide text-fluent-sky bg-fluent-sky/[0.06] border border-fluent-sky/20 hover:bg-fluent-sky/[0.14] hover:border-fluent-sky/40 rounded-full px-4 py-1.5 transition"
                                x-text="copy[lang].toggle">
                            </button>
                        </div>
                    </div>

                    <h1 class="font-poppins font-extrabold text-[27px] text-white" x-text="copy[lang].title"></h1>
                    <p class="mt-2.5 text-sm text-fluent-sky/70 leading-relaxed" x-text="copy[lang].subtitle"></p>

                    <!-- Session Status -->
                    <x-auth-session-status class="mt-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="mt-[30px] space-y-5" novalidate
                        x-data="{ submitting: false }"
                        @submit="submitting = true">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-semibold tracking-wide text-fluent-sky/90 mb-2" x-text="copy[lang].email"></label>
                            <div class="fluent-input relative flex items-center gap-2.5 bg-white/5 border border-fluent-sky/[0.16] rounded-xl px-3.5 transition">
                                <span class="fluent-input-icon text-fluent-sky/55 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.94 6.94a2 2 0 0 1 2-2h10.12a2 2 0 0 1 2 2v6.12a2 2 0 0 1-2 2H4.94a2 2 0 0 1-2-2V6.94Zm1.86.4v5.72l4.63-3.16a1 1 0 0 1 1.14 0l4.63 3.16V7.34l-5.2 3.55-5.2-3.55Z"/>
                                    </svg>
                                </span>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                    class="flex-1 min-w-0 bg-transparent border-none outline-none py-3 text-white placeholder-white/35 text-[14.5px]"
                                    placeholder="name@fluent.com">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div x-data="{ show: false }">
                            <label for="password" class="block text-xs font-semibold tracking-wide text-fluent-sky/90 mb-2" x-text="copy[lang].password"></label>
                            <div class="fluent-input relative flex items-center gap-2.5 bg-white/5 border border-fluent-sky/[0.16] rounded-xl px-3.5 transition">
                                <span class="fluent-input-icon text-fluent-sky/55 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 1a4 4 0 0 0-4 4v2H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-1V5a4 4 0 0 0-4-4Zm2 6V5a2 2 0 1 0-4 0v2h4Z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                                    class="flex-1 min-w-0 bg-transparent border-none outline-none py-3 text-white placeholder-white/35 text-[14.5px]"
                                    placeholder="••••••••">
                                <button type="button" @click="show = !show" class="flex items-center text-fluent-sky/55 flex-shrink-0 p-1.5">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-[17px] w-[17px]" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3.5c-4.14 0-7.5 3.5-7.5 6.5s3.36 6.5 7.5 6.5 7.5-3.5 7.5-6.5S14.14 3.5 10 3.5Zm0 10.5a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/>
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-[17px] w-[17px]" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M3.28 2.22a.75.75 0 0 0-1.06 1.06l14.5 14.5a.75.75 0 1 0 1.06-1.06l-1.86-1.86c1.4-1.07 2.42-2.53 2.94-3.86-1.02-2.6-3.87-6-9.86-6-1.35 0-2.55.27-3.6.72L3.28 2.22ZM10 6.5c.62 0 1.2.13 1.73.35l-4.88 4.88A4 4 0 0 1 10 6.5Zm-7.5 3.5c.5 1.28 1.48 2.7 2.83 3.76l1.2-1.2A4 4 0 0 1 6.4 8.4l-1.4-1.4A9.9 9.9 0 0 0 2.5 10Z"/>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex justify-start">
                            @if (Route::has('password.request'))
                                <a class="font-poppins text-[13px] font-medium text-fluent-sky/75 hover:text-fluent-sky transition" href="{{ route('password.request') }}" x-text="copy[lang].forgot">
                                </a>
                            @endif
                        </div>

                        <button type="submit" :disabled="submitting"
                            class="w-full mt-2 py-[15px] rounded-xl bg-gradient-to-r from-fluent-sky to-fluent-yellow font-poppins font-bold text-[15.5px] text-fluent-dark shadow-[0_14px_32px_rgba(168,232,249,0.25)] hover:-translate-y-px hover:shadow-[0_18px_38px_rgba(255,186,66,0.35)] transition flex items-center justify-center gap-2"
                            :class="submitting ? 'opacity-85 cursor-default' : 'cursor-pointer'">
                            <span x-show="submitting" class="fluent-spinner"></span>
                            <span x-text="submitting ? (lang === 'ar' ? 'جاري الدخول...' : 'Signing in...') : copy[lang].submit"></span>
                        </button>
                    </form>
                </div>

                <!-- Brand card -->
                <div class="fluent-brand-wrap flex-1 basis-[360px] min-w-[300px] max-w-[360px] flex items-center justify-center">
                    <div class="fluent-brand-card relative w-full min-h-[660px] flex flex-col items-center justify-center text-center bg-fluent-dark/40 backdrop-blur-xl backdrop-saturate-[1.15] border border-white/[0.14] rounded-[32px] px-[30px] pt-[76px] pb-[50px] overflow-hidden shadow-[0_30px_70px_rgba(0,0,0,0.35)]">

                        <!-- soft glow behind logo -->
                        <div class="pointer-events-none absolute w-[340px] h-[340px] left-1/2 top-[36%] -translate-x-1/2 -translate-y-1/2 rounded-full blur-[10px]"
                             style="background: radial-gradient(circle, rgba(168,232,249,0.16) 0%, rgba(255,211,91,0.08) 45%, rgba(255,211,91,0) 72%);"></div>

                        <!-- grain texture -->
                        <div class="pointer-events-none absolute inset-0 opacity-[0.05] mix-blend-overlay"
                             style="background-image:url(&quot;data:image/svg+xml;utf8,&lt;svg xmlns='http://www.w3.org/2000/svg'&gt;&lt;filter id='n'&gt;&lt;feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/&gt;&lt;/filter&gt;&lt;rect width='100%25' height='100%25' filter='url(%23n)'/&gt;&lt;/svg&gt;&quot;);"></div>

                        <!-- diagonal shine -->
                        <div class="pointer-events-none absolute -top-[55%] -left-[45%] w-[150%] h-[120%] -rotate-[8deg]"
                             style="background: linear-gradient(120deg, rgba(255,255,255,0.22) 0%, rgba(255,255,255,0.06) 20%, rgba(255,255,255,0) 42%);"></div>

                        <!-- edge highlight -->
                        <div class="pointer-events-none absolute inset-0 rounded-[32px]"
                             style="box-shadow: inset 0 0 0 1px rgba(255,255,255,0.1), inset 0 -30px 50px -30px rgba(0,0,0,0.35);"></div>

                        <div class="fluent-mini-star" style="top:16%; right:20%; width:3px; height:3px;"></div>
                        <div class="fluent-mini-star" style="top:28%; left:14%; width:2px; height:2px; animation-delay:1s;"></div>
                        <div class="fluent-mini-star" style="top:8%; left:24%; width:2px; height:2px; animation-delay:0.5s;"></div>

                        <!-- logo: transparent PNG, glowing, no white box -->
                        <div class="fluent-logo-stage relative w-[190px] h-[190px] flex items-center justify-center z-[1]">
                            <div class="absolute w-[300px] h-[300px] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 rounded-full blur-[16px]"
                                 style="background: radial-gradient(circle, rgba(168,232,249,0.30) 0%, rgba(168,232,249,0) 65%);"></div>
                            <div class="absolute w-[220px] h-[220px] left-1/2 top-[52%] -translate-x-1/2 -translate-y-1/2 rounded-full blur-[12px]"
                                 style="background: radial-gradient(circle, rgba(255,211,91,0.32) 0%, rgba(255,211,91,0) 65%);"></div>
                            <img src="{{ asset('images/logo-transparent.png') }}" alt="Fluent"
                                 class="relative z-[1] w-full h-full object-contain"
                                 style="filter: drop-shadow(0 0 16px rgba(168,232,249,0.4)) drop-shadow(0 0 32px rgba(255,211,91,0.32));">
                        </div>

                        <h2 class="relative z-[1] mt-6 font-poppins font-extrabold text-[34px] tracking-wide"
                            style="background: linear-gradient(90deg,#A8E8F9 0%, #A8E8F9 32%, #FFD35B 55%, #FFBA42 100%); -webkit-background-clip: text; background-clip: text; color: transparent;">Fluent</h2>
                        <p class="relative z-[1] mt-3.5 text-white/60 text-[13px] leading-[1.75] max-w-[230px]" x-text="copy[lang].tagline"></p>

                        <div class="relative z-[1] w-12 h-px bg-gradient-to-r from-transparent via-fluent-sky/35 to-transparent my-8"></div>

                        <div class="relative z-[1] flex flex-wrap justify-center gap-2">
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-white/90 text-[11.5px] font-semibold tracking-wide bg-white/5 border border-fluent-sky/[0.18] rounded-full px-[13px] py-2">
                                <svg class="w-[15px] h-[15px] text-fluent-yellow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 9.5L12 5 2 9.5l10 4.5 10-4.5z"></path>
                                    <path d="M6 11.5V16c0 1.5 2.7 3 6 3s6-1.5 6-3v-4.5"></path>
                                </svg>
                                <span x-text="copy[lang].reviewer"></span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-white/90 text-[11.5px] font-semibold tracking-wide bg-white/5 border border-fluent-sky/[0.18] rounded-full px-[13px] py-2">
                                <svg class="w-[15px] h-[15px] text-fluent-yellow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 21h18"></path>
                                    <path d="M4 21V9l8-5 8 5v12"></path>
                                    <path d="M9.5 21v-6h5v6"></path>
                                    <path d="M9.5 12h5"></path>
                                </svg>
                                <span x-text="copy[lang].owner"></span>
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </body>
</html>