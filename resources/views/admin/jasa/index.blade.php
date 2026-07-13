@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Kelola Jasa</h1>
        <p class="text-sm text-slate-500 mt-1">
            Admin dapat menyetujui atau menolak jasa yang dibuat freelancer.
        </p>
    </div>

    @if (session('success'))
    <div class="p-4 rounded-xl bg-green-100 text-green-700">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
            <div>
                <h3 class="text-lg font-bold text-slate-900">
                    Filter Jasa
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                    Filter jasa berdasarkan status, kategori, dan tanggal dibuat.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.jasa.index') }}"
                class="grid grid-cols-1 md:grid-cols-5 gap-3 w-full lg:w-auto">

                <select name="status_jasa"
                    class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status_jasa') === 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="active" {{ request('status_jasa') === 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="rejected" {{ request('status_jasa') === 'rejected' ? 'selected' : '' }}>
                        Rejected
                    </option>
                </select>

                <select name="kategori"
                    class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoriOptions as $option)
                    <option value="{{ $option }}" {{ request('kategori') === $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                    @endforeach
                </select>

                <input type="date"
                    name="tanggal_mulai"
                    value="{{ request('tanggal_mulai') }}"
                    class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                <input type="date"
                    name="tanggal_selesai"
                    value="{{ request('tanggal_selesai') }}"
                    class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700">
                        Filter
                    </button>

                    <a href="{{ route('admin.jasa.index') }}"
                        class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-700">
                {{ $jasas->count() }} jasa sesuai filter
            </p>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3 text-left">Jasa</th>
                    <th class="px-5 py-3 text-left">Freelancer</th>
                    <th class="px-5 py-3 text-left">Kategori</th>
                    <th class="px-5 py-3 text-left">Harga</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Tanggal Dibuat</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse ($jasas as $jasa)
                <tr>
                    <td class="px-5 py-4">
                        <p class="font-semibold text-slate-900">
                            {{ $jasa->nama_jasa }}
                        </p>
                        <p class="text-xs text-slate-500 line-clamp-1">
                            {{ $jasa->deskripsi }}
                        </p>
                    </td>

                    <td class="px-5 py-4">
                        {{ $jasa->freelancer->nama ?? '-' }}
                    </td>

                    <td class="px-5 py-4">
                        {{ $jasa->kategori }}
                    </td>

                    <td class="px-5 py-4">
                        Rp {{ number_format($jasa->harga, 0, ',', '.') }}
                    </td>

                    <td class="px-5 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if ($jasa->status_jasa === 'active') bg-green-100 text-green-700
                                @elseif ($jasa->status_jasa === 'pending') bg-yellow-100 text-yellow-700
                                @elseif ($jasa->status_jasa === 'rejected') bg-red-100 text-red-700
                                @else bg-slate-100 text-slate-700
                                @endif">
                            {{ ucfirst($jasa->status_jasa) }}
                        </span>
                    </td>

                    <td class="px-5 py-4 text-slate-600">
                        {{ $jasa->created_at?->format('d M Y H:i') ?? '-' }}
                    </td>

                    <td class="px-5 py-4">
                        <div class="flex justify-end gap-2">
                            @if ($jasa->status_jasa !== 'active')
                            <form method="POST" action="{{ route('admin.jasa.approve', $jasa->id) }}">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-2 rounded-lg bg-green-600 text-white text-xs font-semibold hover:bg-green-700">
                                    Approve
                                </button>
                            </form>
                            @endif

                            @if ($jasa->status_jasa !== 'rejected')
                            <form method="POST" action="{{ route('admin.jasa.reject', $jasa->id) }}">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-2 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700">
                                    Reject
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                        Tidak ada jasa sesuai filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-auto-refresh :seconds="30" />
@endsection