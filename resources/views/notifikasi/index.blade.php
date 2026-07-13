@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : (auth()->user()->role === 'freelancer' ? 'layouts.freelancer' : 'layouts.customer'))

@section('title', 'Notifikasi - JasaKampus')
@section('page-title', 'Notifikasi')

@section('content')
@php
$typeStyle = [
'order' => [
'label' => 'Pesanan',
'box' => 'bg-blue-50 text-blue-700 border-blue-100',
'badge' => 'bg-blue-100 text-blue-700',
'icon' => 'M6 7h12l-1 13H7L6 7zM9 7a3 3 0 016 0',
],
'pesanan' => [
'label' => 'Pesanan',
'box' => 'bg-blue-50 text-blue-700 border-blue-100',
'badge' => 'bg-blue-100 text-blue-700',
'icon' => 'M6 7h12l-1 13H7L6 7zM9 7a3 3 0 016 0',
],
'pembayaran' => [
'label' => 'Pembayaran',
'box' => 'bg-green-50 text-green-700 border-green-100',
'badge' => 'bg-green-100 text-green-700',
'icon' => 'M3 7h18v10H3V7zm0 3h18M7 15h.01M17 15h.01',
],
'payment' => [
'label' => 'Pembayaran',
'box' => 'bg-green-50 text-green-700 border-green-100',
'badge' => 'bg-green-100 text-green-700',
'icon' => 'M3 7h18v10H3V7zm0 3h18M7 15h.01M17 15h.01',
],
'dispute' => [
'label' => 'Aduan',
'box' => 'bg-red-50 text-red-700 border-red-100',
'badge' => 'bg-red-100 text-red-700',
'icon' => 'M12 9v4m0 4h.01M10.29 3.86l-7 12.14A2 2 0 005 19h14a2 2 0 001.71-3l-7-12.14a2 2 0 00-3.42 0z',
],
'withdrawal' => [
'label' => 'Pencairan',
'box' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
'badge' => 'bg-yellow-100 text-yellow-700',
'icon' => 'M4 7h16v10H4V7zm3 3h.01M17 14h.01M12 12a2 2 0 100-4 2 2 0 000 4z',
],
'progress' => [
'label' => 'Progress',
'box' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
'badge' => 'bg-indigo-100 text-indigo-700',
'icon' => 'M4 19V5m0 14h16M8 16v-5m4 5V8m4 8v-3',
],
'hasil' => [
'label' => 'Hasil',
'box' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
'badge' => 'bg-emerald-100 text-emerald-700',
'icon' => 'M9 12l2 2 4-4M4 6h16v12H4V6z',
],
'revisi' => [
'label' => 'Revisi',
'box' => 'bg-orange-50 text-orange-700 border-orange-100',
'badge' => 'bg-orange-100 text-orange-700',
'icon' => 'M4 4v6h6M20 20v-6h-6M5 19a9 9 0 0114-7M19 5a9 9 0 00-14 7',
],
'system' => [
'label' => 'Sistem',
'box' => 'bg-slate-50 text-slate-700 border-slate-100',
'badge' => 'bg-slate-100 text-slate-700',
'icon' => 'M12 6v6l4 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
],
];

$filterAktif = request('status', 'semua');
@endphp

<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-sm font-bold uppercase tracking-wider text-blue-600">
                Pusat Notifikasi
            </p>

            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 mt-2">
                Notifikasi
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Pantau pemberitahuan akun, pesanan, pembayaran, aduan, dan aktivitas platform.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <div class="inline-flex rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
                <a href="{{ route('notifikasi.index') }}"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition
                    {{ $filterAktif === 'semua' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
                    Semua
                </a>

                <a href="{{ route('notifikasi.index', ['status' => 'baru']) }}"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition
                    {{ $filterAktif === 'baru' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
                    Baru
                </a>

                <a href="{{ route('notifikasi.index', ['status' => 'dibaca']) }}"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition
                    {{ $filterAktif === 'dibaca' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
                    Dibaca
                </a>
            </div>

            @if (($unreadCount ?? 0) > 0)
            <form method="POST" action="{{ route('notifikasi.readAll') }}">
                @csrf

                <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-slate-800 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4M4 6h16M4 12h4M4 18h10" />
                    </svg>
                    Tandai Semua Dibaca
                </button>
            </form>
            @endif
        </div>
    </div>

    @if (session('success'))
    <div class="flex items-start gap-3 px-5 py-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mt-0.5 shrink-0" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M12 3a9 9 0 110 18 9 9 0 010-18z" />
        </svg>
        <span class="text-sm font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-500 font-semibold">Total Notifikasi</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalCount ?? $notifikasis->count() }}</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-500 font-semibold">Belum Dibaca</p>
            <p class="text-3xl font-extrabold text-blue-600 mt-2">{{ $unreadCount ?? 0 }}</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-500 font-semibold">Sudah Dibaca</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $readCount ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        @if ($notifikasis->count() > 0)
        <div class="divide-y divide-slate-100">
            @foreach ($notifikasis as $notifikasi)
            @php
            $tipe = strtolower($notifikasi->tipe ?? 'system');
            $style = $typeStyle[$tipe] ?? $typeStyle['system'];
            @endphp

            <div class="relative p-5 md:p-6 transition {{ $notifikasi->dibaca ? 'bg-white hover:bg-slate-50' : 'bg-blue-50/70 hover:bg-blue-50' }}">
                @if (! $notifikasi->dibaca)
                <div class="absolute left-0 top-0 h-full w-1 bg-blue-600"></div>
                @endif

                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">
                    <div class="flex items-start gap-4 min-w-0">
                        <div class="w-12 h-12 rounded-2xl border flex items-center justify-center shrink-0 {{ $style['box'] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $style['icon'] }}" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-extrabold text-slate-900">
                                    {{ $notifikasi->judul }}
                                </h3>

                                @if (! $notifikasi->dibaca)
                                <span class="px-2.5 py-1 bg-blue-600 text-white rounded-full text-[11px] font-extrabold">
                                    Baru
                                </span>
                                @endif

                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold {{ $style['badge'] }}">
                                    {{ $style['label'] }}
                                </span>
                            </div>

                            <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                                {{ $notifikasi->pesan }}
                            </p>

                            <div class="flex flex-wrap items-center gap-3 mt-3 text-xs text-slate-400 font-semibold">
                                <span>{{ $notifikasi->created_at?->diffForHumans() }}</span>
                                <span>•</span>
                                <span>{{ $notifikasi->created_at?->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('notifikasi.read', $notifikasi->id) }}" class="shrink-0">
                        @csrf

                        <button type="submit"
                            class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-50 hover:border-blue-200 hover:text-blue-700 transition shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                            {{ $notifikasi->url ? 'Buka Detail' : 'Tandai Dibaca' }}
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        @if (method_exists($notifikasis, 'links'))
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $notifikasis->links() }}
        </div>
        @endif
        @else
        <div class="p-12 text-center">
            <div class="mx-auto w-20 h-20 rounded-3xl bg-slate-100 text-slate-500 flex items-center justify-center mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 17h5l-1.4-1.4A2 2 0 0118 14.17V11a6 6 0 10-12 0v3.17a2 2 0 01-.6 1.43L4 17h5M9 17a3 3 0 006 0" />
                </svg>
            </div>

            <h3 class="text-lg font-extrabold text-slate-900">
                Belum ada notifikasi
            </h3>

            <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">
                Notifikasi aktivitas akun, pesanan, pembayaran, dan sistem akan muncul di halaman ini.
            </p>
        </div>
        @endif
    </div>
</div>

<x-auto-refresh :seconds="20" />
@endsection