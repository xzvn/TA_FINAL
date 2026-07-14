<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Jasa;
use App\Models\VerifikasiFreelancer;
use App\Services\CloudinaryService;
use App\Services\NotifikasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

    private function imageFolder(Request $request): string
    {
        $emailFolder = str_replace(
            ['@', '.', '+'],
            '_',
            strtolower((string) $request->user()->email)
        );

        return 'jasakampus/freelancer/' . $emailFolder . '/jasa';
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

        $data = $request->validate([
            'nama_jasa' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string'],
            'harga' => ['required', 'numeric', 'min:1000'],
            'estimasi_pengerjaan' => ['required', 'string', 'max:100'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $thumbnailUrl = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnailUrl = CloudinaryService::uploadImage(
                $request->file('thumbnail'),
                $this->imageFolder($request)
            );
        }

        $jasa = Jasa::create([
            'id_freelancer' => $request->user()->id,
            'nama_jasa' => $data['nama_jasa'],
            'kategori' => $data['kategori'],
            'deskripsi' => $data['deskripsi'],
            'harga' => $data['harga'],
            'estimasi_pengerjaan' => $data['estimasi_pengerjaan'],
            'thumbnail' => $thumbnailUrl,
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
            ->with(
                'success',
                $thumbnailUrl
                    ? 'Jasa berhasil dibuat dan gambar tersimpan di Cloudinary.'
                    : 'Jasa berhasil dibuat. Anda dapat menambahkan thumbnail melalui menu Ganti Gambar.'
            );
    }

    public function updateThumbnail(Request $request, Jasa $jasa): RedirectResponse
    {
        $this->pastikanFreelancerTerverifikasi($request->user());
        $this->authorizeFreelancerApproved($request);

        abort_if(
            (int) $jasa->id_freelancer !== (int) $request->user()->id,
            403,
            'Anda tidak memiliki akses untuk mengubah jasa ini.'
        );

        $request->validate([
            'thumbnail' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $thumbnailUrl = CloudinaryService::uploadImage(
            $request->file('thumbnail'),
            $this->imageFolder($request)
        );

        $jasa->update([
            'thumbnail' => $thumbnailUrl,
        ]);

        return redirect()
            ->route('freelancer.jasa.index')
            ->with('success', 'Thumbnail jasa berhasil diperbarui dan disimpan di Cloudinary.');
    }
}
