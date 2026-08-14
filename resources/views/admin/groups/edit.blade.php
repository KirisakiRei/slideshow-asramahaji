@extends('layouts.admin')

@section('title', 'Edit Grup Slideshow')
@section('page-title', 'Edit Grup Slideshow')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Grup Slideshow', 'url' => route('photo-groups.index')],
        ['label' => 'Edit: ' . $group->name],
    ]" />

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Edit Grup Slideshow</h2>
        <p class="mt-1 text-sm text-slate-500">Perbarui informasi grup "{{ $group->name }}".</p>
    </div>

    <form method="POST" action="{{ route('photo-groups.update', $group) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main column -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-7">
                    <h3 class="text-base font-semibold text-slate-800 mb-5 pb-4 border-b border-slate-100">Informasi Grup</h3>

                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Nama Grup <span class="text-red-500">*</span>
                            <x-field-tip text="Beri nama yang mudah dikenali, misalnya 'Foto Gedung A' atau 'Seminar 2026'. Nama ini akan digunakan saat memilih grup di halaman display." />
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}"
                               class="w-full px-3 py-2.5 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 @error('name') border-red-500 @enderror"
                               placeholder="Masukkan nama grup" required maxlength="255">
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi
                            <x-field-tip text="Catatan internal tim saja, tidak tampil di layar display." />
                        </label>
                        <textarea name="description" id="description" rows="5"
                                  class="w-full px-3 py-2.5 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 @error('description') border-red-500 @enderror"
                                  placeholder="Deskripsi grup (opsional)" maxlength="1000">{{ old('description', $group->description) }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-7">
                    <h3 class="text-base font-semibold text-slate-800 mb-5 pb-4 border-b border-slate-100">Pengaturan Tampilan</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-7">
                        <div>
                            <label for="slide_duration" class="block text-sm font-medium text-slate-700 mb-1.5">Durasi Slide (detik)
                                <x-field-tip text="Lama setiap media tampil sebelum berganti. 5-8 detik biasanya paling nyaman ditonton." />
                            </label>
                            <input type="number" name="slide_duration" id="slide_duration" value="{{ old('slide_duration', $group->slide_duration) }}"
                                   class="w-full px-3 py-2.5 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 @error('slide_duration') border-red-500 @enderror"
                                   min="1" max="60">
                            <p class="mt-1.5 text-xs text-slate-500">Durasi tampil setiap foto (1-60 detik).</p>
                            @error('slide_duration')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="transition_type" class="block text-sm font-medium text-slate-700 mb-1.5">Animasi Transisi
                                <x-field-tip text="Gaya perpindahan antar media. Fade paling halus; slide dan zoom memberi kesan lebih hidup." />
                            </label>
                            <select name="transition_type" id="transition_type"
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 @error('transition_type') border-red-500 @enderror">
                                <option value="fade" {{ old('transition_type', $group->transition_type) === 'fade' ? 'selected' : '' }}>Fade (Pudar)</option>
                                <option value="slide-left" {{ old('transition_type', $group->transition_type) === 'slide-left' ? 'selected' : '' }}>Slide Kiri</option>
                                <option value="slide-right" {{ old('transition_type', $group->transition_type) === 'slide-right' ? 'selected' : '' }}>Slide Kanan</option>
                                <option value="slide-up" {{ old('transition_type', $group->transition_type) === 'slide-up' ? 'selected' : '' }}>Slide Atas</option>
                                <option value="slide-down" {{ old('transition_type', $group->transition_type) === 'slide-down' ? 'selected' : '' }}>Slide Bawah</option>
                                <option value="zoom-in" {{ old('transition_type', $group->transition_type) === 'zoom-in' ? 'selected' : '' }}>Zoom In</option>
                                <option value="zoom-out" {{ old('transition_type', $group->transition_type) === 'zoom-out' ? 'selected' : '' }}>Zoom Out</option>
                            </select>
                            <p class="mt-1.5 text-xs text-slate-500">Efek animasi saat pergantian.</p>
                            @error('transition_type')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fill_mode" class="block text-sm font-medium text-slate-700 mb-1.5">Cara Tampil Media
                                <x-field-tip text="Crop mengisi seluruh area (bagian luar foto terpotong, titik fokus foto menentukan bagian yang dipertahankan). Tampil Utuh menampilkan foto lengkap dengan latar foto blur di belakangnya." />
                            </label>
                            <select name="fill_mode" id="fill_mode"
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 @error('fill_mode') border-red-500 @enderror">
                                <option value="cover" {{ old('fill_mode', $group->fill_mode) === 'cover' ? 'selected' : '' }}>Crop (isi layar)</option>
                                <option value="contain" {{ old('fill_mode', $group->fill_mode) === 'contain' ? 'selected' : '' }}>Tampil Utuh</option>
                            </select>
                            <p class="mt-1.5 text-xs text-slate-500">Berlaku untuk foto. Video selalu tampil utuh.</p>
                            @error('fill_mode')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-slate-700 mb-1.5">Urutan
                                <x-field-tip text="Posisi grup di daftar slideshow. Angka lebih kecil tampil lebih dulu." />
                            </label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $group->sort_order) }}"
                                   class="w-full px-3 py-2.5 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 @error('sort_order') border-red-500 @enderror"
                                   min="0" max="999">
                            <p class="mt-1.5 text-xs text-slate-500">0-999, semakin kecil semakin awal.</p>
                            @error('sort_order')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar column -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-7 lg:sticky lg:top-20">
                    <h3 class="text-base font-semibold text-slate-800 mb-5">Publikasi</h3>

                    <label class="flex items-start gap-3 p-3.5 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors mb-5">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $group->is_active) ? 'checked' : '' }}
                               class="mt-0.5 rounded border-slate-300 text-green-600 focus:ring-green-500">
                        <span>
                            <span class="block text-sm font-medium text-slate-700">Aktifkan Grup</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Grup aktif akan tampil di slideshow display.</span>
                        </span>
                    </label>

                    <div class="text-xs text-slate-500 mb-5 space-y-2">
                        <div class="flex justify-between"><span>Jumlah media</span><span class="font-medium text-slate-700">{{ $group->items()->count() }}</span></div>
                        <div class="flex justify-between"><span>Dibuat</span><span class="font-medium text-slate-700">{{ $group->created_at->format('d M Y') }}</span></div>
                    </div>

                    <div class="flex flex-col gap-2.5 pt-5 border-t border-slate-100">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Perbarui Grup
                        </button>
                        <a href="{{ route('group-items.index', $group) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-50 text-blue-700 text-sm font-medium rounded-md hover:bg-blue-100 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Kelola Media
                        </a>
                        <a href="{{ route('photo-groups.index') }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
