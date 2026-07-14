<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Marketplace Jasa - JasaKampus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#f6f8fb] min-h-screen text-slate-800">
    <div class="max-w-7xl mx-auto px-6 py-10">

        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">
                    Marketplace Jasa
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Temukan jasa terbaik dari freelancer terverifikasi.
                </p>
            </div>

            <a href="{{ route('dashboard') }}"
                class="px-5 py-3 bg-white border border-slate-200 rounded-lg text-sm font-semibold hover:bg-slate-50">
                Dashboard
            </a>
        </div>

        <form method="GET" action="{{ route('customer.marketplace') }}" class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari jasa..."
                    class="rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">

                <select name="kategori"
                    class="rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

                    <option value="">
                        Semua Kategori
                    </option>

                    @foreach (config('jasakampus.kategori_jasa') as $kategori)
                    <option value="{{ $kategori }}" {{ request('kategori') === $kategori ? 'selected' : '' }}>
                        {{ $kategori }}
                    </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                    Cari Jasa
                </button>
            </div>
        </form>

        @if ($jasa->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($jasa as $item)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="h-44 bg-slate-100 flex items-center justify-center">
                    @if ($item->thumbnail)
                    <img src="{{ \App\Services\CloudinaryService::mediaUrl($item->thumbnail) }}"
                        alt="{{ $item->nama_jasa }}"
                        onerror="this.onerror=null;this.src='{{ asset('images/placeholder-service.svg') }}';"
                        class="w-full h-full object-cover">
                    @else
                    <span class="text-slate-400 text-sm">Belum ada thumbnail</span>
                    @endif
                </div>

                <div class="p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                            {{ $item->kategori }}
                        </span>

                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                            TERSEDIA
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900">
                        {{ $item->nama_jasa }}
                    </h3>

                    <p class="text-sm text-slate-500 mt-2">
                        Freelancer: {{ $item->freelancer->nama ?? '-' }}
                    </p>

                    <p class="text-sm text-slate-500 mt-3 line-clamp-3">
                        {{ $item->deskripsi }}
                    </p>

                    <div class="mt-5 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-400">Mulai dari</p>
                            <p class="text-lg font-bold text-blue-600">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </p>
                        </div>

                        <a href="{{ route('customer.jasa.show', $item->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-10 text-center">
            <h3 class="text-lg font-bold text-slate-900">
                Belum ada jasa tersedia
            </h3>
            <p class="text-sm text-slate-500 mt-2">
                Jasa dari freelancer yang sudah diverifikasi akan tampil di sini.
            </p>
        </div>
        @endif
    </div>
</body>

</html>