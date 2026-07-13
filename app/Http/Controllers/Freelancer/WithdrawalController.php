<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\NotifikasiService;


class WithdrawalController extends Controller
{
    private function authorizeFreelancer(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'freelancer', 403);
    }

    private function hitungSaldoTersedia(int $freelancerId): float
    {
        $saldoCair = Pesanan::where('id_freelancer', $freelancerId)
            ->where('status_pesanan', 'selesai')
            ->whereHas('pembayaran', function ($query) {
                $query->where('status_escrow', 'dicairkan');
            })
            ->sum('total_harga');

        $totalWithdrawalApproved = Withdrawal::where('id_freelancer', $freelancerId)
            ->where('status_withdrawal', 'approved')
            ->sum('jumlah_pencairan');

        $totalWithdrawalPending = Withdrawal::where('id_freelancer', $freelancerId)
            ->where('status_withdrawal', 'pending')
            ->sum('jumlah_pencairan');

        return max(0, $saldoCair - $totalWithdrawalApproved - $totalWithdrawalPending);
    }

    public function index(Request $request): View
    {
        $this->authorizeFreelancer($request);

        $freelancerId = $request->user()->id;

        $saldoTersedia = $this->hitungSaldoTersedia($freelancerId);

        $withdrawals = Withdrawal::where('id_freelancer', $freelancerId)
            ->latest()
            ->get();

        return view('freelancer.withdrawal.index', compact(
            'saldoTersedia',
            'withdrawals'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeFreelancer($request);

        $freelancerId = $request->user()->id;
        $saldoTersedia = $this->hitungSaldoTersedia($freelancerId);

        $request->validate([
            'jumlah_pencairan' => ['required', 'numeric', 'min:10000'],
            'nama_bank' => ['required', 'string', 'max:100'],
            'nomor_rekening' => ['required', 'string', 'max:100'],
            'nama_pemilik_rekening' => ['required', 'string', 'max:150'],
        ]);

        abort_if(
            $request->jumlah_pencairan > $saldoTersedia,
            403,
            'Jumlah pencairan melebihi saldo tersedia.'
        );

        Withdrawal::create([
            'id_freelancer' => $freelancerId,
            'jumlah_pencairan' => $request->jumlah_pencairan,
            'nama_bank' => $request->nama_bank,
            'nomor_rekening' => $request->nomor_rekening,
            'nama_pemilik_rekening' => $request->nama_pemilik_rekening,
            'status_withdrawal' => 'pending',
            'tanggal_pengajuan' => now(),
        ]);

        NotifikasiService::kirimKeAdmin(
            'Pengajuan Withdrawal Baru',
            'Freelancer mengajukan pencairan saldo. Silakan periksa menu withdrawal.',
            'withdrawal',
            route('admin.withdrawals.index', [], false)
        );

        return redirect()
            ->route('freelancer.withdrawals.index')
            ->with('success', 'Pengajuan pencairan berhasil dikirim. Menunggu persetujuan admin.');
    }
}
