<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $status = $request->query('status', 'semua');

        $baseQuery = Notifikasi::where('id_user', $userId);

        $totalCount = (clone $baseQuery)->count();
        $unreadCount = (clone $baseQuery)->where('dibaca', false)->count();
        $readCount = (clone $baseQuery)->where('dibaca', true)->count();

        $notifikasis = (clone $baseQuery)
            ->when($status === 'baru', function ($query) {
                $query->where('dibaca', false);
            })
            ->when($status === 'dibaca', function ($query) {
                $query->where('dibaca', true);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('notifikasi.index', compact(
            'notifikasis',
            'totalCount',
            'unreadCount',
            'readCount',
            'status'
        ));
    }

    public function read(Request $request, Notifikasi $notifikasi): RedirectResponse
    {
        abort_if($notifikasi->id_user !== $request->user()->id, 403);

        if (! $notifikasi->dibaca) {
            $notifikasi->update([
                'dibaca' => true,
                'dibaca_pada' => now(),
            ]);
        }

        if (! empty($notifikasi->url)) {
            return redirect($notifikasi->url);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function readAll(Request $request): RedirectResponse
    {
        Notifikasi::where('id_user', $request->user()->id)
            ->where('dibaca', false)
            ->update([
                'dibaca' => true,
                'dibaca_pada' => now(),
            ]);

        return back()->with('success', 'Semua notifikasi sudah dibaca.');
    }
}
