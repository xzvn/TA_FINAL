<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionMonitoringController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'admin', 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $request->validate([
            'status_transaksi' => ['nullable', 'string'],
            'status_escrow' => ['nullable', 'string'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $statusTransaksi = $request->status_transaksi;
        $statusEscrow = $request->status_escrow;

        $tanggalMulai = $request->filled('tanggal_mulai')
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : null;

        $tanggalSelesai = $request->filled('tanggal_selesai')
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : null;

        $applyPaymentFilter = function ($query) use ($statusTransaksi, $statusEscrow, $tanggalMulai, $tanggalSelesai) {
            if ($statusTransaksi) {
                $query->where('transaction_status', $statusTransaksi);
            }

            if ($statusEscrow) {
                $query->where('status_escrow', $statusEscrow);
            }

            if ($tanggalMulai && $tanggalSelesai) {
                $query->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai]);
            } elseif ($tanggalMulai) {
                $query->where('created_at', '>=', $tanggalMulai);
            } elseif ($tanggalSelesai) {
                $query->where('created_at', '<=', $tanggalSelesai);
            }
        };

        $pembayaranQuery = Pembayaran::query();
        $applyPaymentFilter($pembayaranQuery);

        $pesanans = Pesanan::with(['customer', 'freelancer', 'jasa', 'pembayaran'])
            ->whereHas('pembayaran', function ($query) use ($applyPaymentFilter) {
                $applyPaymentFilter($query);
            })
            ->latest()
            ->get();

        $totalEscrowVolume = (clone $pembayaranQuery)
            ->where('status_escrow', 'ditahan')
            ->sum('gross_amount');

        $totalNilaiTransaksi = (clone $pembayaranQuery)
            ->sum('gross_amount');

        $transaksiMenunggu = (clone $pembayaranQuery)
            ->where('status_escrow', 'ditahan')
            ->count();

        $totalTransaksi = (clone $pembayaranQuery)
            ->count();

        $tingkatKeamanan = 99.9;

        $weeklyLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

        $weeklyVolumes = collect($weeklyLabels)->map(function ($label, $index) {
            $date = now()->startOfWeek()->addDays($index);

            return [
                'label' => $label,
                'total' => Pembayaran::whereDate('created_at', $date)->sum('gross_amount'),
            ];
        });

        $maxWeeklyVolume = max($weeklyVolumes->max('total'), 1);

        return view('admin.transaction.index', compact(
            'pesanans',
            'totalEscrowVolume',
            'totalNilaiTransaksi',
            'transaksiMenunggu',
            'totalTransaksi',
            'tingkatKeamanan',
            'weeklyVolumes',
            'maxWeeklyVolume',
            'statusTransaksi',
            'statusEscrow',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    public function show(Request $request, Pesanan $pesanan): View
    {
        $this->authorizeAdmin($request);

        $pesanan->load([
            'customer',
            'freelancer',
            'jasa',
            'pembayaran',
            'progressPekerjaans',
            'hasilPekerjaan',
            'revisis',
            'review',
            'dispute',
        ]);

        return view('admin.transaction.show', compact('pesanan'));
    }
}
