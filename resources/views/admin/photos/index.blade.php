@extends('layouts.admin')

@section('title', 'Photos')
@section('page-title', 'Photos')

@section('content')
    <x-breadcrumb :items="[['label' => 'Media'], ['label' => 'Photos']]" />

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Photo Library</h2>
            <p class="mt-1 text-sm text-slate-500">Upload dan kelola aset foto untuk slideshow display.</p>
        </div>
        <button type="button" data-open-upload="photo-upload-modal" class="inline-flex items-center justify-center rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700">
            <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5"/>
            </svg>
            Tambah Foto
        </button>
    </div>

    <div class="mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('photos.index') }}" class="flex flex-col gap-3 sm:flex-row" data-live-filter>
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search photo by title..." class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-400">
            </div>
            <select name="status" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-400 sm:w-auto">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700">
                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Search
                </button>
                <a href="{{ route('photos.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-300">Reset</a>
            </div>
        </form>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-slate-600">Total: <span id="photo-total" data-total="{{ $photos->total() }}" class="tabular-nums">{{ $photos->total() }}</span> photos</p>
    </div>

    <div id="photo-grid" class="{{ $photos->count() > 0 ? '' : 'hidden' }} mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        @foreach($photos as $photo)
            <article class="group overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm" data-media-card="{{ $photo->id }}">
                <div class="relative aspect-square cursor-pointer overflow-hidden bg-slate-100" onclick="openPreview(@js(asset('storage/' . $photo->file_path)), @js($photo->title))">
                    <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->title }}" class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105">
                </div>
                <div class="p-3">
                    <h3 class="mb-2 truncate text-xs font-medium text-slate-800" title="{{ $photo->title }}">{{ $photo->title }}</h3>
                    <div class="flex items-center justify-between">
                        @include('components.status-badge', ['active' => $photo->is_active])
                        <div class="flex items-center gap-0.5">
                            <a href="{{ route('photos.edit', $photo) }}" class="rounded p-1.5 text-slate-500 transition-colors hover:bg-blue-50 hover:text-blue-600" title="Edit">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('photos.destroy', $photo) }}" onsubmit="return confirm('Hapus foto ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded p-1.5 text-slate-500 transition-colors hover:bg-red-50 hover:text-red-600" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div id="photo-empty" class="{{ $photos->count() > 0 ? 'hidden' : '' }} rounded-lg border border-slate-200 bg-white p-12 text-center shadow-sm">
        <svg class="mx-auto mb-4 h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm text-slate-500">Belum ada foto.</p>
        <button type="button" data-open-upload="photo-upload-modal" class="mt-4 inline-flex items-center rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700">Upload Foto Pertama</button>
    </div>

    @if($photos->count() > 0)
        <div class="mt-4">{{ $photos->withQueryString()->links() }}</div>
    @endif

    <div id="photo-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80" onclick="closePreview(event)">
        <div class="relative max-h-[90vh] max-w-4xl p-4">
            <button type="button" onclick="closePreview()" class="absolute right-2 top-2 z-10 rounded-full bg-black/50 p-2 text-white transition-colors hover:bg-black/70">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <img id="modal-image" src="" alt="" class="max-h-[85vh] max-w-full rounded-lg object-contain shadow-2xl">
            <p id="modal-title" class="mt-3 text-center text-sm text-white"></p>
        </div>
    </div>

    @include('admin.media._upload_modal', [
        'modalId' => 'photo-upload-modal',
        'formId' => 'photo-upload-form',
        'fileInputId' => 'photo-upload-file',
        'titleInputId' => 'photo-upload-title',
        'title' => 'Upload Foto',
        'description' => 'Tambahkan foto baru ke library tanpa meninggalkan halaman ini.',
        'storeRoute' => route('photos.store'),
        'accept' => 'image/jpeg,image/jpg,image/png,image/gif,image/webp',
        'helpText' => 'JPEG, PNG, GIF, atau WEBP. Maksimal 10MB.',
        'kind' => 'photo',
        'previewType' => 'image',
        'gridId' => 'photo-grid',
        'emptyStateId' => 'photo-empty',
        'totalId' => 'photo-total',
    ])

    <script>
        function openPreview(src, title) {
            const modal = document.getElementById('photo-modal');
            document.getElementById('modal-image').src = src;
            document.getElementById('modal-title').textContent = title;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closePreview(event) {
            if (event && event.target !== document.getElementById('photo-modal')) return;
            const modal = document.getElementById('photo-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePreview();
        });
    </script>
@endsection
