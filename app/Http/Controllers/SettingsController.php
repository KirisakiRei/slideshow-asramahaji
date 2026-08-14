<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show the change password form.
     */
    public function showPassword()
    {
        return view('admin.settings.password');
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:128', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.max' => 'Password baru maksimal 128 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()->route('settings.password')
            ->with('success', 'Password berhasil diubah. Gunakan password baru pada login berikutnya.');
    }
}
