@extends('layouts.admin')

@section('title', 'Kelola Media - ' . $group->name)
@section('page-title', 'Kelola Media')

@section('content')
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Grup Slideshow', 'url' => route('photo-groups.index')],
        ['label' => $group->name, 'url' => route('photo-groups.edit', $group)],
        ['label' => 'Kelola Media'],
    ]" />

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">{{ $group->name }}</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola media dalam grup ini. Gunakan panah untuk mengatur urutan.</p>
        </div>
        <div class="mt-3 sm:mt-0 flex items-center gap-2">
            <a href="{{ route('photo-groups.index') }}"
               class="inline-flex items-center px-3 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <a href="{{ route('display.show', $group) }}" target="_blank"
               class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Buka Display
            </a>
        </div>
    </div>

    <!-- Media Library -->
    @if(isset($allPhotos) && $allPhotos->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5 mb-6">
            <h3 class="text-sm font-semibold text-slate-700 mb-1">Tambah Media ke Grup
                <x-field-tip text="Centang satu atau beberapa media lalu klik tombol tambah. Media yang sudah masuk grup ditandai hijau." />
            </h3>
            <p class="text-xs text-slate-500 mb-3">Pilih foto/video yang ingin ditambahkan. Media yang sudah ada di grup ini ditandai.</p>
            <form method="POST" action="{{ route('group-items.store', $group) }}" id="add-media-form">
                @csrf
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3 mb-4 max-h-72 overflow-y-auto p-1">
                    @foreach($allPhotos as $photo)
                        @php $alreadyIn = in_array($photo->id, $existingPhotoIds); @endphp
                        <label class="relative group {{ $alreadyIn ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                            <input type="checkbox" name="photo_ids[]" value="{{ $photo->id }}"
                                   {{ $alreadyIn ? 'disabled' : '' }}
                                   class="peer absolute top-2 left-2 z-10 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 {{ $alreadyIn ? 'hidden' : '' }}">
                            <div class="aspect-square rounded-lg overflow-hidden border-2 transition-all relative
                                        {{ $alreadyIn ? 'border-green-300 opacity-60' : 'border-slate-200 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-200' }}">
                                @if($photo->type === 'video')
                                    <video src="{{ asset('storage/' . $photo->file_path) }}#t=0.1" class="media-video-thumb w-full h-full object-cover" muted playsinline preload="metadata"></video>
                                    <div class="absolute bottom-1 right-1 bg-black/70 text-white px-1 py-0.5 rounded text-xs flex items-center gap-0.5">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        Video
                                    </div>
                                @else
                                    <img src="{{ asset('storage/' . $photo->file_path) }}"
                                         alt="{{ $photo->title }}"
                                         class="w-full h-full object-cover {{ $alreadyIn ? '' : 'group-hover:scale-105' }} transition-transform">
                                @endif

                                @if($alreadyIn)
                                    <div class="absolute inset-0 flex items-center justify-center bg-green-900/30">
                                        <span class="inline-flex items-center gap-1 bg-green-600 text-white text-xs font-medium px-1.5 py-0.5 rounded">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Di grup
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 truncate mt-1 text-center">{{ $photo->title }}</p>
                        </label>
                    @endforeach
                </div>
                @error('photo_ids')
                    <p class="mb-3 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Media Terpilih
                </button>
            </form>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5 mb-6 text-center text-sm text-slate-500">
            Belum ada media aktif. <a href="{{ route('photos.index') }}" class="text-slate-700 underline">Upload foto</a> atau <a href="{{ route('videos.index') }}" class="text-slate-700 underline">upload video</a> terlebih dahulu.
        </div>
    @endif

    <!-- Items List -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        {{-- ═══ MOBILE CARD VIEW (below lg:) ═══ --}}
        <div class="data-table-mobile">
            @forelse($items as $index => $item)
                <div class="border-b border-slate-200 p-4">
                    <div class="flex items-start gap-3 mb-3">
                        {{-- Thumbnail --}}
                        <div class="shrink-0">
                            @if($item->photo && $item->photo->file_path)
                                @if($item->photo->type === 'video')
                                    <div class="relative h-14 w-18 overflow-hidden rounded bg-slate-900">
                                        <video src="{{ asset('storage/' . $item->photo->file_path) }}#t=0.1" class="media-video-thumb h-full w-full object-cover" muted playsinline preload="metadata"></video>
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/20"><svg class="h-5 w-5 text-white/85" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>
                                    </div>
                                @else
                                    <img src="{{ asset('storage/' . $item->photo->file_path) }}" alt="{{ $item->photo->title }}" class="w-14 h-14 object-cover rounded">
                                @endif
                            @else
                                <div class="w-14 h-14 bg-slate-200 rounded flex items-center justify-center"><svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                            @endif
                        </div>
                        {{-- Title + Status --}}
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-slate-900 truncate">{{ $item->photo->title ?? '-' }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <x-status-badge :active="$item->is_active" />
                                <span class="text-xs text-slate-400">#{{ $item->sort_order }}</span>
                            </div>
                        </div>
                    </div>
                    {{-- Order controls + Actions --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            @if($index > 0)
                                <form method="POST" action="{{ route('group-items.move-up', [$group, $item]) }}">@csrf
                                    <button type="submit" class="p-1.5 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded" title="Naikkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></button>
                                </form>
                            @else
                                <span class="p-1.5 text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></span>
                            @endif
                            @if($index < $items->count() - 1)
                                <form method="POST" action="{{ route('group-items.move-down', [$group, $item]) }}">@csrf
                                    <button type="submit" class="p-1.5 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded" title="Turunkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
                                </form>
                            @else
                                <span class="p-1.5 text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1">
                            <form method="POST" action="{{ route('group-items.update', [$group, $item]) }}">@csrf @method('PUT')
                                <input type="hidden" name="is_active" value="{{ $item->is_active ? '0' : '1' }}">
                                <button type="submit" class="p-1.5 rounded {{ $item->is_active ? 'text-green-600 hover:bg-green-50' : 'text-yellow-600 hover:bg-yellow-50' }} transition-colors" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    @if($item->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
                                    @endif
                                </button>
                            </form>
                            <form method="POST" action="{{ route('group-items.destroy', [$group, $item]) }}" onsubmit="return confirm('Hapus media ini dari grup?')">@csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded transition-colors" title="Hapus dari grup"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-sm text-slate-500">
                    Belum ada media dalam grup ini.
                    @if(isset($allPhotos) && $allPhotos->count() > 0) Pilih media dari library di atas.
                    @else <a href="{{ route('photos.index') }}" class="text-slate-700 underline">Upload media baru</a> terlebih dahulu.
                    @endif
                </div>
            @endforelse
        </div>

        {{-- ═══ DESKTOP TABLE VIEW (lg: and up) ═══ --}}
        <div class="data-table-desktop">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider w-16">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Media</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Title</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Order</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($items as $index => $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    @if($item->photo && $item->photo->file_path)
                                        @if($item->photo->type === 'video')
                                            <div class="relative h-12 w-16 overflow-hidden rounded bg-slate-900">
                                                <video src="{{ asset('storage/' . $item->photo->file_path) }}#t=0.1" class="media-video-thumb h-full w-full object-cover" muted playsinline preload="metadata"></video>
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/20"><svg class="h-5 w-5 text-white/85" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>
                                            </div>
                                        @else
                                            <img src="{{ asset('storage/' . $item->photo->file_path) }}" alt="{{ $item->photo->title }}" class="w-12 h-12 object-cover rounded">
                                        @endif
                                    @else
                                        <div class="w-12 h-12 bg-slate-200 rounded flex items-center justify-center"><svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                    @endif
                                </td>
                                <td class="px-4 py-3"><span class="text-sm font-medium text-slate-900">{{ $item->photo->title ?? '-' }}</span></td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        @if($index > 0)
                                            <form method="POST" action="{{ route('group-items.move-up', [$group, $item]) }}">@csrf<button type="submit" class="p-1 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded" title="Naikkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></button></form>
                                        @else <span class="p-1 text-slate-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></span>
                                        @endif
                                        <span class="text-sm font-mono text-slate-600 w-8 text-center">{{ $item->sort_order }}</span>
                                        @if($index < $items->count() - 1)
                                            <form method="POST" action="{{ route('group-items.move-down', [$group, $item]) }}">@csrf<button type="submit" class="p-1 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded" title="Turunkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button></form>
                                        @else <span class="p-1 text-slate-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center"><x-status-badge :active="$item->is_active" /></td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <form method="POST" action="{{ route('group-items.update', [$group, $item]) }}">@csrf @method('PUT')
                                            <input type="hidden" name="is_active" value="{{ $item->is_active ? '0' : '1' }}">
                                            <button type="submit" class="p-1.5 rounded {{ $item->is_active ? 'text-green-600 hover:bg-green-50' : 'text-yellow-600 hover:bg-yellow-50' }} transition-colors" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                @if($item->is_active)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
                                                @endif
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('group-items.destroy', [$group, $item]) }}" onsubmit="return confirm('Hapus media ini dari grup?')">@csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded transition-colors" title="Hapus dari grup"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                                Belum ada media dalam grup ini.
                                @if(isset($allPhotos) && $allPhotos->count() > 0) Pilih media dari library di atas.
                                @else <a href="{{ route('photos.index') }}" class="text-slate-700 underline">Upload media baru</a> terlebih dahulu.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        (function() {
            document.querySelectorAll('.media-video-thumb').forEach(function(video) {
                video.addEventListener('loadedmetadata', function() {
                    try {
                        if (video.duration > 0) video.currentTime = Math.min(0.1, video.duration);
                    } catch (e) {}
                }, { once: true });
            });
        })();
    </script>
@endsection
