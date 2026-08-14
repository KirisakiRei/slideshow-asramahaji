@extends('layouts.admin')

@section('title', 'Header & Title')
@section('page-title', 'Header & Title Display')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Display Content'],
        ['label' => 'Header & Title'],
    ]" />

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Header & Title Display</h2>
            <p class="mt-1 text-sm text-slate-500">Atur label kecil, judul besar, dan logo yang tampil di bagian atas display.</p>
        </div>
        <a href="{{ route('display.all') }}" target="_blank"
           class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Buka Display
        </a>
    </div>

    <form method="POST" action="{{ route('signage.header.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-5 pb-3 border-b border-slate-100">Konten Header</h3>

                    <div class="mb-6">
                        <label for="eyebrow" class="block text-sm font-medium text-slate-700 mb-2">Label Kecil (Eyebrow)
                            <x-field-tip text="Label pendek di atas judul, contohnya 'Event Saat Ini'. Cukup 2-3 kata." />
                        </label>
                        <input type="text" id="eyebrow" name="eyebrow" maxlength="100"
                               value="{{ $config['eyebrow'] }}"
                               oninput="updatePreview()"
                               placeholder="Contoh: Event Saat Ini"
                               class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        <p class="mt-1.5 text-xs text-slate-500">Label pendek, ditampilkan sebagai pill di atas judul.</p>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-2">Judul Event Utama
                            <x-field-tip text="Judul besar yang paling menonjol di layar. Gunakan nama event yang jelas." />
                        </label>
                        <input type="text" id="title" name="title" maxlength="255"
                               value="{{ $config['title'] }}"
                               oninput="updatePreview()"
                               placeholder="Contoh: Seminar Nasional Teknologi"
                               class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        <p class="mt-1.5 text-xs text-slate-500">Judul besar yang tampil dominan di header display.</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-5 pb-3 border-b border-slate-100">Logo & Teks Institusi
                        <x-field-tip text="Logo di kanan atas display, teks institusi tampil di sebelah kanannya." />
                    </h3>
                    @if(!empty($config['logo']))
                        <div class="flex items-center gap-3 mb-4 p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <img src="{{ asset('storage/' . $config['logo']) }}" alt="Logo" style="height:64px;width:auto;max-width:160px;object-fit:contain;display:block;">
                            <span class="text-xs text-slate-500 flex-1">Logo saat ini</span>
                            <button type="button" onclick="document.getElementById('remove-logo-form').submit()"
                                    class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-700 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus Logo
                            </button>
                        </div>
                    @endif
                    <div class="mb-5">
                        <label for="logo" class="block text-sm font-medium text-slate-700 mb-2">File Logo</label>
                        <input type="file" id="logo" name="logo" accept="image/*"
                               class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        <p class="mt-1.5 text-xs text-slate-500">Format: PNG, JPG, SVG, WEBP &middot; Maks 5MB &middot; Disarankan PNG transparan.</p>
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="logo_text" class="block text-sm font-medium text-slate-700 mb-2">Teks di Sebelah Logo
                            <x-field-tip text="Nama institusi di kanan logo. Tekan Enter untuk baris baru, contoh: ASRAMA HAJI lalu MEDAN." />
                        </label>
                        <textarea id="logo_text" name="logo_text" maxlength="200" rows="3"
                                  oninput="updatePreview()"
                                  placeholder="Contoh:&#10;ASRAMA HAJI&#10;MEDAN"
                                  class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent resize-y">{{ old('logo_text', $config['logo_text'] ?? '') }}</textarea>
                        <p class="mt-1.5 text-xs text-slate-500">Tekan Enter untuk baris baru. Kosongkan jika hanya ingin menampilkan logo tanpa teks.</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 lg:sticky lg:top-20">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">Preview Header</p>
                    <div class="rounded-xl overflow-hidden border border-slate-200 p-4" style="background: linear-gradient(180deg, #faf7f1 0%, #f7f1e8 65%, #f4ecdf 100%);">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <span id="preview-eyebrow" class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wide text-white"
                                      style="background: linear-gradient(135deg, #c3a27a, #ab8456);">{{ $config['eyebrow'] ?: 'Event Saat Ini' }}</span>
                                <p id="preview-title" class="mt-2 text-slate-800 font-bold leading-tight text-base">{{ $config['title'] ?: 'Event Title' }}</p>
                                <span class="block mt-2 h-1 w-12 rounded-full" style="background: linear-gradient(90deg, #b58b57, #d9c09d);"></span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 max-w-[48%]">
                                @if(!empty($config['logo']))
                                    <img src="{{ asset('storage/' . $config['logo']) }}" alt="Logo" style="width:40px;height:40px;object-fit:contain;flex-shrink:0;">
                                @else
                                    <div class="w-10 h-10 flex items-center justify-center text-[9px] font-semibold text-slate-400 shrink-0">LOGO</div>
                                @endif
                                <span id="preview-logo-text" class="text-[10px] font-bold uppercase leading-tight text-slate-800 whitespace-pre-line {{ empty(trim($config['logo_text'] ?? '')) ? 'hidden' : '' }}">{{ $config['logo_text'] ?? '' }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="mt-5 w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </form>

    @if(!empty($config['logo']))
        <form id="remove-logo-form" method="POST" action="{{ route('signage.header.logo.remove') }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif

    <script>
        function updatePreview() {
            const eyebrow = document.getElementById('eyebrow').value;
            const title = document.getElementById('title').value;
            const logoText = document.getElementById('logo_text').value.trim();
            const previewLogoText = document.getElementById('preview-logo-text');

            document.getElementById('preview-eyebrow').textContent = eyebrow || 'Event Saat Ini';
            document.getElementById('preview-title').textContent = title || 'Event Title';
            previewLogoText.textContent = logoText;
            previewLogoText.classList.toggle('hidden', logoText === '');
        }
    </script>
@endsection
