<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\NotifikasiService;

class WithdrawalController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'admin', 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $withdrawals = Withdrawal::with('freelancer')
            ->latest()
            ->get();

        return view('admin.withdrawal.index', compact('withdrawals'));
    }

    public function approve(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $this->authorizeAdmin($request);

        abort_if(
            $withdrawal->status_withdrawal !== 'pending',
            403,
            'Pengajuan pencairan ini sudah diproses.'
        );

        $withdrawal->update([
            'status_withdrawal' => 'approved',
            'catatan_admin' => $request->catatan_admin,
            'tanggal_diproses' => now(),
        ]);

        NotifikasiService::kirim(
            $withdrawal->id_freelancer,
            'Withdrawal Disetujui',
            'Pengajuan pencairan saldo kamu telah disetujui oleh admin.',
            'withdrawal',
            route('freelancer.withdrawals.index', [], false)
        );

        return redirect()
            ->route('admin.withdrawals.index')
            ->with('success', 'Pengajuan pencairan berhasil disetujui.');
    }

    public function reject(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $this->authorizeAdmin($request);

        abort_if(
            $withdrawal->status_withdrawal !== 'pending',
            403,
            'Pengajuan pencairan ini sudah diproses.'
        );

        $request->validate([
            'catatan_admin' => ['required', 'string', 'min:5'],
        ]);

        $withdrawal->update([
            'status_withdrawal' => 'rejected',
            'catatan_admin' => $request->catatan_admin,
            'tanggal_diproses' => now(),
        ]);

        NotifikasiService::kirim(
            $withdrawal->id_freelancer,
            'Withdrawal Ditolak',
            'Pengajuan pencairan saldo kamu ditolak oleh admin. Silakan cek catatan admin.',
            'withdrawal',
            route('freelancer.withdrawals.index', [], false)
        );

        return redirect()
            ->route('admin.withdrawals.index')
            ->with('success', 'Pengajuan pencairan berhasil ditolak.');
    }
}
