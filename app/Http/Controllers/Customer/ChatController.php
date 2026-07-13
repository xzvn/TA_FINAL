<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Jasa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\CloudinaryService;


class ChatController extends Controller
{
    private function authorizeCustomer(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);
    }

    public function show(Request $request, Jasa $jasa): View
    {
        $this->authorizeCustomer($request);

        abort_if($jasa->status_jasa !== 'active', 404);

        $jasa->load('freelancer');

        $chats = Chat::with('pengirim')
            ->where('id_jasa', $jasa->id)
            ->where('id_customer', $request->user()->id)
            ->where('id_freelancer', $jasa->id_freelancer)
            ->orderBy('created_at')
            ->get();

        return view('customer.chat', compact('jasa', 'chats'));
    }

    public function messages(Request $request, Jasa $jasa)
    {
        $this->authorizeCustomer($request);

        $chats = Chat::with('pengirim')
            ->where('id_jasa', $jasa->id)
            ->where('id_customer', $request->user()->id)
            ->where('id_freelancer', $jasa->id_freelancer)
            ->orderBy('created_at')
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'pengirim_id' => $chat->pengirim_id,
                    'pengirim_nama' => $chat->pengirim->nama ?? 'User',
                    'pesan' => $chat->pesan,
                    'lampiran' => CloudinaryService::mediaUrl($chat->lampiran),
                    'waktu' => $chat->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json($chats);
    }

    public function store(Request $request, Jasa $jasa): RedirectResponse
    {
        $this->authorizeCustomer($request);

        abort_if($jasa->status_jasa !== 'active', 404);

        $request->validate([
            'pesan' => ['required', 'string', 'max:2000'],
            'lampiran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip', 'max:5120'],
        ]);

        $lampiranPath = null;

        if ($request->hasFile('lampiran')) {
            $emailFolder = str_replace(['@', '.'], '_', strtolower($request->user()->email));

            $lampiranPath = CloudinaryService::uploadFile(
                $request->file('lampiran'),
                'jasakampus/customer/' . $emailFolder . '/chat'
            );
        }

        Chat::create([
            'id_jasa' => $jasa->id,
            'id_customer' => $request->user()->id,
            'id_freelancer' => $jasa->id_freelancer,
            'pengirim_id' => $request->user()->id,
            'pesan' => $request->pesan,
            'lampiran' => $lampiranPath,
            'waktu_kirim' => now(),
        ]);


        return back()->with('success', 'Pesan berhasil dikirim.');
    }


    public function index(Request $request): View
    {
        $this->authorizeCustomer($request);

        $percakapans = Chat::with(['jasa', 'freelancer', 'pengirim'])
            ->where('id_customer', $request->user()->id)
            ->latest()
            ->get()
            ->unique(fn($chat) => $chat->id_jasa . '-' . $chat->id_freelancer)
            ->values();

        return view('customer.chat.index', compact('percakapans'));
    }
}
