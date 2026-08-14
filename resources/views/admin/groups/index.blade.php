@extends('layouts.admin')

@section('title', 'Grup Slideshow')
@section('page-title', 'Grup Slideshow')

@section('content')
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[['label' => 'Grup Slideshow']]" />

    <!-- Info banner about combined display -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1 text-sm text-blue-800">
            <p class="font-medium mb-1">Lihat Slideshow</p>
            <p class="text-blue-700">Grup yang <strong>aktif</strong> akan diputar sesuai urutan.</p>
        </div>
        <a href="{{ route('display.all') }}" target="_blank"
           class="flex-shrink-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Buka Display
        </a>
    </div>

    <!-- Header with Create Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Daftar Grup Slideshow</h2>
        <a href="{{ route('photo-groups.create') }}"
           class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Buat Grup Baru
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('photo-groups.index') }}" class="flex flex-col sm:flex-row gap-3" data-live-filter>
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search group name..."
                       class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500">
            </div>
            <div>
                <select name="status"
                        class="w-full sm:w-auto px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-slate-600 text-white text-sm font-medium rounded-md hover:bg-slate-500 transition-colors">
                    Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('photo-groups.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Groups Table -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        @php
            $activeGroups = $groups->filter(fn ($g) => $g->is_active)->values();
            $firstActiveId = $activeGroups->first()->id ?? null;
            $lastActiveId = $activeGroups->last()->id ?? null;
            $activeCounter = 0;
        @endphp

        {{-- ═══ MOBILE CARD VIEW (below lg:) ═══ --}}
        <div class="data-table-mobile">
            @forelse($groups as $group)
                @php if ($group->is_active) { $activeCounter++; } @endphp
                <div class="border-b border-slate-200 p-4 {{ $group->is_active ? '' : 'bg-slate-50/50' }}">
                    {{-- Header row: name + toggle --}}
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-slate-900 truncate">{{ $group->name }}</div>
                            @if($group->description)
                                <div class="text-xs text-slate-500 truncate mt-0.5">{{ $group->description }}</div>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('photo-groups.toggle', $group) }}" class="shrink-0">
                            @csrf
                            <button type="submit" role="switch" aria-checked="{{ $group->is_active ? 'true' : 'false' }}"
                                    title="{{ $group->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}"
                                    style="position:relative;display:inline-flex;align-items:center;height:24px;width:44px;border-radius:9999px;border:none;cursor:pointer;transition:background-color .2s;background-color:{{ $group->is_active ? '#22c55e' : '#cbd5e1' }};">
                                <span style="display:inline-block;height:18px;width:18px;border-radius:9999px;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.2);transition:transform .2s;transform:translateX({{ $group->is_active ? '23px' : '3px' }});"></span>
                            </button>
                        </form>
                    </div>

                    {{-- Meta row: order + media + duration --}}
                    <div class="flex items-center gap-4 text-xs text-slate-500 mb-3">
                        <span>
                            @if($group->is_active)
                                Order: <span class="font-semibold text-slate-700">{{ $activeCounter }}</span>
                                <span class="inline-flex gap-0.5 ml-1">
                                    @if($group->id !== $firstActiveId)
                                        <form method="POST" action="{{ route('photo-groups.move-up', $group) }}">@csrf<button type="submit" class="p-0.5 hover:text-slate-800" title="Naikkan"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></button></form>
                                    @endif
                                    @if($group->id !== $lastActiveId)
                                        <form method="POST" action="{{ route('photo-groups.move-down', $group) }}">@csrf<button type="submit" class="p-0.5 hover:text-slate-800" title="Turunkan"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button></form>
                                    @endif
                                </span>
                            @else
                                <span class="text-slate-300">Inactive</span>
                            @endif
                        </span>
                        <span>{{ $group->items_count }} media</span>
                        <span>{{ $group->slide_duration }} dtk</span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <a href="{{ route('group-items.index', $group) }}"
                           class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded hover:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Media
                        </a>
                        <a href="{{ route('display.show', $group) }}" target="_blank"
                           class="inline-flex items-center px-2.5 py-1.5 bg-green-50 text-green-700 text-xs font-medium rounded hover:bg-green-100 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Preview
                        </a>
                        <a href="{{ route('photo-groups.edit', $group) }}"
                           class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 text-slate-700 text-xs font-medium rounded hover:bg-slate-200 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        <form action="{{ route('photo-groups.destroy', $group) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus grup ini? Semua item dalam grup akan ikut terhapus.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-2.5 py-1.5 bg-red-50 text-red-700 text-xs font-medium rounded hover:bg-red-100 transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-sm text-slate-500">
                    @if(request('search') || request('status'))
                        Tidak ada grup yang sesuai dengan filter.
                    @else
                        Belum ada grup slideshow. <a href="{{ route('photo-groups.create') }}" class="text-slate-700 underline">Buat grup baru</a>.
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
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider w-28">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Media</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @php $activeCounter = 0; @endphp
                        @forelse($groups as $group)
                            @php if ($group->is_active) { $activeCounter++; } @endphp
                            <tr class="hover:bg-slate-50 {{ $group->is_active ? '' : 'bg-slate-50/50' }}">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($group->is_active)
                                        <div class="flex items-center justify-center gap-1">
                                            @if($group->id !== $firstActiveId)
                                                <form method="POST" action="{{ route('photo-groups.move-up', $group) }}">@csrf
                                                    <button type="submit" class="p-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded" title="Naikkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></button>
                                                </form>
                                            @else
                                                <span class="p-1 text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></span>
                                            @endif
                                            <span class="text-sm font-semibold text-slate-700 w-6 text-center">{{ $activeCounter }}</span>
                                            @if($group->id !== $lastActiveId)
                                                <form method="POST" action="{{ route('photo-groups.move-down', $group) }}">@csrf
                                                    <button type="submit" class="p-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded" title="Turunkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
                                                </form>
                                            @else
                                                <span class="p-1 text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center text-slate-300 text-sm">—</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-slate-900">{{ $group->name }}</div>
                                    @if($group->description)<div class="text-xs text-slate-500 truncate max-w-xs">{{ $group->description }}</div>@endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $group->items_count }} media</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <form method="POST" action="{{ route('photo-groups.toggle', $group) }}" class="inline">@csrf
                                        <button type="submit" role="switch" aria-checked="{{ $group->is_active ? 'true' : 'false' }}"
                                                title="{{ $group->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}"
                                                style="position:relative;display:inline-flex;align-items:center;height:24px;width:44px;border-radius:9999px;border:none;cursor:pointer;transition:background-color .2s;background-color:{{ $group->is_active ? '#22c55e' : '#cbd5e1' }};">
                                            <span style="display:inline-block;height:18px;width:18px;border-radius:9999px;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.2);transition:transform .2s;transform:translateX({{ $group->is_active ? '23px' : '3px' }});"></span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $group->slide_duration }} dtk</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('group-items.index', $group) }}" class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded hover:bg-blue-100 transition-colors" title="Kelola Media"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Media</a>
                                        <a href="{{ route('display.show', $group) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-green-50 text-green-700 text-xs font-medium rounded hover:bg-green-100 transition-colors" title="Preview"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> Preview</a>
                                        <a href="{{ route('photo-groups.edit', $group) }}" class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 text-slate-700 text-xs font-medium rounded hover:bg-slate-200 transition-colors" title="Edit"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Edit</a>
                                        <form action="{{ route('photo-groups.destroy', $group) }}" method="POST" class="inline" onsubmit="return confirm('Hapus grup ini? Semua item dalam grup akan ikut terhapus.')">@csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-red-50 text-red-700 text-xs font-medium rounded hover:bg-red-100 transition-colors"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                                @if(request('search') || request('status'))
                                    Tidak ada grup yang sesuai dengan filter.
                                @else
                                    Belum ada grup slideshow. <a href="{{ route('photo-groups.create') }}" class="text-slate-700 underline">Buat grup baru</a>.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($groups->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $groups->links() }}
            </div>
        @endif
    </div>
@endsection
