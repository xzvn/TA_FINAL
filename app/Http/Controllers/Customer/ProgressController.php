<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);

        $pesanans = Pesanan::with([
                'jasa.freelancer',
                'progressPekerjaans' => function ($query) {
                    $query->latest();
                },
                'hasilPekerjaan',
            ])
            ->where('id_customer', $user->id)
            ->whereIn('status_pesanan', [
                'dibayar',
                'diproses',
                'revisi',
                'menunggu_approve',
                'selesai',
            ])
            ->latest()
            ->get();

        return view('customer.progress.index', compact('pesanans'));
    }
}