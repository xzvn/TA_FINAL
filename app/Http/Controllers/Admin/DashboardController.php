<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Jasa;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\VerifikasiFreelancer;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'admin', 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $totalCustomer = User::where('role', 'customer')->count();
        $totalFreelancer = User::where('role', 'freelancer')->count();
        $totalAdmin = User::where('role', 'admin')->count();

        $freelancerPending = VerifikasiFreelancer::where('status_verifikasi', 'pending')->count();
        $freelancerApproved = VerifikasiFreelancer::where('status_verifikasi', 'approved')->count();
        $freelancerRejected = VerifikasiFreelancer::where('status_verifikasi', 'rejected')->count();

        $jasaPending = Jasa::where('status_jasa', 'pending')->count();
        $jasaActive = Jasa::where('status_jasa', 'active')->count();
        $jasaRejected = Jasa::where('status_jasa', 'rejected')->count();

        $totalPesanan = Pesanan::count();
        $pesananMenungguPembayaran = Pesanan::where('status_pesanan', 'menunggu_pembayaran')->count();
        $pesananDibayar = Pesanan::where('status_pesanan', 'dibayar')->count();
        $pesananDiproses = Pesanan::where('status_pesanan', 'diproses')->count();
        $pesananRevisi = Pesanan::where('status_pesanan', 'revisi')->count();
        $pesananMenungguApprove = Pesanan::where('status_pesanan', 'menunggu_approve')->count();
        $pesananSelesai = Pesanan::where('status_pesanan', 'selesai')->count();
        $pesananDispute = Pesanan::where('status_pesanan', 'dispute')->count();
        $pesananDibatalkan = Pesanan::where('status_pesanan', 'dibatalkan')->count();

        $totalTransaksi = Pembayaran::count();

        $escrowDitahan = Pembayaran::where('status_escrow', 'ditahan')
            ->sum('gross_amount');

        $escrowDicairkan = Pembayaran::where('status_escrow', 'dicairkan')
            ->sum('gross_amount');

        $escrowDikembalikan = Pembayaran::where('status_escrow', 'dikembalikan')
            ->sum('gross_amount');

        $withdrawalPending = Withdrawal::where('status_withdrawal', 'pending')->count();
        $withdrawalApproved = Withdrawal::where('status_withdrawal', 'approved')->count();
        $withdrawalRejected = Withdrawal::where('status_withdrawal', 'rejected')->count();

        $disputePending = Dispute::where('status_dispute', 'pending')->count();
        $disputeDiproses = Dispute::where('status_dispute', 'diproses')->count();
        $disputeSelesai = Dispute::whereIn('status_dispute', [
            'refund',
            'lanjutkan_pesanan',
            'ditolak',
            'selesai',
        ])->count();

        $pesananTerbaru = Pesanan::with(['customer', 'freelancer', 'jasa', 'pembayaran'])
            ->latest()
            ->take(5)
            ->get();

        $withdrawalTerbaru = Withdrawal::with('freelancer')
            ->latest()
            ->take(5)
            ->get();

        $disputeTerbaru = Dispute::with(['customer', 'freelancer', 'pesanan'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalCustomer',
            'totalFreelancer',
            'totalAdmin',
            'freelancerPending',
            'freelancerApproved',
            'freelancerRejected',
            'jasaPending',
            'jasaActive',
            'jasaRejected',
            'totalPesanan',
            'pesananMenungguPembayaran',
            'pesananDibayar',
            'pesananDiproses',
            'pesananRevisi',
            'pesananMenungguApprove',
            'pesananSelesai',
            'pesananDispute',
            'pesananDibatalkan',
            'totalTransaksi',
            'escrowDitahan',
            'escrowDicairkan',
            'escrowDikembalikan',
            'withdrawalPending',
            'withdrawalApproved',
            'withdrawalRejected',
            'disputePending',
            'disputeDiproses',
            'disputeSelesai',
            'pesananTerbaru',
            'withdrawalTerbaru',
            'disputeTerbaru'
        ));
    }
}
