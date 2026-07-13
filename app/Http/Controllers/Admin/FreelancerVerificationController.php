<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerifikasiFreelancer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\NotifikasiService;
use Carbon\Carbon;

class FreelancerVerificationController extends Controller
{
    private function authorizeAdmin(): void
    {
        $user = Auth::user();

        abort_if(! $user || $user->role !== 'admin', 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        $request->validate([
            'status_verifikasi' => ['nullable', 'in:pending,approved,rejected'],
            'universitas' => ['nullable', 'string', 'max:255'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $statusVerifikasi = $request->status_verifikasi;
        $universitas = $request->universitas;

        $tanggalMulai = $request->filled('tanggal_mulai')
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : null;

        $tanggalSelesai = $request->filled('tanggal_selesai')
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : null;

        $query = VerifikasiFreelancer::with('freelancer.portofolios');

        if ($statusVerifikasi) {
            $query->where('status_verifikasi', $statusVerifikasi);
        }

        if ($universitas) {
            $query->where('universitas', $universitas);
        }

        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('tanggal_pengajuan', [$tanggalMulai, $tanggalSelesai]);
        } elseif ($tanggalMulai) {
            $query->where('tanggal_pengajuan', '>=', $tanggalMulai);
        } elseif ($tanggalSelesai) {
            $query->where('tanggal_pengajuan', '<=', $tanggalSelesai);
        }

        $verifikasis = $query
            ->latest('tanggal_pengajuan')
            ->get();

        $universitasOptions = VerifikasiFreelancer::whereNotNull('universitas')
            ->select('universitas')
            ->distinct()
            ->orderBy('universitas')
            ->pluck('universitas');

        return view('admin.verifikasi-freelancer', compact(
            'verifikasis',
            'universitasOptions',
            'statusVerifikasi',
            'universitas',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    public function approve(VerifikasiFreelancer $verifikasi): RedirectResponse
    {
        $this->authorizeAdmin();

        $verifikasi->update([
            'status_verifikasi' => 'approved',
            'tanggal_verifikasi' => now(),
            'catatan_admin' => null,
        ]);

        NotifikasiService::kirim(
            $verifikasi->id_freelancer,
            'Verifikasi Freelancer Disetujui',
            'Akun freelancer kamu telah disetujui oleh admin. Sekarang kamu dapat membuat dan menawarkan jasa.',
            'system',
            route('freelancer.jasa.index', [], false)
        );

        return back()->with('success', 'Freelancer berhasil disetujui.');
    }

    public function reject(Request $request, VerifikasiFreelancer $verifikasi): RedirectResponse
    {
        $this->authorizeAdmin();

        $request->validate([
            'catatan_admin' => ['required', 'string', 'max:1000'],
        ]);

        $verifikasi->update([
            'status_verifikasi' => 'rejected',
            'tanggal_verifikasi' => now(),
            'catatan_admin' => $request->catatan_admin,
        ]);

        NotifikasiService::kirim(
            $verifikasi->id_freelancer,
            'Verifikasi Freelancer Ditolak',
            'Verifikasi akun freelancer kamu ditolak oleh admin. Silakan cek catatan admin dan ajukan ulang jika diperlukan.',
            'system',
            null
        );

        return back()->with('success', 'Freelancer berhasil ditolak.');
    }
}
