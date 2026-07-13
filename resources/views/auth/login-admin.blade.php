<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Admin - JasaKampus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-slate-100">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

        {{-- LEFT SIDE --}}
        <div class="hidden lg:flex flex-col justify-between p-12 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 border-r border-white/10">

            <div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-900/40">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-7 h-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-extrabold">
                            JasaKampus Admin
                        </h1>
                        <p class="text-sm text-slate-400 mt-1">
                            Admin Control Center
                        </p>
                    </div>
                </div>

                <div class="mt-20 max-w-xl">
                    <p class="text-sm font-bold uppercase tracking-[0.28em] text-blue-300">
                        Platform Management
                    </p>

                    <h2 class="mt-5 text-5xl font-extrabold leading-tight">
                        Kelola platform secara aman dan terpusat.
                    </h2>

                    <p class="mt-6 text-slate-300 leading-relaxed">
                        Panel admin digunakan untuk memantau pengguna, verifikasi freelancer,
                        mengelola jasa, transaksi, pencairan dana, aduan, dan laporan aktivitas
                        pada sistem JasaKampus.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                    <p class="text-2xl font-extrabold text-white">24/7</p>
                    <p class="text-sm text-slate-400 mt-1">Monitoring</p>
                </div>

                <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                    <p class="text-2xl font-extrabold text-white">Secure</p>
                    <p class="text-sm text-slate-400 mt-1">Admin Access</p>
                </div>

                <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                    <p class="text-2xl font-extrabold text-white">Data</p>
                    <p class="text-sm text-slate-400 mt-1">Terintegrasi</p>
                </div>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="flex items-center justify-center px-6 py-12 bg-slate-50 text-slate-800">

            <div class="w-full max-w-md">

                <div class="lg:hidden mb-8 text-center">
                    <div class="mx-auto w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-8 h-8 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                        </svg>
                    </div>

                    <h1 class="text-2xl font-extrabold mt-4">
                        JasaKampus Admin
                    </h1>
                </div>

                <div class="bg-white border border-slate-200 rounded-3xl shadow-xl shadow-slate-200/70 p-8">

                    <div class="mb-8">
                        <p class="text-sm font-bold uppercase tracking-wider text-blue-600">
                            Login Admin
                        </p>

                        <h2 class="text-3xl font-extrabold text-slate-900 mt-2">
                            Masuk ke Panel Admin
                        </h2>

                        <p class="text-sm text-slate-500 mt-3 leading-relaxed">
                            Gunakan akun admin yang sudah terdaftar untuk mengelola data
                            platform JasaKampus.
                        </p>
                    </div>

                    @if (session('success'))
                    <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if (session('status'))
                    <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login.admin.store') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700 mb-2">
                                Email Admin
                            </label>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                placeholder="admin@jasakampus.com"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 mb-2">
                                Password
                            </label>

                            <div class="relative">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    placeholder="Masukkan password admin"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 pr-12 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-slate-700">
                                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                Ingat saya
                            </label>

                            <a href="{{ route('password.request', ['role' => 'admin']) }}"
                                class="text-sm font-bold text-blue-600 hover:underline">
                                Lupa password?
                            </a>
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-slate-900 px-5 py-3.5 text-sm font-bold text-white hover:bg-slate-800 transition shadow-lg shadow-slate-300">
                            Masuk ke Admin Panel
                        </button>
                    </form>

                    <div class="mt-7 pt-6 border-t border-slate-100 flex items-center justify-center">
                        <a href="{{ route('landing') }}"
                            class="text-sm font-semibold text-slate-500 hover:text-slate-900">
                            Kembali ke Landing
                        </a>
                    </div>
                </div>

                <p class="text-center text-xs text-slate-400 mt-6">
                    © 2024 JasaKampus. Admin access only.
                </p>
            </div>
        </div>
    </div>

    @php
    $pendingOtp = session('login_otp');
    $showOtpModal = $pendingOtp && ($pendingOtp['role'] ?? null) === 'admin';
    $expiredAt = $pendingOtp['expired_at'] ?? now()->addMinutes(5)->toDateTimeString();
    $otpEmail = $pendingOtp['email'] ?? old('email');
    @endphp

    @if ($showOtpModal)
    <div id="otpModal"
        class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center px-4">

        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">

            <div class="p-6 border-b border-slate-100 text-center">
                <div class="mx-auto w-16 h-16 rounded-3xl bg-blue-100 text-blue-600 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-8 h-8"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 11c1.657 0 3-1.12 3-2.5S13.657 6 12 6 9 7.12 9 8.5s1.343 2.5 3 2.5zm0 0v7m-3-3h6" />
                    </svg>
                </div>

                <h2 class="text-2xl font-extrabold text-slate-900">
                    Verifikasi OTP Admin
                </h2>

                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    Masukkan 6 digit kode OTP yang sudah dikirim ke email admin.
                </p>

                @if ($otpEmail)
                <p class="mt-3 text-sm font-bold text-blue-600">
                    {{ $otpEmail }}
                </p>
                @endif
            </div>

            <form method="POST" action="{{ route('login.verify-otp') }}" id="otpForm" class="p-6 space-y-5">
                @csrf

                <input type="hidden" name="pin" id="otpPin">

                <div class="flex justify-center gap-2">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text"
                        maxlength="1"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        class="otp-box w-12 h-14 rounded-2xl border border-slate-300 text-center text-xl font-extrabold text-slate-900 focus:border-blue-500 focus:ring-blue-500"
                        value="{{ old('pin') ? substr(old('pin'), $i, 1) : '' }}">
                        @endfor
                </div>

                <div class="text-center">
                    <p class="text-sm text-slate-500">
                        Kode OTP berlaku selama:
                    </p>

                    <p id="otpCountdown"
                        data-expired-at="{{ \Carbon\Carbon::parse($expiredAt)->timestamp * 1000 }}"
                        class="text-lg font-extrabold text-blue-600 mt-1">
                        --
                    </p>
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-blue-600 px-5 py-3.5 text-sm font-bold text-white hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                    Verifikasi & Masuk
                </button>
            </form>

            <div class="px-6 pb-6 flex flex-col sm:flex-row gap-3">
                <form method="POST" action="{{ route('login.resend-otp') }}" class="flex-1">
                    @csrf

                    <button type="submit"
                        class="w-full rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 transition">
                        Kirim Ulang OTP
                    </button>
                </form>

                <form method="POST" action="{{ route('login.cancel-otp') }}" class="flex-1">
                    @csrf

                    <button type="submit"
                        class="w-full rounded-xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-200 transition">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');

            if (passwordInput && togglePassword) {
                togglePassword.addEventListener('click', function() {
                    passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
                });
            }

            const otpBoxes = Array.from(document.querySelectorAll('.otp-box'));
            const otpForm = document.getElementById('otpForm');
            const otpPin = document.getElementById('otpPin');
            const countdown = document.getElementById('otpCountdown');

            if (otpBoxes.length > 0) {
                otpBoxes[0].focus();

                otpBoxes.forEach(function(box, index) {
                    box.addEventListener('input', function() {
                        box.value = box.value.replace(/[^0-9]/g, '');

                        if (box.value && otpBoxes[index + 1]) {
                            otpBoxes[index + 1].focus();
                        }
                    });

                    box.addEventListener('keydown', function(event) {
                        if (event.key === 'Backspace' && !box.value && otpBoxes[index - 1]) {
                            otpBoxes[index - 1].focus();
                        }
                    });

                    box.addEventListener('paste', function(event) {
                        event.preventDefault();

                        const pasted = (event.clipboardData || window.clipboardData)
                            .getData('text')
                            .replace(/[^0-9]/g, '')
                            .slice(0, 6);

                        pasted.split('').forEach(function(char, i) {
                            if (otpBoxes[i]) {
                                otpBoxes[i].value = char;
                            }
                        });

                        const nextEmpty = otpBoxes.find(box => !box.value);
                        if (nextEmpty) {
                            nextEmpty.focus();
                        } else {
                            otpBoxes[otpBoxes.length - 1].focus();
                        }
                    });
                });
            }

            if (otpForm && otpPin) {
                otpForm.addEventListener('submit', function() {
                    otpPin.value = otpBoxes.map(box => box.value).join('');
                });
            }

            if (countdown) {
                const expiredAt = Number(countdown.dataset.expiredAt);

                function updateCountdown() {
                    const now = Date.now();
                    const distance = expiredAt - now;

                    if (distance <= 0) {
                        countdown.textContent = 'OTP sudah kedaluwarsa';
                        countdown.classList.remove('text-blue-600');
                        countdown.classList.add('text-red-600');
                        return;
                    }

                    const minutes = Math.floor(distance / 1000 / 60);
                    const seconds = Math.floor((distance / 1000) % 60);

                    countdown.textContent =
                        String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            }
        });
    </script>
</body>

</html>