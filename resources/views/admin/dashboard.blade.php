@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Hero -->
    <div class="rounded-2xl bg-slate-900 text-white px-4 sm:px-8 lg:px-10 py-5 sm:py-8 mb-6 flex flex-col lg:flex-row lg:items-center gap-4 sm:gap-6 relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-white/5"></div>
        <div class="absolute -right-8 -bottom-20 w-40 h-40 rounded-full bg-white/5"></div>
        <div class="relative flex-1 min-w-0">
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ $today }}</p>
            <h2 class="text-xl sm:text-2xl font-bold">Selamat datang, {{ Auth::user()->name }}</h2>
            <p class="mt-1.5 text-sm text-slate-300">Atur konten slideshow, fasilitas, event, dan teks berjalan untuk layar display.</p>
        </div>
        <div class="relative flex flex-col lg:flex-row gap-2 sm:gap-3 shrink-0 lg:shrink">
            <a href="{{ route('display.all') }}" target="_blank"
               class="inline-flex items-center justify-center w-full lg:w-auto px-5 py-2.5 bg-white text-slate-900 text-sm font-semibold rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Buka Display
            </a>
            <a href="{{ route('signage.main') }}"
               class="inline-flex items-center justify-center w-full lg:w-auto px-5 py-2.5 bg-white/10 text-white text-sm font-semibold rounded-lg hover:bg-white/20 transition-colors">
                Atur Slideshow
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-5 flex flex-col items-center text-center min-w-0">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-2.5">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-slate-800 leading-none tabular-nums">{{ $totalPhotos }}</p>
            <p class="mt-1 text-xs sm:text-sm font-medium text-slate-600">Total Photos</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-5 flex flex-col items-center text-center min-w-0">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-2.5">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-slate-800 leading-none tabular-nums">{{ $totalVideos }}</p>
            <p class="mt-1 text-xs sm:text-sm font-medium text-slate-600">Total Videos</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-5 flex flex-col items-center text-center min-w-0">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2.5">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-slate-800 leading-none tabular-nums">{{ $totalGroups }}</p>
            <p class="mt-1 text-xs sm:text-sm font-medium text-slate-600">Grup Slideshow</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-5 flex flex-col items-center text-center min-w-0">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-2.5">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-slate-800 leading-none tabular-nums">{{ $activeGroups }}</p>
            <p class="mt-1 text-xs sm:text-sm font-medium text-slate-600">Active Groups</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Quick actions -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-6">
                <h3 class="text-base font-semibold text-slate-800 mb-1">Quick Actions</h3>
                <p class="text-sm text-slate-500 mb-4 sm:mb-5">Jalan pintas untuk pekerjaan yang paling sering dilakukan.</p>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                    <a href="{{ route('photos.index') }}" class="group flex flex-col items-center text-center gap-2 p-4 rounded-xl border border-slate-200 hover:border-slate-400 hover:bg-slate-50 transition-all">
                        <div class="w-11 h-11 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-slate-800">Upload Foto</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Tambahkan foto ke library</span>
                        </span>
                    </a>
                    <a href="{{ route('videos.index') }}" class="group flex flex-col items-center text-center gap-2 p-4 rounded-xl border border-slate-200 hover:border-slate-400 hover:bg-slate-50 transition-all">
                        <div class="w-11 h-11 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-slate-800">Upload Video</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Tambahkan video ke library</span>
                        </span>
                    </a>
                    <a href="{{ route('photo-groups.create') }}" class="group flex flex-col items-center text-center gap-2 p-4 rounded-xl border border-slate-200 hover:border-slate-400 hover:bg-slate-50 transition-all">
                        <div class="w-11 h-11 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-slate-800">Buat Grup</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Kelompokkan media untuk display</span>
                        </span>
                    </a>
                    <a href="{{ route('photo-groups.index') }}" class="group flex flex-col items-center text-center gap-2 p-4 rounded-xl border border-slate-200 hover:border-slate-400 hover:bg-slate-50 transition-all">
                        <div class="w-11 h-11 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </div>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-slate-800">Kelola Grup</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Urutkan dan aktifkan grup</span>
                        </span>
                    </a>
                    <a href="{{ route('signage.main') }}" class="group flex flex-col items-center text-center gap-2 p-4 rounded-xl border border-slate-200 hover:border-slate-400 hover:bg-slate-50 transition-all">
                        <div class="w-11 h-11 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-slate-800">Atur Display</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Pilih grup untuk slideshow utama</span>
                        </span>
                    </a>
                    <a href="{{ route('running-texts.index') }}" class="group flex flex-col items-center text-center gap-2 p-4 rounded-xl border border-slate-200 hover:border-slate-400 hover:bg-slate-50 transition-all">
                        <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h18M3 12h18M3 19h12"/></svg>
                        </div>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-slate-800">Running Text</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Teks berjalan di bawah layar</span>
                        </span>
                    </a>
                </div>
            </div>

            <!-- Recent media -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-base font-semibold text-slate-800">Media Terbaru</h3>
                    <a href="{{ route('photos.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Lihat semua</a>
                </div>
                <p class="text-sm text-slate-500 mb-4">Foto terakhir yang diunggah ke library.</p>
                @if($recentPhotos->isNotEmpty())
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 sm:gap-3">
                        @foreach($recentPhotos as $photo)
                            <a href="{{ route('photos.edit', $photo) }}" class="block aspect-square rounded-lg overflow-hidden bg-slate-100 group">
                                <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->title }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center">
                        <p class="text-sm text-slate-500 mb-3">Belum ada foto di library.</p>
                        <a href="{{ route('photos.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">Upload Foto Pertama</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4 sm:space-y-6">
            <!-- Setup checklist -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-6">
                <h3 class="text-base font-semibold text-slate-800 mb-1">Status Persiapan</h3>
                <p class="text-sm text-slate-500 mb-4">Langkah agar slideshow siap tampil.</p>
                <ol class="space-y-3">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 {{ $setup['media'] ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800">Unggah media</p>
                            <p class="text-xs text-slate-500">{{ $setup['media'] ? 'Foto atau video sudah tersedia di library.' : 'Belum ada media. Mulai dari halaman Foto atau Video.' }}</p>
                            @if(!$setup['media'])
                                <a href="{{ route('photos.index') }}" class="text-xs font-medium text-slate-600 underline">Upload sekarang</a>
                            @endif
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 {{ $setup['groups'] ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800">Buat grup aktif</p>
                            <p class="text-xs text-slate-500">{{ $setup['groups'] ? 'Ada grup yang aktif dan siap diputar.' : 'Belum ada grup aktif.' }}</p>
                            @if(!$setup['groups'])
                                <a href="{{ route('photo-groups.create') }}" class="text-xs font-medium text-slate-600 underline">Buat grup</a>
                            @endif
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 {{ $setup['mainSlot'] ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800">Atur slideshow utama</p>
                            <p class="text-xs text-slate-500">{{ $setup['mainSlot'] ? 'Grup sudah dipilih untuk slideshow utama.' : 'Belum ada grup di slideshow utama.' }}</p>
                            @if(!$setup['mainSlot'])
                                <a href="{{ route('signage.main') }}" class="text-xs font-medium text-slate-600 underline">Atur sekarang</a>
                            @endif
                        </div>
                    </li>
                </ol>
            </div>

            <!-- Recent groups -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-base font-semibold text-slate-800">Grup Terbaru</h3>
                    <a href="{{ route('photo-groups.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Lihat semua</a>
                </div>
                <p class="text-sm text-slate-500 mb-4">Grup yang baru dibuat atau diperbarui.</p>
                @if($recentGroups->isNotEmpty())
                    <ul class="divide-y divide-slate-100">
                        @foreach($recentGroups as $group)
                            <li class="py-3 flex items-center gap-3">
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('photo-groups.edit', $group) }}" class="block text-sm font-medium text-slate-800 hover:text-slate-600 truncate">{{ $group->name }}</a>
                                    <p class="text-xs text-slate-500">{{ $group->items_count }} media</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $group->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $group->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-400 text-center py-6">Belum ada grup.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
