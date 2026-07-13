<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananController extends Controller
{
    private function authorizeFreelancer(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'freelancer', 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeFreelancer($request);

        $request->validate([
            'status_pesanan' => ['nullable', 'in:dibayar,diproses,menunggu_approve,revisi,selesai,dispute,dibatalkan'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $statusPesanan = $request->status_pesanan;

        $tanggalMulai = $request->filled('tanggal_mulai')
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : null;

        $tanggalSelesai = $request->filled('tanggal_selesai')
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : null;

        $query = Pesanan::with(['customer', 'jasa', 'pembayaran', 'progressPekerjaans'])
            ->where('id_freelancer', $request->user()->id)
            ->whereIn('status_pesanan', [
                'dibayar',
                'diproses',
                'menunggu_approve',
                'revisi',
                'selesai',
                'dispute',
                'dibatalkan',
            ]);

        if ($statusPesanan) {
            $query->where('status_pesanan', $statusPesanan);
        }

        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('tanggal_pesan', [$tanggalMulai, $tanggalSelesai]);
        } elseif ($tanggalMulai) {
            $query->where('tanggal_pesan', '>=', $tanggalMulai);
        } elseif ($tanggalSelesai) {
            $query->where('tanggal_pesan', '<=', $tanggalSelesai);
        }

        $pesanans = $query
            ->latest('tanggal_pesan')
            ->get();

        return view('freelancer.pesanan.index', compact(
            'pesanans',
            'statusPesanan',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    public function show(Request $request, Pesanan $pesanan)
    {
        $this->authorizeFreelancer($request);

        abort_if($pesanan->id_freelancer !== $request->user()->id, 403);

        return redirect()->route('freelancer.progress.create', $pesanan->id);
    }
}
