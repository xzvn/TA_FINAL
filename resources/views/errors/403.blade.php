<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-lg w-full bg-white rounded-3xl shadow-sm border border-slate-200 p-8 text-center">
        <div class="w-16 h-16 mx-auto rounded-full bg-red-100 text-red-600 flex items-center justify-center text-3xl">
            ⚠️
        </div>

        <h1 class="mt-6 text-2xl font-bold text-slate-900">
            Akses Ditolak
        </h1>

        <p class="mt-3 text-slate-600">
            {{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}
        </p>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() }}"
                class="px-5 py-3 rounded-xl bg-slate-200 text-slate-700 font-semibold hover:bg-slate-300">
                Kembali
            </a>

            <a href="{{ route('dashboard') }}"
                class="px-5 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
                Ke Dashboard
            </a>
        </div>
    </div>
</body>

</html>