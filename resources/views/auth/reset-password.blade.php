<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reset Password - JasaKampus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 overflow-x-hidden">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-[45%_55%]">

        {{-- LEFT SIDE --}}
        <div class="hidden lg:flex flex-col justify-between px-12 py-10 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 text-white overflow-hidden">

            <div>
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-900/40">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-7 h-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4" />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-extrabold">
                            JasaKampus
                        </h1>
                        <p class="text-sm text-slate-400 mt-1">
                            Account Security Center
                        </p>
                    </div>
                </a>

                <div class="mt-20 max-w-xl">
                    <p class="text-sm font-bold uppercase tracking-[0.28em] text-blue-300">
                        Password Recovery
                    </p>

                    <h2 class="mt-5 text-4xl xl:text-5xl font-extrabold leading-tight">
                        Buat password baru untuk mengamankan akunmu.
                    </h2>

                    <p class="mt-6 text-slate-300 leading-relaxed">
                        Masukkan password baru yang kuat dan mudah kamu ingat.
                        Setelah berhasil direset, kamu bisa login kembali sesuai peran akunmu.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-10">
                <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                    <p class="text-xl font-extrabold text-white">
                        Aman
                    </p>
                    <p class="text-sm text-slate-400 mt-1">
                        Proteksi akun
                    </p>
                </div>

                <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                    <p class="text-xl font-extrabold text-white">
                        Cepat
                    </p>
                    <p class="text-sm text-slate-400 mt-1">
                        Reset instan
                    </p>
                </div>

                <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                    <p class="text-xl font-extrabold text-white">
                        Privat
                    </p>
                    <p class="text-sm text-slate-400 mt-1">
                        Data terlindungi
                    </p>
                </div>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="flex items-center justify-center px-5 py-8 lg:px-10 bg-slate-50">

            <div class="w-full max-w-md">

                <div class="lg:hidden mb-6 text-center">
                    <a href="{{ route('landing') }}" class="inline-flex flex-col items-center">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-8 h-8 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4" />
                            </svg>
                        </div>

                        <h1 class="text-2xl font-extrabold mt-4 text-slate-900">
                            JasaKampus
                        </h1>
                    </a>
                </div>

                <div class="bg-white border border-slate-200 rounded-3xl shadow-xl shadow-slate-200/70 p-7 md:p-8">

                    <div class="mb-8">
                        <p class="text-sm font-bold uppercase tracking-wider text-blue-600">
                            Reset Password
                        </p>

                        <h2 class="text-3xl font-extrabold text-slate-900 mt-2">
                            Buat Password Baru
                        </h2>

                        <p class="text-sm text-slate-500 mt-3 leading-relaxed">
                            Masukkan password baru untuk akun JasaKampus kamu.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                        @csrf

                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700 mb-2">
                                Email
                            </label>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email', $request->email) }}"
                                required
                                readonly
                                class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm text-slate-600 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 mb-2">
                                Password Baru
                            </label>

                            <div class="relative">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    placeholder="Minimal 8 karakter"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 pr-12 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

                                <button
                                    type="button"
                                    data-toggle-password="password"
                                    class="password-toggle absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg"
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

                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">
                                Konfirmasi Password Baru
                            </label>

                            <div class="relative">
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    placeholder="Ulangi password baru"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 pr-12 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

                                <button
                                    type="button"
                                    data-toggle-password="password_confirmation"
                                    class="password-toggle absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg"
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

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-blue-600 px-5 py-3.5 text-sm font-bold text-white hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                            Simpan Password Baru
                        </button>
                    </form>

                    <div class="mt-7 pt-6 border-t border-slate-100 flex items-center justify-center">
                        <a href="{{ route('login.customer') }}"
                            class="text-sm font-bold text-blue-600 hover:underline">
                            Kembali ke Login
                        </a>
                    </div>
                </div>

                <p class="text-center text-xs text-slate-400 mt-5">
                    © 2024 JasaKampus. Account recovery.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.password-toggle').forEach(function (button) {
                button.addEventListener('click', function () {
                    const inputId = button.getAttribute('data-toggle-password');
                    const input = document.getElementById(inputId);

                    if (!input) return;

                    input.type = input.type === 'password' ? 'text' : 'password';
                });
            });
        });
    </script>
</body>

</html>