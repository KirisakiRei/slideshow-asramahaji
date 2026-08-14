@extends('layouts.admin')

@section('title', 'Fasilitas')
@section('page-title', 'Facilities Display')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Display Content'],
        ['label' => 'Facilities'],
    ]" />

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Facilities</h2>
        <p class="mt-1 text-sm text-slate-500">Kelola 3 kartu fasilitas di sidebar display. Tiap fasilitas memutar media dari grup yang dipilih (urut daftar = urutan pemutaran).</p>
    </div>

    <form method="POST" action="{{ route('signage.facilities.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6 space-y-5">
            <div>
                <label for="section_chip" class="block text-sm font-medium text-slate-700 mb-2">Label Bagian (Section Chip)
                    <x-field-tip text="Label kecil di atas kartu fasilitas, biasanya 'Fasilitas'." />
                </label>
                <input type="text" id="section_chip" name="section_chip" maxlength="100"
                       value="{{ old('section_chip', $config['section_chip'] ?? '') }}"
                       placeholder="Contoh: Fasilitas"
                       class="w-full max-w-md px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
            </div>

            @php
                $captionsEnabled = old(
                    'show_facility_captions',
                    ($config['show_facility_captions'] ?? true) ? '1' : '0'
                ) == '1';
            @endphp
            <div class="flex items-start gap-3 max-w-xl">
                <input type="hidden" name="show_facility_captions" value="0">
                <input type="checkbox"
                       id="show_facility_captions"
                       name="show_facility_captions"
                       value="1"
                       @checked($captionsEnabled)
                       class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-800 focus:ring-slate-400">
                <div>
                    <label for="show_facility_captions" class="block text-sm font-medium text-slate-700">
                        Tampilkan caption di kartu fasilitas
                        <x-field-tip text="Matikan agar media fasilitas memenuhi seluruh kartu. Saat aktif, caption hanya muncul jika diisi." />
                    </label>
                    <p class="mt-1 text-xs text-slate-500">
                        Saat nonaktif, bar caption disembunyikan dan gambar/video memenuhi card. Saat aktif, caption kosong tidak ditampilkan (tanpa fallback "Fasilitas 1/2/3").
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach([1, 2, 3] as $slot)
                @php
                    $facility = $facilities->get($slot);
                @endphp
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-4">Fasilitas {{ $slot }}</h3>

                    <div class="mb-4">
                        <label for="caption_{{ $slot }}" class="block text-sm font-medium text-slate-700 mb-2">Caption
                            <x-field-tip text="Nama singkat fasilitas. Kosongkan agar bar caption di card ini hilang dan media memenuhi card." />
                        </label>
                        <input type="text" id="caption_{{ $slot }}" name="caption_{{ $slot }}" maxlength="100"
                               value="{{ old('caption_' . $slot, $facility->caption ?? '') }}"
                               placeholder="Kosongkan = tanpa caption"
                               class="facility-caption-input w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent {{ $captionsEnabled ? '' : 'opacity-50' }}">
                    </div>

                    @include('admin.signage._group-picker', [
                        'name' => 'group_ids_' . $slot,
                        'placements' => $placements[$slot] ?? collect(),
                        'groups' => $groups,
                        'group_previews' => $groupPreviews,
                    ])
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Fasilitas
            </button>
        </div>
    </form>

    <script>
        (function () {
            const toggle = document.getElementById('show_facility_captions');
            if (!toggle) return;

            const captionInputs = document.querySelectorAll('.facility-caption-input');

            const syncCaptionFields = () => {
                captionInputs.forEach((input) => {
                    // Keep inputs submittable; only mute visual weight when captions are off.
                    input.classList.toggle('opacity-50', !toggle.checked);
                });
            };

            toggle.addEventListener('change', syncCaptionFields);
            syncCaptionFields();
        })();
    </script>
@endsection
