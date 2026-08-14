@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Template Crop')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Settings'],
        ['label' => 'Template Crop'],
    ]" />

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Template Crop</h2>
        <p class="mt-1 text-sm text-slate-500">Rasio framing (lebar:tinggi) tiap slot slideshow, dipakai sebagai acuan preview crop foto di halaman Edit Foto.</p>
    </div>

    @php
        $labels = [
            'main' => 'Slideshow Utama',
            'main_desc' => 'Media besar di bagian atas display.',
            'facilities' => 'Fasilitas',
            'facilities_desc' => 'Kartu media tiap fasilitas di tengah display.',
            'next_event' => 'Event Selanjutnya',
            'next_event_desc' => 'Media event mendatang di bawah display.',
        ];
        $fields = [
            'main' => '907:656',
            'facilities' => '239:143',
            'next_event' => '608:315',
        ];
    @endphp

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('settings.crop.update') }}">
            @csrf
            <div class="space-y-5">
                @foreach ($fields as $key => $example)
                    <div>
                        <label for="crop_{{ $key }}" class="block text-sm font-medium text-slate-700 mb-1.5">
                            {{ $labels[$key] }}
                            <x-field-tip text="Ukuran yang terukur dari layout display 1080x1920. Edit jika tampilan frame berubah." />
                        </label>
                        <input type="text" id="crop_{{ $key }}" name="{{ $key }}"
                               value="{{ old($key, $templates[$key] ?? '') }}"
                               maxlength="32" required placeholder="{{ $example }}"
                               class="w-full px-3 py-2.5 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 @error($key) border-red-500 @enderror">
                        <p class="mt-1 text-xs text-slate-400">{{ $labels[$key.'_desc'] }}</p>
                        @error($key)
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="mt-5 p-3 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-500">
                Nilai ini hanya panduan untuk simulasi crop di panel admin. Di display, foto otomatis menyesuaikan dengan ukuran asli frame masing-masing slot.
            </div>

            <div class="flex justify-end pt-5 mt-5 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Template
                </button>
            </div>
        </form>
    </div>
@endsection