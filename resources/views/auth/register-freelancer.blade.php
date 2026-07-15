<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Freelancer - JasaKampus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="min-h-screen flex items-center justify-center px-6 py-10">
        <div class="w-full max-w-6xl bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-5">

            <section class="lg:col-span-2 bg-blue-700 text-white p-8 lg:p-10 flex flex-col justify-between">
                <div>
                    <a href="{{ url('/') }}" class="inline-block text-2xl font-extrabold tracking-tight">
                        JasaKampus
                    </a>

                    <div class="mt-10">
                        <p class="text-sm font-semibold text-blue-100 uppercase tracking-wide">
                            Pendaftaran Freelancer
                        </p>

                        <h1 class="mt-3 text-3xl lg:text-4xl font-extrabold leading-tight">
                            Bangun portofolio dan mulai menerima pesanan jasa.
                        </h1>

                        <p class="mt-4 text-blue-100 leading-relaxed">
                            Lengkapi data akademik, unggah KTM, dan portofolio. Admin akan meninjau data kamu sebelum akun freelancer aktif.
                        </p>
                    </div>

                    <div class="mt-10 space-y-4">
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-white text-blue-700 flex items-center justify-center font-bold shrink-0">
                                1
                            </div>
                            <div>
                                <h3 class="font-bold">Isi data diri</h3>
                                <p class="text-sm text-blue-100">Gunakan nama dan email kampus yang valid.</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-white text-blue-700 flex items-center justify-center font-bold shrink-0">
                                2
                            </div>
                            <div>
                                <h3 class="font-bold">Unggah dokumen</h3>
                                <p class="text-sm text-blue-100">KTM dan portofolio digunakan untuk proses verifikasi.</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-white text-blue-700 flex items-center justify-center font-bold shrink-0">
                                3
                            </div>
                            <div>
                                <h3 class="font-bold">Menunggu verifikasi</h3>
                                <p class="text-sm text-blue-100">Setelah disetujui admin, kamu bisa mulai membuat jasa.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-blue-500 text-sm text-blue-100">
                    Sudah punya akun?
                    <a href="{{ route('login.freelancer') }}" class="font-bold text-white hover:underline">
                        Masuk sekarang
                    </a>
                </div>
            </section>

            <section class="lg:col-span-3 p-8 lg:p-10">
                <div class="max-w-3xl">
                    <div class="mb-8">
                        <h2 class="text-3xl font-extrabold text-slate-900">
                            Daftar Sebagai Freelancer
                        </h2>
                        <p class="mt-2 text-slate-500">
                            Pastikan data yang kamu masukkan sesuai dengan identitas kampus.
                        </p>
                    </div>

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

                    <form action="{{ route('freelancer.register.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Nama Lengkap
                                </label>
                                <input
                                    type="text"
                                    name="nama"
                                    value="{{ old('nama') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                                    required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Alamat Domisili
                                </label>
                                <textarea
                                    name="alamat"
                                    rows="3"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                                    required>{{ old('alamat') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Email Kampus
                                </label>
                                <input
                                    type="email"
                                    name="email_kampus"
                                    value="{{ old('email_kampus') }}"
                                    placeholder="nama@student.ac.id"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                <p class="mt-2 text-xs text-slate-400">
                                    Email pribadi seperti Gmail/Yahoo tidak diterima.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Nama Kampus / Universitas
                                </label>
                                <input
                                    type="text"
                                    name="universitas"
                                    value="{{ old('universitas') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                                    required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Program Studi
                                </label>
                                <input
                                    type="text"
                                    name="program_studi"
                                    value="{{ old('program_studi') }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Upload KTM
                                </label>
                                <input
                                    type="file"
                                    name="file_ktm"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="block w-full text-sm text-slate-600 border border-slate-300 rounded-xl cursor-pointer bg-white focus:outline-none file:mr-4 file:py-3 file:px-4 file:border-0 file:bg-blue-50 file:text-blue-700 file:font-bold"
                                    required>
                                <p class="mt-2 text-xs text-slate-400">
                                    Format JPG, PNG, atau PDF. Maksimal 2MB.
                                </p>

                                @error('file_ktm')
                                <p class="mt-2 text-sm font-semibold text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Upload Portofolio
                                </label>
                                <input
                                    type="file"
                                    name="file_portofolio"
                                    accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.ppt,.pptx"
                                    class="block w-full text-sm text-slate-600 border border-slate-300 rounded-xl cursor-pointer bg-white focus:outline-none file:mr-4 file:py-3 file:px-4 file:border-0 file:bg-blue-50 file:text-blue-700 file:font-bold"
                                    required>
                                <p class="mt-2 text-xs text-slate-400">
                                    JPG, PNG, WEBP, PDF, DOC, DOCX, PPT, PPTX. Maksimal 1MB.
                                </p>
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

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Konfirmasi Password
                                </label>

                                <div class="relative">
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-3 pr-12 focus:border-blue-500 focus:ring-blue-500"
                                        required>

                                    <button
                                        type="button"
                                        data-toggle-password="password_confirmation"
                                        class="absolute inset-y-0 right-4 flex items-center text-slate-500 hover:text-blue-600">
                                        👁
                                    </button>
                                </div>
                            </div>

                            <div class="pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <a href="{{ route('register') }}" class="text-sm font-semibold text-slate-500 hover:text-blue-700">
                                    Daftar sebagai customer
                                </a>

                                <button
                                    type="submit"
                                    class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-sm">
                                    Daftar Freelancer
                                </button>
                            </div>
                    </form>
                </div>
            </section>
        </div>
    </main>
    @if (session()->has('freelancer_register_otp'))
    @php
    $pendingOtp = session('freelancer_register_otp');
    $expiredAt = $pendingOtp['expired_at'] ?? now()->toDateTimeString();
    $emailOtp = $pendingOtp['data']['email_kampus'] ?? null;
    @endphp

    <div id="otpModal"
        class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center px-4">
        <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-200 p-7">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold">
                    OTP
                </div>

                <h2 class="mt-5 text-2xl font-extrabold text-slate-900">
                    Verifikasi Email Kampus
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Masukkan OTP 6 digit yang sudah dikirim ke email kampus kamu.
                </p>

                @if ($emailOtp)
                <p class="mt-2 text-sm font-bold text-blue-600">
                    {{ $emailOtp }}
                </p>
                @endif
            </div>

            <form id="otpForm" action="{{ route('freelancer.register.verify-otp') }}" method="POST" class="mt-7">
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
                    Verifikasi & Buat Akun
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

                <form action="{{ route('freelancer.register.resend-otp') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="text-sm font-bold text-blue-600 hover:underline">
                        Kirim ulang OTP
                    </button>
                </form>

                <form action="{{ route('freelancer.register.cancel-otp') }}" method="POST" class="mt-4">
                    @csrf
                    <button
                        type="submit"
                        class="text-sm font-semibold text-slate-500 hover:text-slate-800">
                        Ganti Email
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('otpModal');
            const closeButton = document.getElementById('closeOtpModal');
            const form = document.getElementById('otpForm');
            const pinValue = document.getElementById('pinValue');
            const boxes = Array.from(document.querySelectorAll('.pin-box'));
            const countdown = document.getElementById('otpCountdown');
            const submitButton = document.getElementById('submitOtpButton');

            if (closeButton && modal) {
                closeButton.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            }

            if (form && pinValue && boxes.length) {
                boxes.find(box => !box.value)?.focus();

                boxes.forEach((box, index) => {
                    box.addEventListener('input', function() {
                        this.value = this.value.replace(/\D/g, '');

                        if (this.value && index < boxes.length - 1) {
                            boxes[index + 1].focus();
                        }

                        updateHiddenPin();
                    });

                    box.addEventListener('keydown', function(event) {
                        if (event.key === 'Backspace' && !this.value && index > 0) {
                            boxes[index - 1].focus();
                        }
                    });

                    box.addEventListener('paste', function(event) {
                        event.preventDefault();

                        const pasted = (event.clipboardData || window.clipboardData)
                            .getData('text')
                            .replace(/\D/g, '')
                            .slice(0, 6);

                        pasted.split('').forEach((char, i) => {
                            if (boxes[i]) {
                                boxes[i].value = char;
                            }
                        });

                        updateHiddenPin();
                        boxes[Math.min(pasted.length, boxes.length - 1)].focus();
                    });
                });

                function updateHiddenPin() {
                    pinValue.value = boxes.map(box => box.value).join('');
                }

                form.addEventListener('submit', function(event) {
                    updateHiddenPin();

                    if (pinValue.value.length !== 6) {
                        event.preventDefault();
                        alert('Masukkan OTP 6 digit terlebih dahulu.');
                        boxes.find(box => !box.value)?.focus();
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

    <script>
        document.addEventListener('click', function(event) {
            const button = event.target.closest('[data-toggle-password]');

            if (!button) {
                return;
            }

            const inputId = button.getAttribute('data-toggle-password');
            const input = document.getElementById(inputId);

            if (!input) {
                return;
            }

            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = '🙈';
            } else {
                input.type = 'password';
                button.textContent = '👁';
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const ktmInput = document.querySelector('input[name="file_ktm"]');
            const portfolioInput = document.querySelector(
                'input[name="file_portofolio"]'
            );

            form?.addEventListener('submit', function(event) {
                const ktmMaxSize = 2 * 1024 * 1024;
                const portfolioMaxSize = 1 * 1024 * 1024;

                if (
                    ktmInput?.files[0] &&
                    ktmInput.files[0].size > ktmMaxSize
                ) {
                    event.preventDefault();
                    alert('Ukuran file KTM maksimal 2 MB.');
                    ktmInput.focus();
                    return;
                }

                if (
                    portfolioInput?.files[0] &&
                    portfolioInput.files[0].size > portfolioMaxSize
                ) {
                    event.preventDefault();
                    alert('Ukuran file portofolio maksimal 1 MB.');
                    portfolioInput.focus();
                }
            });
        });
    </script>
</body>

</html>