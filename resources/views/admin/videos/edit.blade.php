@extends('layouts.admin')

@section('title', 'Edit Video')
@section('page-title', 'Edit Video')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Videos', 'url' => route('videos.index')],
        ['label' => 'Edit: ' . $video->title],
    ]" />

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Edit Video</h2>
        <p class="mt-1 text-sm text-slate-500">Perbarui judul dan status video.</p>
    </div>

    <form method="POST" action="{{ route('videos.update', $video) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Preview (larger) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-4 pb-3 border-b border-slate-100">Preview Video</h3>
                    <div class="bg-slate-900 rounded-lg overflow-hidden flex items-center justify-center" style="height: 420px;">
                        <video src="{{ asset('storage/' . $video->file_path) }}" controls
                               style="max-width:100%;max-height:100%;width:auto;height:auto;border-radius:6px;display:block;"></video>
                    </div>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 lg:sticky lg:top-20">
                    <h3 class="text-base font-semibold text-slate-800 mb-4">Detail Video</h3>

                    <div class="mb-5">
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">
                            Judul <span class="text-red-500">*</span>
                            <x-field-tip text="Nama yang tampil di galeri dan preview. Jika dikosongkan, nama file akan digunakan secara otomatis." />
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title', $video->title) }}"
                               maxlength="255" required
                               class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors mb-5">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $video->is_active) ? 'checked' : '' }}
                               class="mt-0.5 rounded border-slate-300 text-green-600 focus:ring-green-500">
                        <span>
                            <span class="block text-sm font-medium text-slate-700">Active</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Video aktif dapat ditampilkan di slideshow.</span>
                        </span>
                    </label>

                    <div class="text-xs text-slate-500 mb-5 space-y-1 pb-5 border-b border-slate-100">
                        <div class="flex justify-between"><span>Diupload</span><span class="font-medium text-slate-700">{{ $video->created_at->format('d M Y') }}</span></div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('videos.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
