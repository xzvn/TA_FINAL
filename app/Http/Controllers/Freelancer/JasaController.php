<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Jasa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\NotifikasiService;
use App\Models\VerifikasiFreelancer;
use App\Services\CloudinaryService;

class JasaController extends Controller
{

    private function pastikanFreelancerTerverifikasi($user): void
    {
        $verifikasi = VerifikasiFreelancer::where('id_freelancer', $user->id)
            ->where('status_verifikasi', 'approved')
            ->first();

        abort_if(! $verifikasi, 403, 'Akun freelancer Anda belum diverifikasi admin.');
    }
    private function authorizeFreelancerApproved(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'freelancer', 403);

        abort_if(
            $user->verifikasiFreelancer?->status_verifikasi !== 'approved',
            403,
            'Akun freelancer Anda belum diverifikasi admin.'
        );
    }

    public function index(Request $request): View
    {
        $this->authorizeFreelancerApproved($request);

        $jasa = Jasa::where('id_freelancer', $request->user()->id)
            ->latest()
            ->get();

        return view('freelancer.jasa.index', compact('jasa'));
    }

    public function create(Request $request): View
    {
        $this->pastikanFreelancerTerverifikasi($request->user());

        $this->authorizeFreelancerApproved($request);

        return view('freelancer.jasa.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->pastikanFreelancerTerverifikasi($request->user());

        $this->authorizeFreelancerApproved($request);

        $request->validate([
            'nama_jasa' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string'],
            'harga' => ['required', 'numeric', 'min:1000'],
            'estimasi_pengerjaan' => ['required', 'string', 'max:100'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $emailFolder = str_replace(['@', '.'], '_', strtolower($request->user()->email));

            $thumbnailPath = CloudinaryService::uploadImage(
                $request->file('thumbnail'),
                'jasakampus/freelancer/' . $emailFolder . '/jasa'
            );
        }

        $jasa = Jasa::create([
            'id_freelancer' => $request->user()->id,
            'nama_jasa' => $request->nama_jasa,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'estimasi_pengerjaan' => $request->estimasi_pengerjaan,
            'thumbnail' => $thumbnailPath,
            'status_jasa' => 'pending',
        ]);

        NotifikasiService::kirimKeAdmin(
            'Pengajuan Jasa Baru',
            'Freelancer mengajukan jasa baru: "' . $jasa->nama_jasa . '". Silakan review pada menu Kelola Jasa.',
            'system',
            route('admin.jasa.index', [], false)
        );

        return redirect()
            ->route('freelancer.jasa.index')
            ->with('success', 'Jasa berhasil dibuat dan gambar tersimpan di Cloudinary.');
    }
}
