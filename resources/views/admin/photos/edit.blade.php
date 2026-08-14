@extends('layouts.admin')

@section('title', 'Edit Foto')
@section('page-title', 'Edit Foto')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Photos', 'url' => route('photos.index')],
        ['label' => 'Edit: ' . $photo->title],
    ]" />

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Edit Photo</h2>
        <p class="mt-1 text-sm text-slate-500">Perbarui judul dan status foto.</p>
    </div>

    <form method="POST" action="{{ route('photos.update', $photo) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="focus_x" id="focus_x" value="{{ old('focus_x', $photo->focus_x ?? 50) }}">
        <input type="hidden" name="focus_y" id="focus_y" value="{{ old('focus_y', $photo->focus_y ?? 50) }}">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Preview (larger) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-4 pb-3 border-b border-slate-100">Preview Foto</h3>
                    <div class="bg-slate-900 rounded-lg overflow-hidden" style="height: 420px;">
                        @if($photo->isPhoto())
                            <div id="focus-wrap" class="relative w-full h-full flex items-center justify-center cursor-crosshair">
                                <img id="focus-img" src="{{ asset('storage/' . $photo->file_path) }}"
                                     alt="{{ $photo->title }}"
                                     style="max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;display:block;">
                                <div id="focus-marker" class="absolute pointer-events-none"
                                     style="width:22px;height:22px;border:2px solid #fff;border-radius:50%;box-shadow:0 0 0 2px rgba(0,0,0,.6);transform:translate(-50%,-50%);"></div>
                            </div>
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <img src="{{ asset('storage/' . $photo->file_path) }}"
                                     alt="{{ $photo->title }}"
                                     style="max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;display:block;">
                            </div>
                        @endif
                    </div>
                    @if($photo->isPhoto())
                        <p class="mt-3 text-xs text-slate-500">Klik pada foto untuk menetapkan <strong>titik fokus</strong> — bagian ini dipertahankan saat foto di-crop agar mengisi layar.</p>
                        <button type="button" id="focus-reset" class="mt-2 text-xs font-medium text-slate-600 underline hover:text-slate-900">Reset ke tengah</button>
                    @endif
                </div>
            </div>

            <!-- Right: Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 lg:sticky lg:top-20">
                    <h3 class="text-base font-semibold text-slate-800 mb-4">Detail Foto</h3>

                    <div class="mb-5">
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">
                            Judul <span class="text-red-500">*</span>
                            <x-field-tip text="Nama yang tampil di galeri dan preview. Jika dikosongkan, nama file akan digunakan secara otomatis." />
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title', $photo->title) }}"
                               maxlength="255" required
                               class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors mb-5">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $photo->is_active) ? 'checked' : '' }}
                               class="mt-0.5 rounded border-slate-300 text-green-600 focus:ring-green-500">
                        <span>
                            <span class="block text-sm font-medium text-slate-700">Active</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Foto aktif dapat ditampilkan di slideshow.</span>
                        </span>
                    </label>

                    <div class="text-xs text-slate-500 mb-5 space-y-1 pb-5 border-b border-slate-100">
                        <div class="flex justify-between"><span>Diupload</span><span class="font-medium text-slate-700">{{ $photo->created_at->format('d M Y') }}</span></div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('photos.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @if($photo->isPhoto())
    <script>
        (function () {
            const wrap = document.getElementById('focus-wrap');
            const img = document.getElementById('focus-img');
            const marker = document.getElementById('focus-marker');
            const inputX = document.getElementById('focus_x');
            const inputY = document.getElementById('focus_y');
            if (!wrap || !img || !marker || !inputX || !inputY) return;

            function place(fx, fy) {
                const r = img.getBoundingClientRect();
                const wr = wrap.getBoundingClientRect();
                marker.style.left = (r.left - wr.left + r.width * fx / 100) + 'px';
                marker.style.top = (r.top - wr.top + r.height * fy / 100) + 'px';
            }

            function setFocus(fx, fy) {
                fx = Math.max(0, Math.min(100, fx));
                fy = Math.max(0, Math.min(100, fy));
                inputX.value = Math.round(fx);
                inputY.value = Math.round(fy);
                place(fx, fy);
            }

            wrap.addEventListener('click', function (e) {
                const r = img.getBoundingClientRect();
                setFocus((e.clientX - r.left) / r.width * 100, (e.clientY - r.top) / r.height * 100);
            });

            const reset = document.getElementById('focus-reset');
            if (reset) {
                reset.addEventListener('click', function () { setFocus(50, 50); });
            }

            function placeSaved() {
                place(parseInt(inputX.value, 10) || 50, parseInt(inputY.value, 10) || 50);
            }

            if (img.complete) {
                placeSaved();
            } else {
                img.addEventListener('load', placeSaved);
            }
            window.addEventListener('resize', placeSaved);
        })();
    </script>
    @endif
@endsection
