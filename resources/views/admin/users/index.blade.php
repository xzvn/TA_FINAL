@extends('layouts.admin')

@section('title', 'Kelola Users - JasaKampus')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Kelola Users
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Daftar seluruh pengguna yang terdaftar di platform JasaKampus.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs text-slate-400 uppercase font-semibold">Total User</p>
            <h3 class="text-3xl font-bold text-slate-900 mt-2">
                {{ $users->count() }}
            </h3>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs text-slate-400 uppercase font-semibold">Customer</p>
            <h3 class="text-3xl font-bold text-blue-600 mt-2">
                {{ $users->where('role', 'customer')->count() }}
            </h3>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs text-slate-400 uppercase font-semibold">Freelancer</p>
            <h3 class="text-3xl font-bold text-purple-600 mt-2">
                {{ $users->where('role', 'freelancer')->count() }}
            </h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="font-bold text-slate-900">
                Daftar User
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Total {{ $users->count() }} user terdaftar.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">Nama</th>
                        <th class="px-6 py-4 text-left font-semibold">Email</th>
                        <th class="px-6 py-4 text-left font-semibold">Role</th>
                        <th class="px-6 py-4 text-left font-semibold">Tanggal Daftar</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($user->nama ?? $user->email, 0, 1)) }}
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-800">
                                        {{ $user->nama ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-slate-700">
                            {{ $user->email }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                    @if ($user->role === 'admin') bg-slate-200 text-slate-700
                                    @elseif ($user->role === 'freelancer') bg-purple-100 text-purple-700
                                    @else bg-blue-100 text-blue-700
                                    @endif">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            {{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                            Belum ada user terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection