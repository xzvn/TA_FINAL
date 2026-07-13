<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Customer - JasaKampus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 overflow-x-hidden">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-[45%_55%]">

        {{-- LEFT SIDE --}}
        <div class="hidden lg:flex flex-col justify-between px-12 py-10 bg-gradient-to-br from-blue-700 via-blue-800 to-slate-950 text-white overflow-hidden">

            <div>
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-7 h-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m6-6H6" />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-extrabold">
                            JasaKampus
                        </h1>
                        <p class="text-sm text-blue-100 mt-1">
                            Marketplace jasa mahasiswa
                        </p>
                    </div>
                </a>

                <div class="mt-16 max-w-lg">
                    <p class="text-sm font-bold uppercase tracking-[0.24em] text-blue-200">
                        Customer Account
                    </p>

                    <h2 class="mt-5 text-4xl xl:text-5xl font-extrabold leading-tight">
                        Temukan jasa mahasiswa sesuai kebutuhanmu.
                    </h2>

                    <p class="mt-6 text-blue-100 leading-relaxed text-base">
                        Daftar sebagai customer untuk mencari layanan, berdiskusi dengan freelancer,
                        melakukan pemesanan, memantau progress, dan memberi ulasan setelah pekerjaan selesai.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-10">
                <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                    <p class="text-xl font-extrabold">Aman</p>
                    <p class="text-sm text-blue-100 mt-1">Escrow</p>
                </div>

                <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                    <p class="text-xl font-extrabold">Cepat</p>
                    <p class="text-sm text-blue-100 mt-1">Cari Jasa</p>
                </div>

                <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                    <p class="text-xl font-extrabold">Terpercaya</p>
                    <p class="text-sm text-blue-100 mt-1">Kampus</p>
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
                                    d="M12 6v12m6-6H6" />
                            </svg>
                        </div>

                        <h1 class="text-2xl font-extrabold mt-4 text-slate-900">
                            JasaKampus
                        </h1>
                    </a>
                </div>

                <div class="bg-white border border-slate-200 rounded-3xl shadow-xl shadow-slate-200/70 p-7 md:p-8">

                    <div class="mb-7">
                        <p class="text-sm font-bold uppercase tracking-wider text-blue-600">
                            Daftar Customer
                        </p>

                        <h2 class="text-3xl font-extrabold text-slate-900 mt-2">
                            Buat Akun Baru
                        </h2>

                        <p class="text-sm text-slate-500 mt-3 leading-relaxed">
                            Lengkapi data berikut untuk mulai menggunakan layanan JasaKampus.
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

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="nama" class="block text-sm font-bold text-slate-700 mb-2">
                                Nama Lengkap
                            </label>

                            <input
                                id="nama"
                                type="text"
                                name="nama"
                                value="{{ old('nama') }}"
                                required
                                autofocus
                                placeholder="Masukkan nama lengkap"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700 mb-2">
                                Email
                            </label>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="nama@email.com"
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
                                Konfirmasi Password
                            </label>

                            <div class="relative">
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    placeholder="Ulangi password"
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
                            Daftar Sebagai Customer
                        </button>
                    </form>

                    <div class="mt-6 pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <a href="{{ route('login.customer') }}"
                            class="text-sm font-bold text-blue-600 hover:underline">
                            Sudah punya akun? Login
                        </a>

                        <a href="{{ route('freelancer.register') }}"
                            class="text-sm font-semibold text-slate-500 hover:text-slate-900">
                            Daftar Freelancer
                        </a>
                    </div>
                </div>

                <p class="text-center text-xs text-slate-400 mt-5">
                    © 2024 JasaKampus. Marketplace jasa mahasiswa.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.password-toggle').forEach(function(button) {
                button.addEventListener('click', function() {
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