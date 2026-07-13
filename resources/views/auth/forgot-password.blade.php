@php
$role = request('role') === 'freelancer' ? 'freelancer' : 'customer';

$loginRoute = $role === 'freelancer'
? route('login.freelancer')
: route('login.customer');

$roleLabel = $role === 'freelancer'
? 'Freelancer'
: 'Customer';
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lupa Password {{ $roleLabel }} - JasaKampus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-5xl bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-5">

            {{-- PANEL KIRI --}}
            <section class="lg:col-span-2 bg-blue-700 text-white p-8 lg:p-10 flex flex-col justify-between">
                <div>
                    <a href="{{ url('/') }}" class="inline-block text-2xl font-extrabold">
                        JasaKampus
                    </a>

                    <div class="mt-10">
                        <p class="text-sm font-semibold text-blue-100 uppercase tracking-wide">
                            Reset Password {{ $roleLabel }}
                        </p>

                        <h1 class="mt-3 text-3xl lg:text-4xl font-extrabold leading-tight">
                            Lupa Password?
                        </h1>

                        <p class="mt-4 text-blue-100 leading-relaxed">
                            Jangan khawatir. Masukkan email yang sudah terdaftar, lalu kami akan mengirimkan link reset password ke email tersebut.
                        </p>
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-blue-500 text-sm text-blue-100">
                    Sudah ingat password?
                    <a href="{{ $loginRoute }}" class="font-bold text-white hover:underline">
                        Kembali ke Login {{ $roleLabel }}
                    </a>
                </div>
            </section>

            {{-- FORM KANAN --}}
            <section class="lg:col-span-3 p-8 lg:p-10">
                <div class="max-w-xl mx-auto">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-3xl mb-4">
                            🔐
                        </div>

                        <h2 class="text-3xl font-extrabold text-slate-900">
                            Reset Password {{ $roleLabel }}
                        </h2>

                        <p class="mt-3 text-slate-500 leading-relaxed">
                            Link reset password akan dikirim ke email yang sesuai dengan akun {{ strtolower($roleLabel) }} kamu.
                        </p>
                    </div>

                    {{-- Status sukses --}}
                    @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-700">
                        Link reset password berhasil dikirim ke email yang terdaftar.
                    </div>
                    @endif

                    {{-- Error --}}
                    @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                        <p class="font-bold mb-2">
                            Ada data yang perlu diperbaiki:
                        </p>

                        <ul class="list-disc ml-5 space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700 mb-2">
                                Email Terdaftar
                            </label>

                            <input id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                placeholder="contoh@email.com"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-blue-600 px-5 py-3.5 text-white font-bold hover:bg-blue-700 transition">
                            Kirim Link Reset Password
                        </button>
                    </form>

                    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
                        <a href="{{ $loginRoute }}"
                            class="font-bold text-blue-600 hover:underline">
                            Kembali ke Login {{ $roleLabel }}
                        </a>

                        <a href="{{ url('/') }}"
                            class="font-semibold text-slate-500 hover:text-blue-600 hover:underline">
                            Halaman Utama
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>

</html>