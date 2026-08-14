@props(['title', 'value', 'icon' => null, 'color' => 'slate'])

<div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $title }}</p>
            <p class="text-3xl font-bold text-slate-800 mt-1 tabular-nums">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="p-3 rounded-full bg-{{ $color }}-100 text-{{ $color }}-600">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
