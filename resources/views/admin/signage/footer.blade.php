@extends('layouts.admin')

@section('title', 'Footer Info')
@section('page-title', 'Footer Info Display')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Display Content'],
        ['label' => 'Footer Info'],
    ]" />

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Footer Info Display</h2>
        <p class="mt-1 text-sm text-slate-500">Teks bagian bawah display, di samping jam. Kosongkan jika tidak diperlukan.</p>
    </div>

    <form method="POST" action="{{ route('signage.footer.update') }}">
        @csrf
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-3xl space-y-6">
            <div>
                <label for="footer_title" class="block text-sm font-medium text-slate-700 mb-2">Judul Utama
                    <x-field-tip text="Nama instansi atau venue, contoh: Balai Kota Bandung." />
                </label>
                <input type="text" id="footer_title" name="footer_title" maxlength="255"
                       value="{{ $config['footer_title'] }}"
                       placeholder="Contoh: Nama Instansi / Venue"
                       class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
            </div>

            <div>
                <label for="footer_subtitle" class="block text-sm font-medium text-slate-700 mb-2">Judul Sekunder
                    <x-field-tip text="Baris kedua di bawah judul utama. Boleh dikosongkan." />
                </label>
                <input type="text" id="footer_subtitle" name="footer_subtitle" maxlength="255"
                       value="{{ $config['footer_subtitle'] }}"
                       placeholder="Contoh: Unit Pelayanan Informasi"
                       class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
            </div>

            <div>
                <label for="footer_support" class="block text-sm font-medium text-slate-700 mb-2">Kalimat Pendukung
                    <x-field-tip text="Salam atau pesan singkat untuk pengunjung layar." />
                </label>
                <textarea id="footer_support" name="footer_support" rows="3" maxlength="500"
                          placeholder="Contoh: Selamat datang di lobi utama. Silakan gunakan fasilitas dengan tertib."
                          class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">{{ $config['footer_support'] }}</textarea>
                <p class="mt-1.5 text-xs text-slate-500">Satu kalimat pendek, tampil sebagai teks pendukung di footer.</p>
            </div>

            <div class="bg-slate-50 rounded-lg border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-1">Pengaturan Jam</h3>
                <p class="text-xs text-slate-500 mb-4">Jam display mengikuti jam perangkat TV. Jika meleset dari jam resmi, isi selisihnya di sini sekali saja.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="clock_offset" class="block text-sm font-medium text-slate-700 mb-1.5">Koreksi Waktu (detik)
                            <x-field-tip text="Selisih jam display terhadap jam resmi. Angka positif memajukan jam, negatif memundurkan. Contoh: jam display telat 5 menit, isi 300." />
                        </label>
                        <input type="number" id="clock_offset" name="clock_offset" min="-3600" max="3600" step="30"
                               value="{{ $config['clock_offset'] }}"
                               oninput="updateClockPreview()"
                               class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        <p class="mt-1.5 text-xs text-slate-500">Batas -1 jam sampai +1 jam. Saat ini:
                            <span class="font-medium text-slate-700">
                                @if($config['clock_offset'] > 0)
                                    maju {{ gmdate('i:s', $config['clock_offset']) }} menit
                                @elseif($config['clock_offset'] < 0)
                                    mundur {{ gmdate('i:s', abs($config['clock_offset'])) }} menit
                                @else
                                    tanpa koreksi
                                @endif
                            </span>
                        </p>
                        @error('clock_offset')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-col justify-center">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1.5">Preview Jam</p>
                        <div class="rounded-lg border border-slate-200 bg-white px-4 py-3 flex items-center gap-3">
                            <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span id="clock-preview" class="text-lg font-semibold text-slate-800 tabular-nums">--:--:--</span>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Waktu yang akan tampil di layar TV.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Footer
                </button>
            </div>
        </div>
    </form>

    <script>
        function updateClockPreview() {
            const offset = parseInt(document.getElementById('clock_offset').value || '0', 10);
            const now = new Date(Date.now() + offset * 1000);
            document.getElementById('clock-preview').textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false,
            });
        }
        updateClockPreview();
    </script>
@endsection
