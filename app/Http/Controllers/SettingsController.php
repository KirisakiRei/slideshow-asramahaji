<?php

namespace App\Http\Controllers;

use App\Models\Setting;
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

    /**
     * Show the crop template ratios for each slideshow slot.
     */
    public function showCrop()
    {
        return view('admin.settings.crop', [
            'templates' => Setting::cropTemplates(),
        ]);
    }

    /**
     * Update the crop template ratios used as admin preview guides.
     */
    public function updateCrop(Request $request)
    {
        $validated = $request->validate([
            'main' => ['required', 'string', 'max:32', 'regex:/^\d+\s*:\s*\d+$/'],
            'facilities' => ['required', 'string', 'max:32', 'regex:/^\d+\s*:\s*\d+$/'],
            'next_event' => ['required', 'string', 'max:32', 'regex:/^\d+\s*:\s*\d+$/'],
        ], [
            'main.regex' => 'Format rasio harus berupa "lebar:tinggi", contoh 907:656.',
            'facilities.regex' => 'Format rasio harus berupa "lebar:tinggi", contoh 239:143.',
            'next_event.regex' => 'Format rasio harus berupa "lebar:tinggi", contoh 608:315.',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set('crop_template_'.$key, trim($value));
        }

        return redirect()->route('settings.crop')
            ->with('success', 'Template crop berhasil disimpan.');
    }
}
