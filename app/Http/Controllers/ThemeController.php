<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'theme' => ['required', 'in:light,dark'],
        ]);

        $user = $request->user();

        $user->update([
            'theme' => $request->theme,
        ]);

        return back()->with('success', 'Tema tampilan berhasil diperbarui.');
    }
}
