@php
    $pickerId = 'picker-' . preg_replace('/[^a-zA-Z0-9]/', '_', $name);
    $selectedIds = $placements->pluck('photo_group_id')->toArray();
    $previews = $groupPreviews ?? [];
@endphp

<div id="{{ $pickerId }}" class="group-picker" data-name="{{ $name }}" data-previews='@json($previews)'>
    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Group Order
        <x-field-tip text="Susun grup dari atas ke bawah sesuai urutan tampil di layar. Grup paling atas diputar lebih dulu." />
    </p>

    <!-- Selected groups (ordered) -->
    <div class="picker-rows space-y-2 mb-3">
        @forelse($placements as $placement)
            @php $preview = $previews[$placement->photo_group_id] ?? null; @endphp
            <div class="picker-row flex items-center gap-2.5 p-2 rounded-lg border border-slate-200 bg-slate-50" data-group-id="{{ $placement->photo_group_id }}">
                <input type="hidden" name="{{ $name }}[]" value="{{ $placement->photo_group_id }}">
                <div class="picker-thumb w-10 h-10 rounded-md overflow-hidden flex-shrink-0 bg-slate-200">
                    @if($preview)
                        @if($preview['type'] === 'video')
                            <div class="w-full h-full flex items-center justify-center bg-slate-800 text-white">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        @else
                            <img src="{{ $preview['url'] }}" alt="" class="w-full h-full object-cover">
                        @endif
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>
                <span class="picker-order w-5 text-center text-xs font-semibold text-slate-400 flex-shrink-0">{{ $loop->iteration }}</span>
                <span class="flex-1 min-w-0 text-sm text-slate-700 truncate">{{ $placement->group->name ?? 'Grup #' . $placement->photo_group_id }}</span>
                <button type="button" class="picker-up p-1 text-slate-400 hover:text-slate-700 rounded" title="Naikkan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                </button>
                <button type="button" class="picker-down p-1 text-slate-400 hover:text-slate-700 rounded" title="Turunkan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <button type="button" class="picker-remove p-1 text-slate-400 hover:text-red-600 rounded" title="Hapus">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @empty
            <p class="picker-empty text-sm text-slate-400 italic">Belum ada grup dipilih.</p>
        @endforelse
    </div>

    <!-- Add group -->
    <div class="flex items-center gap-2">
        <select class="picker-select flex-1 min-w-0 px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
            <option value="">-- Pilih grup --</option>
            @foreach($groups as $group)
                @if(!in_array($group->id, $selectedIds))
                    <option value="{{ $group->id }}" data-label="{{ $group->name }}">{{ $group->name }}</option>
                @endif
            @endforeach
        </select>
        <button type="button" class="picker-add inline-flex items-center px-3 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-md hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah
        </button>
    </div>
</div>

<script>
    (function() {
        const picker = document.getElementById('{{ $pickerId }}');
        if (!picker) return;

        let previews = {};
        try {
            previews = JSON.parse(picker.dataset.previews || '{}');
        } catch (e) {}

        const rows = () => Array.from(picker.querySelectorAll('.picker-row'));
        const empty = picker.querySelector('.picker-empty');
        const select = picker.querySelector('.picker-select');
        const name = picker.dataset.name;

        function thumbHtml(preview) {
            if (!preview) {
                return '<div class="w-full h-full flex items-center justify-center text-slate-400">' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>';
            }
            if (preview.type === 'video') {
                return '<div class="w-full h-full flex items-center justify-center bg-slate-800 text-white">' +
                    '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>';
            }
            return '<img src="' + preview.url + '" alt="" class="w-full h-full object-cover">';
        }

        function renumber() {
            rows().forEach(function(row, i) {
                row.querySelector('.picker-order').textContent = i + 1;
            });
        }

        function toggleEmpty() {
            if (empty) empty.style.display = rows().length ? 'none' : '';
        }

        function optionFor(groupId, label) {
            const opt = document.createElement('option');
            opt.value = groupId;
            opt.dataset.label = label;
            opt.textContent = label;
            return opt;
        }

        picker.querySelector('.picker-add').addEventListener('click', function() {
            const groupId = select.value;
            if (!groupId) return;
            const label = select.selectedOptions[0].dataset.label || 'Grup #' + groupId;

            const row = document.createElement('div');
            row.className = 'picker-row flex items-center gap-2.5 p-2 rounded-lg border border-slate-200 bg-slate-50';
            row.dataset.groupId = groupId;
            row.innerHTML =
                '<input type="hidden" name="' + name + '[]" value="' + groupId + '">' +
                '<div class="picker-thumb w-10 h-10 rounded-md overflow-hidden flex-shrink-0 bg-slate-200">' + thumbHtml(previews[groupId] || null) + '</div>' +
                '<span class="picker-order w-5 text-center text-xs font-semibold text-slate-400 flex-shrink-0"></span>' +
                '<span class="flex-1 min-w-0 text-sm text-slate-700 truncate"></span>' +
                '<button type="button" class="picker-up p-1 text-slate-400 hover:text-slate-700 rounded" title="Naikkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></button>' +
                '<button type="button" class="picker-down p-1 text-slate-400 hover:text-slate-700 rounded" title="Turunkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>' +
                '<button type="button" class="picker-remove p-1 text-slate-400 hover:text-red-600 rounded" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
            row.querySelector('.picker-order').textContent = rows().length + 1;
            row.querySelector('span.flex-1').textContent = label;
            picker.querySelector('.picker-rows').appendChild(row);

            select.remove(select.selectedIndex);
            select.value = '';
            toggleEmpty();
        });

        picker.querySelector('.picker-rows').addEventListener('click', function(e) {
            const btn = e.target.closest('button');
            if (!btn) return;
            const row = btn.closest('.picker-row');

            if (btn.classList.contains('picker-up')) {
                const prev = row.previousElementSibling;
                if (prev && prev.classList.contains('picker-row')) {
                    row.parentNode.insertBefore(row, prev);
                    renumber();
                }
            } else if (btn.classList.contains('picker-down')) {
                const next = row.nextElementSibling;
                if (next && next.classList.contains('picker-row')) {
                    row.parentNode.insertBefore(next, row);
                    renumber();
                }
            } else if (btn.classList.contains('picker-remove')) {
                const groupId = row.dataset.groupId;
                const label = row.querySelector('span.flex-1').textContent;
                select.appendChild(optionFor(groupId, label));
                row.remove();
                renumber();
                toggleEmpty();
            }
        });
    })();
</script>
