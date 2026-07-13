@extends('layouts.freelancer')

@section('title', 'Pencairan Dana - JasaKampus')
@section('page-title', 'Pencairan Dana')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                Pencairan Dana
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Ajukan pencairan dana dari saldo yang sudah tersedia.
            </p>
        </div>

        <a href="{{ route('freelancer.earnings.index') }}"
            class="px-5 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50">
            Kembali ke Earnings
        </a>
    </div>

    @if (session('success'))
    <div class="px-5 py-4 bg-green-100 border border-green-200 text-green-700 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="px-5 py-4 bg-red-100 border border-red-200 text-red-700 rounded-xl">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        <div class="xl:col-span-4 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <p class="text-sm text-slate-500">
                    Saldo Tersedia
                </p>

                <h2 class="text-3xl font-bold text-green-600 mt-2">
                    Rp {{ number_format($saldoTersedia, 0, ',', '.') }}
                </h2>

                <p class="text-xs text-slate-400 mt-2">
                    Saldo tersedia sudah dikurangi pencairan pending dan approved.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-900">
                    Ajukan Pencairan
                </h3>

                <form method="POST"
                    action="{{ route('freelancer.withdrawals.store') }}"
                    class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Jumlah Pencairan
                        </label>

                        <input type="number"
                            name="jumlah_pencairan"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: 50000"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Nama Bank
                        </label>

                        <input type="text"
                            name="nama_bank"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="BCA / BRI / Mandiri"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Nomor Rekening
                        </label>

                        <input type="text"
                            name="nomor_rekening"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Nama Pemilik Rekening
                        </label>

                        <input type="text"
                            name="nama_pemilik_rekening"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <button type="submit"
                        class="w-full px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                        Ajukan Pencairan
                    </button>
                </form>
            </div>
        </div>

        <div class="xl:col-span-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">
                        Riwayat Pencairan
                    </h3>
                </div>

                @if ($withdrawals->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach ($withdrawals as $withdrawal)
                    @php
                    $statusClass = match ($withdrawal->status_withdrawal) {
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'approved' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    default => 'bg-slate-100 text-slate-700',
                    };
                    @endphp

                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-3">
                                    <h4 class="font-bold text-slate-900">
                                        Withdrawal #{{ $withdrawal->id }}
                                    </h4>

                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                        {{ strtoupper($withdrawal->status_withdrawal) }}
                                    </span>
                                </div>

                                <p class="text-sm text-slate-500 mt-2">
                                    {{ $withdrawal->nama_bank }} -
                                    {{ $withdrawal->nomor_rekening }}
                                </p>

                                <p class="text-sm text-slate-500 mt-1">
                                    Pemilik: {{ $withdrawal->nama_pemilik_rekening }}
                                </p>

                                @if ($withdrawal->catatan_admin)
                                <p class="text-sm text-slate-500 mt-1">
                                    Catatan admin: {{ $withdrawal->catatan_admin }}
                                </p>
                                @endif
                            </div>

                            <div class="lg:text-right">
                                <p class="text-xl font-bold text-blue-600">
                                    Rp {{ number_format($withdrawal->jumlah_pencairan, 0, ',', '.') }}
                                </p>

                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $withdrawal->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-10 text-center">
                    <div class="text-4xl mb-3">🏦</div>

                    <h3 class="font-bold text-slate-900">
                        Belum ada pencairan
                    </h3>

                    <p class="text-sm text-slate-500 mt-2">
                        Riwayat pencairan akan muncul setelah kamu mengajukan pencairan.
                    </p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection