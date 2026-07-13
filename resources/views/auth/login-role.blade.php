<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }} - JasaKampus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="min-h-screen flex items-center justify-center px-6 py-10">
        <div class="w-full max-w-5xl bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-5">
            <section class="lg:col-span-2 bg-blue-700 text-white p-8 lg:p-10 flex flex-col justify-between">
                <div>
                    <a href="{{ url('/') }}" class="inline-block text-2xl font-extrabold">
                        JasaKampus
                    </a>

                    <div class="mt-10">
                        <p class="text-sm font-semibold text-blue-100 uppercase tracking-wide">
                            Login {{ ucfirst($role) }}
                        </p>

                        <h1 class="mt-3 text-3xl lg:text-4xl font-extrabold leading-tight">
                            {{ $title }}
                        </h1>

                        <p class="mt-4 text-blue-100 leading-relaxed">
                            {{ $subtitle }}
                        </p>
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-blue-500 text-sm text-blue-100">
                    Belum punya akun?
                    @if (!empty($registerRoute))
                    <a href="{{ $registerRoute }}"
                        class="text-sm font-bold text-blue-600 hover:underline">
                        {{ $registerText ?? 'Buat akun baru' }}
                    </a>
                    @endif
                </div>
            </section>

            <section class="lg:col-span-3 p-8 lg:p-10">
                <div class="max-w-xl mx-auto">
                    <div class="mb-8">
                        <h2 class="text-3xl font-extrabold text-slate-900">
                            Selamat Datang
                        </h2>
                        <p class="mt-2 text-slate-500">
                            Masukkan email dan password. Setelah itu, OTP akan dikirim ke email kamu.
                        </p>
                    </div>

                    @if (session('success'))
                    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-700">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                        <p class="font-bold mb-2">Ada data yang perlu diperbaiki:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ $storeRoute }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Email
                            </label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Password
                            </label>

                            <div class="relative">
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 pr-12 focus:border-blue-500 focus:ring-blue-500"
                                    required>

                                <button
                                    type="button"
                                    data-toggle-password="password"
                                    class="absolute inset-y-0 right-4 flex items-center text-slate-500 hover:text-blue-600">
                                    👁
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between -mt-2">
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                Ingat saya
                            </label>

                            <a href="{{ route('password.request', ['role' => $role]) }}"
                                class="text-sm font-bold text-blue-600 hover:underline">
                                Lupa password?
                            </a>
                        </div>

                        <div class="text-right">
                            <a href="{{ $otherLoginRoute }}" class="text-sm font-bold text-slate-600 hover:text-blue-600 hover:underline">
                                {{ $otherLoginText }}
                            </a>
                        </div>
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-blue-600 px-5 py-3.5 text-white font-bold hover:bg-blue-700">
                            Kirim OTP Login
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    @if (session('login_otp.role') === $role)
    @php
    $pendingOtp = session('login_otp');
    $expiredAt = $pendingOtp['expired_at'] ?? now()->toDateTimeString();
    @endphp

    <div id="otpModal"
        class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center px-4">
        <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-200 p-7">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold">
                    OTP
                </div>

                <h2 class="mt-5 text-2xl font-extrabold text-slate-900">
                    Verifikasi Login
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Masukkan OTP 6 digit yang sudah dikirim ke email akun kamu.
                </p>
            </div>

            <form id="otpForm" action="{{ route('login.verify-otp') }}" method="POST" class="mt-7">
                @csrf

                <input type="hidden" name="pin" id="pinValue" value="{{ old('pin') }}">

                <div class="grid grid-cols-6 gap-3">
                    @for ($i = 0; $i < 6; $i++)
                        <input
                        type="text"
                        maxlength="1"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        value="{{ old('pin') ? substr(old('pin'), $i, 1) : '' }}"
                        class="pin-box h-14 rounded-xl border border-slate-300 text-center text-2xl font-bold text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @endfor
                </div>

                <button
                    type="submit"
                    id="submitOtpButton"
                    class="mt-6 w-full rounded-xl bg-blue-600 px-5 py-3.5 text-white font-bold hover:bg-blue-700">
                    Verifikasi & Masuk
                </button>
            </form>

            <div class="mt-5 text-center">
                <p class="text-sm text-slate-400">
                    OTP berlaku selama
                    <span
                        id="otpCountdown"
                        class="font-bold text-blue-600"
                        data-expires-at="{{ \Carbon\Carbon::parse($expiredAt)->timestamp * 1000 }}"
                        data-server-now="{{ now()->timestamp * 1000 }}">
                        10:00
                    </span>
                </p>

                <form action="{{ route('login.resend-otp') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="text-sm font-bold text-blue-600 hover:underline">
                        Kirim ulang OTP
                    </button>
                </form>

                <form action="{{ route('login.cancel-otp') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-slate-800">
                        Batal Login
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('click', function(event) {
            const button = event.target.closest('[data-toggle-password]');

            if (!button) return;

            const input = document.getElementById(button.getAttribute('data-toggle-password'));

            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = '🙈';
            } else {
                input.type = 'password';
                button.textContent = '👁';
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('otpForm');
            const pinValue = document.getElementById('pinValue');
            const boxes = Array.from(document.querySelectorAll('.pin-box'));
            const countdown = document.getElementById('otpCountdown');
            const submitButton = document.getElementById('submitOtpButton');

            if (form && pinValue && boxes.length) {
                boxes.find(box => !box.value)?.focus();

                boxes.forEach((box, index) => {
                    box.addEventListener('input', function() {
                        this.value = this.value.replace(/\D/g, '');

                        if (this.value && index < boxes.length - 1) {
                            boxes[index + 1].focus();
                        }

                        pinValue.value = boxes.map(box => box.value).join('');
                    });

                    box.addEventListener('keydown', function(event) {
                        if (event.key === 'Backspace' && !this.value && index > 0) {
                            boxes[index - 1].focus();
                        }
                    });
                });

                form.addEventListener('submit', function(event) {
                    pinValue.value = boxes.map(box => box.value).join('');

                    if (pinValue.value.length !== 6) {
                        event.preventDefault();
                        alert('Masukkan OTP 6 digit terlebih dahulu.');
                    }
                });
            }

            if (countdown) {
                const expiresAt = Number(countdown.dataset.expiresAt);
                const serverNow = Number(countdown.dataset.serverNow);
                const clientStart = Date.now();

                function currentServerTime() {
                    return serverNow + (Date.now() - clientStart);
                }

                function updateCountdown() {
                    const distance = expiresAt - currentServerTime();

                    if (distance <= 0) {
                        countdown.textContent = '00:00';
                        countdown.classList.remove('text-blue-600');
                        countdown.classList.add('text-red-600');

                        if (submitButton) {
                            submitButton.disabled = true;
                            submitButton.textContent = 'OTP Kedaluwarsa';
                            submitButton.classList.add('opacity-60', 'cursor-not-allowed');
                        }

                        clearInterval(timer);
                        return;
                    }

                    const minutes = Math.floor(distance / 1000 / 60);
                    const seconds = Math.floor((distance / 1000) % 60);

                    countdown.textContent =
                        String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                }

                updateCountdown();
                const timer = setInterval(updateCountdown, 1000);
            }
        });
    </script>
</body>

</html>