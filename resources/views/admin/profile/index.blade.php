@extends('layouts.admin')

@section('title', 'Profile Admin - JasaKampus')

@section('content')
@php
$admin = auth()->user();
@endphp

<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Profile Admin
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Informasi akun admin yang sedang digunakan.
        </p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
        <div class="flex flex-col md:flex-row md:items-center gap-6">
            <div class="w-24 h-24 rounded-full bg-blue-600 text-white flex items-center justify-center text-4xl font-bold">
                {{ strtoupper(substr($admin->nama ?? $admin->email, 0, 1)) }}
            </div>

            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    {{ $admin->nama ?? '-' }}
                </h2>

                <p class="text-slate-500 mt-1">
                    {{ $admin->email }}
                </p>

                <span class="inline-block mt-4 px-4 py-2 rounded-full bg-slate-100 text-slate-700 text-sm font-bold">
                    {{ strtoupper($admin->role) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-8">
            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-400 uppercase font-semibold">Nama</p>
                <p class="font-bold text-slate-900 mt-2">
                    {{ $admin->nama ?? '-' }}
                </p>
            </div>

            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-400 uppercase font-semibold">Email</p>
                <p class="font-bold text-slate-900 mt-2">
                    {{ $admin->email }}
                </p>
            </div>

            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-400 uppercase font-semibold">Role</p>
                <p class="font-bold text-slate-900 mt-2">
                    {{ strtoupper($admin->role) }}
                </p>
            </div>

            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-400 uppercase font-semibold">Terdaftar</p>
                <p class="font-bold text-slate-900 mt-2">
                    {{ $admin->created_at ? $admin->created_at->format('d M Y H:i') : '-' }}
                </p>
            </div>
        </div>
    </div>
    <x-theme-setting />
</div>
@endsection