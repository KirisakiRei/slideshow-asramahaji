@extends('layouts.admin')

@section('title', 'Running Text')
@section('page-title', 'Running Text')

@section('content')
    <x-breadcrumb :items="[['label' => 'Running Text']]" />

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Manajemen Running Text</h2>
        <p class="mt-1 text-sm text-slate-500">Running text tampil berjalan di bawah footer display. Semua teks berjalan berurutan sesuai urutan, lalu mengulang dari awal.</p>
    </div>

    <!-- Create form -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
        <h3 class="text-base font-semibold text-slate-800 mb-4">Tambah Running Text</h3>
        <form method="POST" action="{{ route('running-texts.store') }}" class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
            @csrf
            <div class="flex-1 min-w-0 w-full">
                <label for="text" class="block text-sm font-medium text-slate-700 mb-1.5">Teks
                    <x-field-tip text="Teks yang berjalan di bagian bawah layar. Tulis kalimat lengkap, contoh: Selamat datang di lobi utama." />
                </label>
                <input type="text" id="text" name="text" maxlength="500" value="{{ old('text') }}"
                       placeholder="Contoh: Selamat datang di lobi utama"
                       class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                @error('text')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah
            </button>
        </form>
    </div>

    <!-- List -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        {{-- ═══ MOBILE CARD VIEW (below lg:) ═══ --}}
        <div class="data-table-mobile">
            @forelse($runningTexts as $index => $item)
                <div class="border-b border-slate-200 p-4 {{ $item->is_active ? '' : 'bg-slate-50/50' }}">
                    {{-- Order + Status --}}
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1">
                            @if($index > 0)
                                <form method="POST" action="{{ route('running-texts.move-up', $item) }}">@csrf
                                    <button type="submit" class="p-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded" title="Naikkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></button>
                                </form>
                            @else
                                <span class="p-1 text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></span>
                            @endif
                            <span class="text-sm font-semibold text-slate-700 w-6 text-center">{{ $index + 1 }}</span>
                            @if($index < $runningTexts->count() - 1)
                                <form method="POST" action="{{ route('running-texts.move-down', $item) }}">@csrf
                                    <button type="submit" class="p-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded" title="Turunkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
                                </form>
                            @else
                                <span class="p-1 text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1">
                            <form method="POST" action="{{ route('running-texts.toggle', $item) }}">@csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $item->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('running-texts.destroy', $item) }}" onsubmit="return confirm('Hapus running text ini?');">@csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded transition-colors" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        </div>
                    </div>
                    {{-- Inline edit form --}}
                    <form method="POST" action="{{ route('running-texts.update', $item) }}">@csrf @method('PUT')
                        <div class="flex items-center gap-2">
                            <input type="text" name="text" maxlength="500" value="{{ $item->text }}"
                                   class="flex-1 min-w-0 px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                            <label class="flex items-center gap-1.5 text-sm text-slate-600 cursor-pointer whitespace-nowrap shrink-0">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-green-600 focus:ring-green-500">
                                Tampil
                            </label>
                            <button type="submit" class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded shrink-0" title="Simpan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-sm text-slate-400">Belum ada running text. Tambahkan lewat form di atas.</div>
            @endforelse
        </div>

        {{-- ═══ DESKTOP TABLE VIEW (lg: and up) ═══ --}}
        <div class="data-table-desktop">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider w-28">Order
                                <x-field-tip side="right" text="Urutan tampil di layar: baris atas lebih dulu, baris bawah terakhir." />
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Text</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider w-28">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider w-48">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($runningTexts as $index => $item)
                            <tr class="hover:bg-slate-50 {{ $item->is_active ? '' : 'bg-slate-50/50' }}">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        @if($index > 0)
                                            <form method="POST" action="{{ route('running-texts.move-up', $item) }}">@csrf<button type="submit" class="p-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded" title="Naikkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></button></form>
                                        @else <span class="p-1 text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></span>
                                        @endif
                                        <span class="text-sm font-semibold text-slate-700 w-6 text-center">{{ $index + 1 }}</span>
                                        @if($index < $runningTexts->count() - 1)
                                            <form method="POST" action="{{ route('running-texts.move-down', $item) }}">@csrf<button type="submit" class="p-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded" title="Turunkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button></form>
                                        @else <span class="p-1 text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('running-texts.update', $item) }}">@csrf @method('PUT')
                                        <div class="flex items-center gap-2">
                                            <input type="text" name="text" maxlength="500" value="{{ $item->text }}" class="flex-1 min-w-0 px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                                            <label class="flex items-center gap-1.5 text-sm text-slate-600 cursor-pointer whitespace-nowrap"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-green-600 focus:ring-green-500"> Tampil</label>
                                            <button type="submit" class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded" title="Simpan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></button>
                                        </div>
                                    </form>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <form method="POST" action="{{ route('running-texts.toggle', $item) }}">@csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $item->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ route('running-texts.destroy', $item) }}" onsubmit="return confirm('Hapus running text ini?');">@csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-700 font-medium"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-sm text-slate-400">Belum ada running text. Tambahkan lewat form di atas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
