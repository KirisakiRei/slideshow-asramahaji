@extends('layouts.admin')

@section('title', 'Edit Foto')
@section('page-title', 'Edit Foto')

@section('content')
    @php
        $tmplOptions = [];
        foreach (($cropTemplates ?? []) as $key => $ratio) {
            preg_match('/^(\d+)\s*:\s*(\d+)$/', trim((string) $ratio), $m);
            $tmplOptions[$key] = [
                'label' => $cropTemplateLabels[$key] ?? ucfirst((string) $key),
                'w' => isset($m[1]) ? (int) $m[1] : null,
                'h' => isset($m[2]) ? (int) $m[2] : null,
            ];
        }
        $mainW = $tmplOptions['main']['w'] ?? 907;
        $mainH = $tmplOptions['main']['h'] ?? 656;

        $framings = [];
        foreach (\App\Models\Photo::FRAMING_SLOTS as $slot) {
            $framings[$slot] = $photo->framingFor($slot);
        }
    @endphp

    <x-breadcrumb :items="[
        ['label' => 'Photos', 'url' => route('photos.index')],
        ['label' => 'Edit: ' . $photo->title],
    ]" />

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Edit Photo</h2>
        <p class="mt-1 text-sm text-slate-500">Perbarui judul dan status foto.</p>
    </div>

    <form method="POST" action="{{ route('photos.update', $photo) }}">
        @csrf
        @method('PUT')
@foreach (\App\Models\Photo::FRAMING_SLOTS as $slot)
            <input type="hidden" name="crop_data[{{ $slot }}][fx]" id="crop_data_{{ $slot }}_fx" value="{{ old('crop_data.' . $slot . '.fx', ($framings[$slot]['fx'] ?? 50)) }}">
            <input type="hidden" name="crop_data[{{ $slot }}][fy]" id="crop_data_{{ $slot }}_fy" value="{{ old('crop_data.' . $slot . '.fy', ($framings[$slot]['fy'] ?? 50)) }}">
            <input type="hidden" name="crop_data[{{ $slot }}][zoom]" id="crop_data_{{ $slot }}_zoom" value="{{ old('crop_data.' . $slot . '.zoom', ($framings[$slot]['zoom'] ?? 100)) }}">
        @endforeach
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Preview (larger) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-4 pb-3 border-b border-slate-100">Preview Foto</h3>
                    <div class="bg-slate-900 rounded-lg overflow-hidden" style="height: 460px;">
                        @if($photo->isPhoto())
                            <div id="crop-wrap" class="w-full h-full flex items-center justify-center p-4">
                                <div id="crop-box" class="relative bg-black select-none"
                                     style="width: 100%; aspect-ratio: {{ $mainW }}/{{ $mainH }}; overflow: hidden; cursor: crosshair; touch-action: none;">
                                    <img id="focus-img" src="{{ asset('storage/' . $photo->file_path) }}"
                                         alt="{{ $photo->title }}"
                                         style="position:absolute; inset:0; width:100%; height:100%; display:block; object-fit:cover; object-position:50% 50%;">
                                    <div id="focus-marker" class="absolute pointer-events-none"
                                         style="width:22px;height:22px;border:2px solid #fff;border-radius:50%;box-shadow:0 0 0 2px rgba(0,0,0,.6);transform:translate(-50%,-50%);"></div>
                                </div>
                            </div>
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <img src="{{ asset('storage/' . $photo->file_path) }}"
                                     alt="{{ $photo->title }}"
                                     style="max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;display:block;">
                            </div>
                        @endif
                    </div>
                    @if($photo->isPhoto())
                        <div class="mt-4 flex flex-wrap items-end gap-5">
                            <div>
                                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Acuan Framing</span>
                                <div id="framing-label" class="mt-1 text-sm font-medium text-slate-800">Mengatur: Slideshow Utama</div>
                                <div id="template-buttons" class="mt-1.5 flex flex-wrap gap-2"></div>
                            </div>
                            <div class="min-w-[220px] flex-1">
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="zoom-range" class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Zoom Masuk</label>
                                    <span id="zoom-value" class="text-xs font-semibold text-slate-800">100%</span>
                                </div>
                                <input type="range" id="zoom-range" min="100" max="400" step="5" value="100"
                                       class="w-full accent-slate-800 cursor-pointer">
                            </div>
                            <button type="button" id="focus-reset"
                                    class="px-3 py-2 text-xs font-medium text-slate-600 border border-slate-300 rounded-md hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                Reset Framing
                            </button>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">Atur <strong>posisi</strong> (drag pada preview) dan <strong>zoom</strong> untuk tiap frame slideshow secara terpisah: Slideshow Utama, Fasilitas, dan Event Selanjutnya. Pilih acuannya di atas, sesuaikan, lalu simpan — hasil di display otomatis memakai framing sesuai frame-nya. Video selalu tampil utuh.</p>
                    @endif
                </div>
            </div>

            <!-- Right: Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 lg:sticky lg:top-20">
                    <h3 class="text-base font-semibold text-slate-800 mb-4">Detail Foto</h3>

                    <div class="mb-5">
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">
                            Judul <span class="text-red-500">*</span>
                            <x-field-tip text="Nama yang tampil di galeri dan preview. Jika dikosongkan, nama file akan digunakan secara otomatis." />
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title', $photo->title) }}"
                               maxlength="255" required
                               class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors mb-5">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $photo->is_active) ? 'checked' : '' }}
                               class="mt-0.5 rounded border-slate-300 text-green-600 focus:ring-green-500">
                        <span>
                            <span class="block text-sm font-medium text-slate-700">Active</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Foto aktif dapat ditampilkan di slideshow.</span>
                        </span>
                    </label>

                    <div class="text-xs text-slate-500 mb-5 space-y-1 pb-5 border-b border-slate-100">
                        <div class="flex justify-between"><span>Diupload</span><span class="font-medium text-slate-700">{{ $photo->created_at->format('d M Y') }}</span></div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('photos.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

@if($photo->isPhoto())
    <script>
        (function () {
            const templates = @json($tmplOptions);
            const slotKeys = Object.keys(templates).length ? Object.keys(templates) : ['main'];
            const box = document.getElementById('crop-box');
            const wrap = document.getElementById('crop-wrap');
            const img = document.getElementById('focus-img');
            const marker = document.getElementById('focus-marker');
            const range = document.getElementById('zoom-range');
            const zoomValue = document.getElementById('zoom-value');
            const resetBtn = document.getElementById('focus-reset');
            const tmplBtns = document.getElementById('template-buttons');
            const framingLabel = document.getElementById('framing-label');
            if (!box || !wrap || !img || !marker || !range || !tmplBtns || !resetBtn) return;

            const slotInputs = {};
            slotKeys.forEach(function (key) {
                slotInputs[key] = {
                    fx: document.getElementById('crop_data_' + key + '_fx'),
                    fy: document.getElementById('crop_data_' + key + '_fy'),
                    zoom: document.getElementById('crop_data_' + key + '_zoom')
                };
            });

            let active = slotKeys[0];
            let fx = 50, fy = 50, zoom = 100;
            let ratio = templates[active] && templates[active].w && templates[active].h
                ? { w: templates[active].w, h: templates[active].h }
                : { w: 907, h: 656 };

            const clamp = function (v, min, max) { return Math.max(min, Math.min(max, v)); };

            function fitBox() {
                const rect = wrap.getBoundingClientRect();
                if (rect.width <= 0 || rect.height <= 0) return;
                const cs = getComputedStyle(wrap);
                const availW = rect.width - parseFloat(cs.paddingLeft) - parseFloat(cs.paddingRight);
                const availH = rect.height - parseFloat(cs.paddingTop) - parseFloat(cs.paddingBottom);
                if (availW <= 0 || availH <= 0) return;
                const r = ratio.w / ratio.h;
                let w = availW, h = w / r;
                if (h > availH) { h = availH; w = h * r; }
                box.style.width = Math.max(1, Math.floor(w)) + 'px';
                box.style.height = Math.max(1, Math.floor(h)) + 'px';
            }

            function apply() {
                fx = clamp(Math.round(fx), 0, 100);
                fy = clamp(Math.round(fy), 0, 100);
                zoom = clamp(Math.round(zoom), 100, 400);
                const inp = slotInputs[active];
                if (inp) {
                    inp.fx.value = fx;
                    inp.fy.value = fy;
                    inp.zoom.value = zoom;
                }
                img.style.objectPosition = fx + '% ' + fy + '%';
                img.style.transformOrigin = fx + '% ' + fy + '%';
                img.style.transform = 'scale(' + (zoom / 100) + ')';
                marker.style.left = fx + '%';
                marker.style.top = fy + '%';
                range.value = zoom;
                zoomValue.textContent = zoom + '%';
            }

            const btnBase = 'px-3 py-1.5 rounded-md border text-xs font-medium transition-colors';
            const btnIdle = btnBase + ' text-slate-600 border-slate-300 hover:bg-slate-50';
            const btnActive = btnBase + ' bg-slate-800 text-white border-slate-800';

            function loadSlot(key) {
                const inp = slotInputs[key];
                if (!inp) return;
                const vfx = parseInt(inp.fx.value, 10);
                const vfy = parseInt(inp.fy.value, 10);
                const vz = parseInt(inp.zoom.value, 10);
                fx = Number.isFinite(vfx) ? vfx : 50;
                fy = Number.isFinite(vfy) ? vfy : 50;
                zoom = Number.isFinite(vz) ? vz : 100;
            }

            function activateSlot(key) {
                const t = templates[key];
                if (!t) return;
                active = key;
                loadSlot(key);
                ratio = { w: t.w || 907, h: t.h || 656 };
                apply();
                fitBox();
                Array.prototype.forEach.call(tmplBtns.children, function (b) {
                    b.className = b.dataset.tmpl === key ? btnActive : btnIdle;
                });
                if (framingLabel) {
                    framingLabel.textContent = 'Mengatur: ' + (t.label || 'Framing');
                }
            }

            Object.keys(templates).forEach(function (key) {
                const t = templates[key];
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.dataset.tmpl = key;
                btn.className = btnIdle;
                btn.textContent = t.label;
                btn.addEventListener('click', function () { activateSlot(key); });
                tmplBtns.appendChild(btn);
            });

            activateSlot(slotKeys[0]);

            let dragging = false;
            function setFromClient(clientX, clientY) {
                const r = box.getBoundingClientRect();
                if (r.width <= 0 || r.height <= 0) return;
                fx = (clientX - r.left) / r.width * 100;
                fy = (clientY - r.top) / r.height * 100;
                apply();
            }

            box.addEventListener('pointerdown', function (e) {
                dragging = true;
                box.setPointerCapture(e.pointerId);
                setFromClient(e.clientX, e.clientY);
            });
            box.addEventListener('pointermove', function (e) {
                if (!dragging) return;
                setFromClient(e.clientX, e.clientY);
            });
            box.addEventListener('pointerup', function () { dragging = false; });
            box.addEventListener('pointercancel', function () { dragging = false; });

            range.addEventListener('input', function () {
                zoom = parseInt(range.value, 10) || 100;
                apply();
            });

            resetBtn.addEventListener('click', function () {
                fx = 50;
                fy = 50;
                zoom = 100;
                apply();
            });

            window.addEventListener('resize', fitBox);
        })();
    </script>
    @endif
@endsection
