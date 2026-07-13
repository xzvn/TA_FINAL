<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Jasa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\CloudinaryService;


class ChatController extends Controller
{
    private function authorizeFreelancer(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'freelancer', 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeFreelancer($request);

        $conversations = Chat::with(['customer', 'jasa'])
            ->where('id_freelancer', $request->user()->id)
            ->latest()
            ->get()
            ->unique(function ($chat) {
                return $chat->id_customer . '-' . $chat->id_jasa;
            });

        return view('freelancer.chat.index', compact('conversations'));
    }

    public function show(Request $request, Jasa $jasa, User $customer): View
    {
        $this->authorizeFreelancer($request);

        abort_if($jasa->id_freelancer !== $request->user()->id, 403);

        $chats = Chat::with('pengirim')
            ->where('id_jasa', $jasa->id)
            ->where('id_customer', $customer->id)
            ->where('id_freelancer', $request->user()->id)
            ->orderBy('created_at')
            ->get();

        return view('freelancer.chat.show', compact('jasa', 'customer', 'chats'));
    }

    public function store(Request $request, Jasa $jasa, User $customer): RedirectResponse
    {
        $this->authorizeFreelancer($request);

        abort_if($jasa->id_freelancer !== $request->user()->id, 403);

        $request->validate([
            'pesan' => ['required', 'string', 'max:2000'],
            'lampiran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip', 'max:5120'],
        ]);

        $lampiranPath = null;

        if ($request->hasFile('lampiran')) {
            $emailFolder = str_replace(['@', '.'], '_', strtolower($request->user()->email));

            $lampiranPath = CloudinaryService::uploadFile(
                $request->file('lampiran'),
                'jasakampus/freelancer/' . $emailFolder . '/chat'
            );
        }

        Chat::create([
            'id_jasa' => $jasa->id,
            'id_customer' => $customer->id,
            'id_freelancer' => $request->user()->id,
            'pengirim_id' => $request->user()->id,
            'pesan' => $request->pesan,
            'lampiran' => $lampiranPath,
            'waktu_kirim' => now(),
        ]);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }
}
