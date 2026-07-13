@extends('layouts.admin')

@section('title', 'Pencairan Dana - Admin JasaKampus')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500">
                Admin / Pencairan Dana
            </p>

            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mt-1">
                Pengajuan Pencairan Dana
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Kelola pengajuan pencairan dana dari freelancer.
            </p>
        </div>
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

    {{-- SUMMARY --}}
    @php
    $totalPending = $withdrawals->where('status_withdrawal', 'pending')->count();
    $totalApproved = $withdrawals->where('status_withdrawal', 'approved')->count();
    $totalRejected = $withdrawals->where('status_withdrawal', 'rejected')->count();

    $nominalPending = $withdrawals->where('status_withdrawal', 'pending')->sum('jumlah_pencairan');
    $nominalApproved = $withdrawals->where('status_withdrawal', 'approved')->sum('jumlah_pencairan');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Pending</p>
            <h2 class="text-2xl font-bold text-yellow-600 mt-2">
                {{ $totalPending }}
            </h2>
            <p class="text-xs text-slate-400 mt-2">
                Rp {{ number_format($nominalPending, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Approved</p>
            <h2 class="text-2xl font-bold text-green-600 mt-2">
                {{ $totalApproved }}
            </h2>
            <p class="text-xs text-slate-400 mt-2">
                Rp {{ number_format($nominalApproved, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Rejected</p>
            <h2 class="text-2xl font-bold text-red-600 mt-2">
                {{ $totalRejected }}
            </h2>
            <p class="text-xs text-slate-400 mt-2">
                Pengajuan yang ditolak admin.
            </p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">
                Daftar Pengajuan
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Total {{ $withdrawals->count() }} pengajuan pencairan.
            </p>
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
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

                    {{-- INFO --}}
                    <div class="xl:col-span-7">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="font-bold text-slate-900">
                                Withdrawal #{{ $withdrawal->id }}
                            </h3>

                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                {{ strtoupper($withdrawal->status_withdrawal) }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-slate-500">Freelancer</p>
                                <p class="text-sm font-bold text-slate-900">
                                    {{ $withdrawal->freelancer->nama ?? '-' }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $withdrawal->freelancer->email ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-500">Nominal</p>
                                <p class="text-xl font-bold text-blue-600">
                                    Rp {{ number_format($withdrawal->jumlah_pencairan, 0, ',', '.') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-500">Bank</p>
                                <p class="text-sm font-bold text-slate-900">
                                    {{ $withdrawal->nama_bank }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-500">Rekening</p>
                                <p class="text-sm font-bold text-slate-900">
                                    {{ $withdrawal->nomor_rekening }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $withdrawal->nama_pemilik_rekening }}
                                </p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400 mt-4">
                            Diajukan: {{ $withdrawal->created_at->format('d M Y H:i') }}
                        </p>

                        @if ($withdrawal->tanggal_diproses)
                        <p class="text-xs text-slate-400 mt-1">
                            Diproses: {{ \Carbon\Carbon::parse($withdrawal->tanggal_diproses)->format('d M Y H:i') }}
                        </p>
                        @endif

                        @if ($withdrawal->catatan_admin)
                        <div class="mt-4 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <p class="text-xs text-slate-500">Catatan Admin</p>
                            <p class="text-sm text-slate-700 mt-1">
                                {{ $withdrawal->catatan_admin }}
                            </p>
                        </div>
                        @endif
                    </div>

                    {{-- ACTION --}}
                    <div class="xl:col-span-5">
                        @if ($withdrawal->status_withdrawal === 'pending')
                        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5">
                            <h4 class="font-bold text-slate-900">
                                Aksi Admin
                            </h4>

                            <form method="POST"
                                action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}"
                                class="mt-4 space-y-3">
                                @csrf

                                <textarea name="catatan_admin"
                                    rows="3"
                                    class="w-full rounded-xl border-slate-300 focus:border-green-500 focus:ring-green-500 text-sm"
                                    placeholder="Catatan opsional untuk approve..."></textarea>

                                <button type="submit"
                                    class="w-full px-5 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700">
                                    Approve Pencairan
                                </button>
                            </form>

                            <form method="POST"
                                action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}"
                                class="mt-4 space-y-3">
                                @csrf

                                <textarea name="catatan_admin"
                                    rows="3"
                                    class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500 text-sm"
                                    placeholder="Alasan penolakan..."
                                    required></textarea>

                                <button type="submit"
                                    class="w-full px-5 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700">
                                    Reject Pencairan
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5">
                            <p class="text-sm font-bold text-slate-900">
                                Pengajuan sudah diproses.
                            </p>

                            <p class="text-sm text-slate-500 mt-2">
                                Status saat ini:
                                <span class="font-bold">
                                    {{ strtoupper($withdrawal->status_withdrawal) }}
                                </span>
                            </p>
                        </div>
                        @endif
                    </div>

                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-10 text-center">
            <div class="text-4xl mb-3">🏦</div>

            <h3 class="font-bold text-slate-900">
                Belum ada pengajuan pencairan
            </h3>

            <p class="text-sm text-slate-500 mt-2">
                Pengajuan dari freelancer akan muncul di halaman ini.
            </p>
        </div>
        @endif
    </div>
</div>
<x-auto-refresh :seconds="30" />
@endsection