@extends('layouts.admin')

@section('title', 'Videos')
@section('page-title', 'Videos')

@section('content')
    <x-breadcrumb :items="[['label' => 'Media'], ['label' => 'Videos']]" />

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Video Library</h2>
            <p class="mt-1 text-sm text-slate-500">Upload dan kelola aset video untuk slideshow display.</p>
        </div>
        <button type="button" data-open-upload="video-upload-modal" class="inline-flex items-center justify-center rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700">
            <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5"/>
            </svg>
            Tambah Video
        </button>
    </div>

    <div class="mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('videos.index') }}" class="flex flex-col gap-3 sm:flex-row" data-live-filter>
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search video by title..." class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-400">
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
                <a href="{{ route('videos.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-300">Reset</a>
            </div>
        </form>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-slate-600">Total: <span id="video-total" data-total="{{ $videos->total() }}" class="tabular-nums">{{ $videos->total() }}</span> videos</p>
    </div>

    <div id="video-grid" class="{{ $videos->count() > 0 ? '' : 'hidden' }} mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
        @foreach($videos as $video)
            <article class="group overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm" data-media-card="{{ $video->id }}">
                <div class="relative aspect-video cursor-pointer overflow-hidden bg-slate-900" onclick="openPreview(@js(asset('storage/' . $video->file_path)), @js($video->title))">
                    <video src="{{ asset('storage/' . $video->file_path) }}#t=0.1" class="media-video-thumb h-full w-full object-cover" muted playsinline preload="metadata"></video>
                    <div class="absolute inset-0 flex items-center justify-center bg-black/20 transition-colors group-hover:bg-black/40">
                        <svg class="h-10 w-10 text-white/85" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                    <span class="absolute bottom-2 right-2 rounded bg-black/70 px-1.5 py-0.5 text-xs font-medium text-white">Video</span>
                </div>
                <div class="p-3">
                    <h3 class="mb-2 truncate text-xs font-medium text-slate-800" title="{{ $video->title }}">{{ $video->title }}</h3>
                    <div class="flex items-center justify-between">
                        @include('components.status-badge', ['active' => $video->is_active])
                        <div class="flex items-center gap-0.5">
                            <a href="{{ route('videos.edit', $video) }}" class="rounded p-1.5 text-slate-500 transition-colors hover:bg-blue-50 hover:text-blue-600" title="Edit">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('videos.destroy', $video) }}" onsubmit="return confirm('Hapus video ini?')">
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

    <div id="video-empty" class="{{ $videos->count() > 0 ? 'hidden' : '' }} rounded-lg border border-slate-200 bg-white p-12 text-center shadow-sm">
        <svg class="mx-auto mb-4 h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm text-slate-500">Belum ada video.</p>
        <button type="button" data-open-upload="video-upload-modal" class="mt-4 inline-flex items-center rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700">Upload Video Pertama</button>
    </div>

    @if($videos->count() > 0)
        <div class="mt-4">{{ $videos->withQueryString()->links() }}</div>
    @endif

    <div id="video-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80" onclick="closePreview(event)">
        <div class="relative w-full max-w-4xl p-4">
            <button type="button" onclick="closePreview()" class="absolute right-2 top-2 z-10 rounded-full bg-black/50 p-2 text-white transition-colors hover:bg-black/70">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <video id="modal-video" controls class="max-h-[80vh] w-full rounded-lg bg-black shadow-2xl"></video>
            <p id="modal-title" class="mt-3 text-center text-sm text-white"></p>
        </div>
    </div>

    @include('admin.media._upload_modal', [
        'modalId' => 'video-upload-modal',
        'formId' => 'video-upload-form',
        'fileInputId' => 'video-upload-file',
        'titleInputId' => 'video-upload-title',
        'title' => 'Upload Video',
        'description' => 'Tambahkan video baru ke library tanpa meninggalkan halaman ini.',
        'storeRoute' => route('videos.store'),
        'accept' => 'video/mp4,video/x-matroska,video/quicktime',
        'helpText' => 'MP4, MKV, atau MOV. Maksimal 200MB.',
        'kind' => 'video',
        'previewType' => 'video',
        'gridId' => 'video-grid',
        'emptyStateId' => 'video-empty',
        'totalId' => 'video-total',
    ])

    <script>
        window.prepareVideoThumbnails = function(scope) {
            (scope || document).querySelectorAll('.media-video-thumb').forEach(function(video) {
                if (video.dataset.thumbnailPrepared === '1') return;
                video.dataset.thumbnailPrepared = '1';

                video.addEventListener('loadedmetadata', function() {
                    try {
                        if (video.duration > 0) video.currentTime = Math.min(0.1, video.duration);
                    } catch (e) {}
                }, { once: true });
            });
        };

        window.prepareVideoThumbnails(document);

        function openPreview(src, title) {
            const modal = document.getElementById('video-modal');
            const video = document.getElementById('modal-video');
            document.getElementById('modal-title').textContent = title;
            video.src = src;
            video.play().catch(function() {});
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closePreview(event) {
            if (event && event.target !== document.getElementById('video-modal')) return;
            const modal = document.getElementById('video-modal');
            const video = document.getElementById('modal-video');
            video.pause();
            video.src = '';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePreview();
        });
    </script>
@endsection
