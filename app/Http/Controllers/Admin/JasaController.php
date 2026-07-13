<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jasa;
use App\Services\NotifikasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class JasaController extends Controller
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
            'status_jasa' => ['nullable', 'in:pending,active,rejected'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $statusJasa = $request->status_jasa;
        $kategori = $request->kategori;

        $tanggalMulai = $request->filled('tanggal_mulai')
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : null;

        $tanggalSelesai = $request->filled('tanggal_selesai')
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : null;

        $query = Jasa::with('freelancer');

        if ($statusJasa) {
            $query->where('status_jasa', $statusJasa);
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai]);
        } elseif ($tanggalMulai) {
            $query->where('created_at', '>=', $tanggalMulai);
        } elseif ($tanggalSelesai) {
            $query->where('created_at', '<=', $tanggalSelesai);
        }

        $jasas = $query
            ->latest('created_at')
            ->get();

        $kategoriOptions = Jasa::query()
            ->whereNotNull('kategori')
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('admin.jasa.index', compact(
            'jasas',
            'kategoriOptions',
            'statusJasa',
            'kategori',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    public function approve(Request $request, Jasa $jasa): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $jasa->update([
            'status_jasa' => 'active',
        ]);

        NotifikasiService::kirim(
            $jasa->id_freelancer,
            'Jasa Disetujui',
            'Jasa "' . $jasa->nama_jasa . '" telah disetujui oleh admin dan sekarang tampil di marketplace.',
            'system',
            route('freelancer.jasa.index', [], false)
        );

        return back()->with('success', 'Jasa berhasil disetujui.');
    }

    public function reject(Request $request, Jasa $jasa): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $jasa->update([
            'status_jasa' => 'rejected',
        ]);

        NotifikasiService::kirim(
            $jasa->id_freelancer,
            'Jasa Ditolak',
            'Jasa "' . $jasa->nama_jasa . '" ditolak oleh admin. Silakan periksa kembali data jasa kamu.',
            'system',
            route('freelancer.jasa.index', [], false)
        );

        return back()->with('success', 'Jasa berhasil ditolak.');
    }
}
