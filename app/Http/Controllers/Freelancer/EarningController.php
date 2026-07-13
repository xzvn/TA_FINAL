<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarningController extends Controller
{
    private function authorizeFreelancer(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'freelancer', 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeFreelancer($request);

        $freelancerId = $request->user()->id;

        $pesanans = Pesanan::with(['customer', 'jasa', 'pembayaran'])
            ->where('id_freelancer', $freelancerId)
            ->whereHas('pembayaran')
            ->latest()
            ->get();

        $saldoDitahan = $pesanans
            ->filter(fn($pesanan) => $pesanan->pembayaran?->status_escrow === 'ditahan')
            ->sum('total_harga');

        $saldoCair = $pesanans
            ->filter(fn($pesanan) => $pesanan->pembayaran?->status_escrow === 'dicairkan')
            ->sum('total_harga');

        $saldoRefund = $pesanans
            ->filter(fn($pesanan) => $pesanan->pembayaran?->status_escrow === 'dikembalikan')
            ->sum('total_harga');

        $totalPenghasilan = $saldoDitahan + $saldoCair;
        $totalPesananSelesai = $pesanans->where('status_pesanan', 'selesai')->count();

        $targetBulanan = 5000000;
        $persenTarget = $targetBulanan > 0
            ? min(100, round(($saldoCair / $targetBulanan) * 100))
            : 0;

        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
        $monthlyPendapatan = collect($bulanLabels)->map(function ($bulan, $index) use ($pesanans) {
            $monthNumber = $index + 1;

            return [
                'bulan' => $bulan,
                'total' => $pesanans
                    ->filter(function ($pesanan) use ($monthNumber) {
                        return $pesanan->created_at->month === $monthNumber
                            && $pesanan->pembayaran?->status_escrow === 'dicairkan';
                    })
                    ->sum('total_harga'),
            ];
        });

        $maxMonthly = max($monthlyPendapatan->max('total'), 1);

        return view('freelancer.earnings.index', compact(
            'pesanans',
            'saldoDitahan',
            'saldoCair',
            'saldoRefund',
            'totalPenghasilan',
            'totalPesananSelesai',
            'targetBulanan',
            'persenTarget',
            'monthlyPendapatan',
            'maxMonthly'
        ));
    }
}
