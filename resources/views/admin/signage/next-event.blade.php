@extends('layouts.admin')

@section('title', 'Next Event')
@section('page-title', 'Next Event')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Display Content'],
        ['label' => 'Next Event'],
    ]" />

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Next Event</h2>
        <p class="mt-1 text-sm text-slate-500">Isi kartu event berikutnya di display. Area media memutar media dari grup yang dipilih (urut daftar = urutan pemutaran). Isi teks yang kosong tidak akan ditampilkan.</p>
    </div>

    <form method="POST" action="{{ route('signage.next-event.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-5 pb-3 border-b border-slate-100">Detail Event</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="next_event_label" class="block text-sm font-medium text-slate-700 mb-2">Label
                                <x-field-tip text="Kata kecil di atas judul event, misalnya 'Event Selanjutnya'." />
                            </label>
                            <input type="text" id="next_event_label" name="next_event_label" maxlength="100"
                                   value="{{ $config['next_event_label'] }}"
                                   placeholder="Contoh: Event Selanjutnya"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        </div>
                        <div>
                            <label for="next_event_category" class="block text-sm font-medium text-slate-700 mb-2">Kategori
                                <x-field-tip text="Jenis acara, misalnya Seminar, Workshop, atau Pameran." />
                            </label>
                            <input type="text" id="next_event_category" name="next_event_category" maxlength="100"
                                   value="{{ $config['next_event_category'] }}"
                                   placeholder="Contoh: Seminar"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="next_event_title" class="block text-sm font-medium text-slate-700 mb-2">Judul Event
                                <x-field-tip text="Nama acara yang tampil besar di kartu event." />
                            </label>
                            <input type="text" id="next_event_title" name="next_event_title" maxlength="255"
                                   value="{{ $config['next_event_title'] }}"
                                   placeholder="Contoh: Workshop Fotografi Digital"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        </div>
                        <div>
                            <label for="next_event_organizer" class="block text-sm font-medium text-slate-700 mb-2">Penyelenggara
                                <x-field-tip text="Nama panitia atau instansi yang menyelenggarakan." />
                            </label>
                            <input type="text" id="next_event_organizer" name="next_event_organizer" maxlength="255"
                                   value="{{ $config['next_event_organizer'] }}"
                                   placeholder="Contoh: Panitia Inti"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        </div>
                        <div>
                            <label for="next_event_date" class="block text-sm font-medium text-slate-700 mb-2">Tanggal
                                <x-field-tip text="Tulis tanggal lengkap agar mudah dibaca, contoh: Sabtu, 15 Agustus 2026." />
                            </label>
                            <input type="text" id="next_event_date" name="next_event_date" maxlength="100"
                                   value="{{ $config['next_event_date'] }}"
                                   placeholder="Contoh: Sabtu, 15 Agustus 2026"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        </div>
                        <div>
                            <label for="next_event_time" class="block text-sm font-medium text-slate-700 mb-2">Waktu
                                <x-field-tip text="Jam mulai sampai selesai, contoh: 09.00 - 12.00 WIB." />
                            </label>
                            <input type="text" id="next_event_time" name="next_event_time" maxlength="50"
                                   value="{{ $config['next_event_time'] }}"
                                   placeholder="Contoh: 09.00 - 12.00 WIB"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="next_event_location" class="block text-sm font-medium text-slate-700 mb-2">Lokasi
                                <x-field-tip text="Tempat acara, contoh: Aula Utama, Lt. 2." />
                            </label>
                            <input type="text" id="next_event_location" name="next_event_location" maxlength="255"
                                   value="{{ $config['next_event_location'] }}"
                                   placeholder="Contoh: Aula Utama, Lt. 2"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 lg:sticky lg:top-20">
                    <h3 class="text-base font-semibold text-slate-800 mb-4">Media Event</h3>

                    @include('admin.signage._group-picker', [
                        'name' => 'group_ids',
                        'placements' => $placements,
                        'groups' => $groups,
                        'group_previews' => $groupPreviews,
                    ])

                    <button type="submit" class="mt-4 w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Event
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
