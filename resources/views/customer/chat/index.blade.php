@extends('layouts.customer')

@section('title', 'Pesan - JasaKampus')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500">
                Dashboard / Pesan
            </p>

            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mt-1">
                Pesan
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Daftar percakapan kamu dengan freelancer.
            </p>
        </div>

        <a href="{{ route('customer.marketplace') }}"
            class="px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
            Cari Jasa
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">
                Daftar Percakapan
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Total {{ $percakapans->count() }} percakapan.
            </p>
        </div>

        @if ($percakapans->count() > 0)
        <div class="divide-y divide-slate-100">
            @foreach ($percakapans as $chat)
            <a href="{{ route('customer.chat.show', $chat->jasa->id) }}"
                class="block p-6 hover:bg-slate-50 transition">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold shrink-0">
                        {{ strtoupper(substr($chat->freelancer->nama ?? 'F', 0, 1)) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-slate-900">
                                    {{ $chat->freelancer->nama ?? 'Freelancer' }}
                                </h3>

                                <p class="text-sm text-blue-600 font-semibold mt-1">
                                    {{ $chat->jasa->nama_jasa ?? '-' }}
                                </p>
                            </div>

                            <p class="text-xs text-slate-400">
                                {{ ($chat->waktu_kirim ?? $chat->created_at)->timezone('Asia/Jakarta')->format('H:i') }} WIB
                            </p>
                        </div>

                        <p class="text-sm text-slate-500 mt-3 line-clamp-2">
                            {{ $chat->pesan }}
                        </p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="p-10 text-center">
            <div class="text-4xl mb-3">💬</div>

            <h3 class="font-bold text-slate-900">
                Belum ada pesan
            </h3>

            <p class="text-sm text-slate-500 mt-2">
                Mulai chat dengan freelancer dari halaman detail jasa.
            </p>

            <a href="{{ route('customer.marketplace') }}"
                class="inline-block mt-5 px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                Cari Jasa Sekarang
            </a>
        </div>
        @endif
    </div>
</div>
<x-auto-refresh :seconds="10" />
@endsection