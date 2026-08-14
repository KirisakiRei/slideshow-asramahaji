@extends('layouts.admin')

@section('title', 'Main Slideshow')
@section('page-title', 'Main Slideshow')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Display Content'],
        ['label' => 'Main Slideshow'],
    ]" />

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Main Slideshow</h2>
            <p class="mt-1 text-sm text-slate-500">Pilih grup yang ditampilkan di hero display. Urutan daftar = urutan pemutaran. Grup di luar daftar ini tidak tampil di slideshow utama.</p>
        </div>
        <a href="{{ route('display.all') }}" target="_blank"
           class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Buka Display
        </a>
    </div>

    <form method="POST" action="{{ route('signage.main.update') }}">
        @csrf
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-3xl">
            @include('admin.signage._group-picker', [
                'name' => 'group_ids',
                'placements' => $placements,
                'groups' => $groups,
                'group_previews' => $groupPreviews,
            ])

            @error('group_ids.*')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex justify-end pt-4 mt-4 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Main Slideshow
                </button>
            </div>
        </div>
    </form>
@endsection
