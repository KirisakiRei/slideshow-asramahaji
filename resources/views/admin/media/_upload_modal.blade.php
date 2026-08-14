@php
    $modalId = $modalId ?? 'upload-modal';
    $formId = $formId ?? $modalId . '-form';
    $fileInputId = $fileInputId ?? $modalId . '-file';
    $titleInputId = $titleInputId ?? $modalId . '-title';
    $previewType = $previewType ?? 'image';
@endphp

<div id="{{ $modalId }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6" data-upload-modal>
    <div class="w-full max-w-3xl overflow-hidden rounded-lg bg-white shadow-2xl">
        <div class="flex items-start justify-between border-b border-slate-200 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">{{ $title }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
            </div>
            <button type="button" class="rounded-md p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" data-close-upload>
                <span class="sr-only">Tutup</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="{{ $formId }}" action="{{ $storeRoute }}" method="POST" enctype="multipart/form-data" class="grid gap-0 lg:grid-cols-[1fr_280px]" data-upload-form data-grid-id="{{ $gridId }}" data-empty-id="{{ $emptyStateId }}" data-total-id="{{ $totalId }}" data-media-kind="{{ $kind }}">
            @csrf

            <div class="px-6 py-5">
                <label for="{{ $fileInputId }}" class="group flex min-h-56 cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center transition hover:border-slate-400 hover:bg-slate-100" data-drop-zone>
                    <input id="{{ $fileInputId }}" type="file" name="file" accept="{{ $accept }}" class="sr-only" data-upload-file>
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-600 shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-800">Pilih file atau tarik ke sini</span>
                    <span class="mt-1 text-xs text-slate-500">{{ $helpText }}</span>
                </label>

                <p class="mt-2 hidden text-sm text-red-600" data-upload-file-error></p>

                <div class="mt-5 grid gap-4 sm:grid-cols-[1fr_auto]">
                    <div>
                        <label for="{{ $titleInputId }}" class="mb-1 block text-sm font-medium text-slate-700">Judul
                            <x-field-tip text="Nama yang tampil di galeri. Jika dikosongkan, nama file akan digunakan secara otomatis." />
                        </label>
                        <input id="{{ $titleInputId }}" type="text" name="title" maxlength="255" placeholder="Kosongkan untuk memakai nama file" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200" data-upload-title>
                    </div>
                    <label class="mt-6 inline-flex items-center gap-2 whitespace-nowrap text-sm font-medium text-slate-700">Active
                        <x-field-tip text="Media aktif dapat langsung digunakan pada slideshow. Media nonaktif tetap tersimpan namun tidak ditampilkan." />
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-slate-800 focus:ring-slate-400">
                    </label>
                </div>

                <div class="mt-5 hidden" data-upload-progress>
                    <div class="mb-1 flex items-center justify-between text-xs font-medium text-slate-600">
                        <span data-upload-progress-label>Mengunggah...</span>
                        <span data-upload-progress-percent>0%</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full w-0 rounded-full bg-slate-800 transition-all duration-200" data-upload-progress-bar></div>
                    </div>
                    <p class="mt-1 text-xs text-slate-400" data-upload-progress-detail></p>
                </div>

                <div class="mt-4 hidden rounded-md border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700" data-upload-success></div>
                <div class="mt-4 hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" data-upload-error></div>
            </div>

            <aside class="border-t border-slate-200 bg-slate-50 px-5 py-5 lg:border-l lg:border-t-0">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-800">Preview</h3>
                    <button type="button" class="hidden text-xs font-medium text-slate-500 hover:text-red-600" data-clear-file>Hapus</button>
                </div>

                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                    @if($previewType === 'video')
                        <video class="hidden aspect-video w-full bg-black object-cover" muted playsinline controls data-preview-video></video>
                    @else
                        <img class="hidden aspect-square w-full object-cover" alt="Preview upload" data-preview-image>
                    @endif
                    <div class="flex min-h-24 items-center justify-center px-4 text-center text-sm text-slate-400" data-preview-empty>
                        Belum ada file dipilih
                    </div>
                </div>

                <p class="mt-3 truncate text-xs font-medium text-slate-700" data-preview-filename></p>
                <p class="mt-1 text-xs text-slate-400" data-preview-filesize></p>

                <div class="mt-5 flex items-center justify-end gap-2">
                    <button type="button" class="rounded-md bg-white px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-100" data-close-upload>Batal</button>
                    <button type="submit" class="rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60" data-upload-submit>Upload</button>
                </div>
            </aside>
        </form>
    </div>
</div>

<script>
    (function() {
        const modal = document.getElementById(@json($modalId));
        if (!modal) return;

        const form = modal.querySelector('[data-upload-form]');
        const fileInput = modal.querySelector('[data-upload-file]');
        const titleInput = modal.querySelector('[data-upload-title]');
        const dropZone = modal.querySelector('[data-drop-zone]');
        const previewImage = modal.querySelector('[data-preview-image]');
        const previewVideo = modal.querySelector('[data-preview-video]');
        const previewEmpty = modal.querySelector('[data-preview-empty]');
        const previewFilename = modal.querySelector('[data-preview-filename]');
        const previewFilesize = modal.querySelector('[data-preview-filesize]');
        const clearFileButton = modal.querySelector('[data-clear-file]');
        const submitButton = modal.querySelector('[data-upload-submit]');
        const progress = modal.querySelector('[data-upload-progress]');
        const progressBar = modal.querySelector('[data-upload-progress-bar]');
        const progressPercent = modal.querySelector('[data-upload-progress-percent]');
        const progressDetail = modal.querySelector('[data-upload-progress-detail]');
        const successBox = modal.querySelector('[data-upload-success]');
        const errorBox = modal.querySelector('[data-upload-error]');
        const fileError = modal.querySelector('[data-upload-file-error]');
        const csrfToken = @json(csrf_token());
        const mediaKind = form.dataset.mediaKind;

        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function statusBadge(active) {
            if (active) {
                return '<span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-green-200">Active</span>';
            }

            return '<span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-slate-200">Inactive</span>';
        }

        function renderCard(media) {
            const title = escapeHtml(media.title);
            const editUrl = escapeHtml(media.edit_url);
            const deleteUrl = escapeHtml(media.delete_url);
            const url = escapeHtml(media.url);
            const deleteText = media.type === 'video' ? 'Hapus video ini?' : 'Hapus foto ini?';
            const thumb = media.type === 'video'
                ? '<div class="aspect-video bg-slate-900 overflow-hidden cursor-pointer relative" onclick="openPreview(\'' + url + '\', \'' + title.replace(/'/g, '\\&#039;') + '\')"><video src="' + url + '#t=0.1" class="media-video-thumb h-full w-full object-cover" muted playsinline preload="metadata"></video><div class="absolute inset-0 flex items-center justify-center bg-black/20"><svg class="h-10 w-10 text-white/85" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div></div>'
                : '<div class="aspect-square bg-slate-100 overflow-hidden cursor-pointer relative" onclick="openPreview(\'' + url + '\', \'' + title.replace(/'/g, '\\&#039;') + '\')"><img src="' + url + '" alt="' + title + '" class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105"></div>';

            return '<article class="group overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm" data-media-card="' + media.id + '">' +
                thumb +
                '<div class="p-3"><h3 class="mb-2 truncate text-xs font-medium text-slate-800" title="' + title + '">' + title + '</h3>' +
                '<div class="flex items-center justify-between">' + statusBadge(media.is_active) +
                '<div class="flex items-center gap-0.5"><a href="' + editUrl + '" class="rounded p-1.5 text-slate-500 transition-colors hover:bg-blue-50 hover:text-blue-600" title="Edit"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>' +
                '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'' + deleteText + '\')"><input type="hidden" name="_token" value="' + csrfToken + '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="rounded p-1.5 text-slate-500 transition-colors hover:bg-red-50 hover:text-red-600" title="Hapus"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form></div></div></div></article>';
        }

        function resetMessages() {
            successBox.classList.add('hidden');
            errorBox.classList.add('hidden');
            fileError.classList.add('hidden');
            successBox.textContent = '';
            errorBox.textContent = '';
            fileError.textContent = '';
        }

        function resetForm() {
            form.reset();
            if (previewImage) {
                previewImage.removeAttribute('src');
                previewImage.classList.add('hidden');
            }
            if (previewVideo) {
                previewVideo.pause();
                previewVideo.removeAttribute('src');
                previewVideo.load();
                previewVideo.classList.add('hidden');
            }
            previewEmpty.classList.remove('hidden');
            previewFilename.textContent = '';
            previewFilesize.textContent = '';
            clearFileButton.classList.add('hidden');
            progress.classList.add('hidden');
            progressBar.style.width = '0%';
            progressPercent.textContent = '0%';
            progressDetail.textContent = '';
            submitButton.disabled = false;
            submitButton.textContent = 'Upload';
        }

        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            resetMessages();
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            resetForm();
            resetMessages();
        }

        function showFile(file) {
            if (!file) return;
            resetMessages();
            previewFilename.textContent = file.name;
            previewFilesize.textContent = formatSize(file.size);
            clearFileButton.classList.remove('hidden');
            previewEmpty.classList.add('hidden');

            if (!titleInput.value.trim()) {
                titleInput.value = file.name.replace(/\.[^/.]+$/, '');
            }

            const objectUrl = URL.createObjectURL(file);
            if (previewVideo) {
                previewVideo.src = objectUrl;
                previewVideo.load();
                previewVideo.classList.remove('hidden');
            }
            if (previewImage) {
                previewImage.src = objectUrl;
                previewImage.classList.remove('hidden');
            }
        }

        document.querySelectorAll('[data-open-upload="' + @json($modalId) + '"]').forEach(function(button) {
            button.addEventListener('click', openModal);
        });

        modal.querySelectorAll('[data-close-upload]').forEach(function(button) {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', function(event) {
            if (event.target === modal) closeModal();
        });

        fileInput.addEventListener('change', function() {
            showFile(fileInput.files[0]);
        });

        clearFileButton.addEventListener('click', resetForm);

        ['dragenter', 'dragover'].forEach(function(eventName) {
            dropZone.addEventListener(eventName, function(event) {
                event.preventDefault();
                dropZone.classList.add('border-slate-500', 'bg-slate-100');
            });
        });

        ['dragleave', 'drop'].forEach(function(eventName) {
            dropZone.addEventListener(eventName, function(event) {
                event.preventDefault();
                dropZone.classList.remove('border-slate-500', 'bg-slate-100');
            });
        });

        dropZone.addEventListener('drop', function(event) {
            if (!event.dataTransfer.files.length) return;
            fileInput.files = event.dataTransfer.files;
            showFile(fileInput.files[0]);
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            resetMessages();

            if (!fileInput.files.length) {
                fileError.textContent = mediaKind === 'video' ? 'Pilih file video terlebih dahulu.' : 'Pilih file foto terlebih dahulu.';
                fileError.classList.remove('hidden');
                return;
            }

            const xhr = new XMLHttpRequest();
            const formData = new FormData(form);
            const startedAt = Date.now();

            submitButton.disabled = true;
            submitButton.textContent = 'Mengunggah...';
            progress.classList.remove('hidden');
            progressBar.style.width = '0%';
            progressPercent.textContent = '0%';

            xhr.upload.addEventListener('progress', function(e) {
                if (!e.lengthComputable) return;
                const percent = Math.round((e.loaded / e.total) * 100);
                const elapsed = Math.max((Date.now() - startedAt) / 1000, 0.1);
                const speed = e.loaded / elapsed;
                const remaining = Math.max((e.total - e.loaded) / speed, 0);

                progressBar.style.width = percent + '%';
                progressPercent.textContent = percent + '%';
                progressDetail.textContent = formatSize(e.loaded) + ' / ' + formatSize(e.total) + ' - ' + Math.round(remaining) + ' detik tersisa';
            });

            xhr.addEventListener('load', function() {
                submitButton.disabled = false;
                submitButton.textContent = 'Upload';
                progress.classList.add('hidden');

                let response = {};
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (e) {
                    response = {};
                }

                if (xhr.status >= 200 && xhr.status < 400 && response.media) {
                    const grid = document.getElementById(form.dataset.gridId);
                    const emptyState = document.getElementById(form.dataset.emptyId);
                    const total = document.getElementById(form.dataset.totalId);

                    if (grid) {
                        grid.insertAdjacentHTML('afterbegin', renderCard(response.media));
                        grid.classList.remove('hidden');
                        if (response.media.type === 'video' && window.prepareVideoThumbnails) {
                            window.prepareVideoThumbnails(grid);
                        }
                    }
                    if (emptyState) emptyState.classList.add('hidden');
                    if (total) {
                        const current = Number(total.dataset.total || total.textContent || 0);
                        total.dataset.total = current + 1;
                        total.textContent = current + 1;
                    }

                    successBox.textContent = response.message || 'Upload berhasil.';
                    successBox.classList.remove('hidden');
                    resetForm();
                    return;
                }

                const errors = response.errors || {};
                const firstFileError = errors.file ? errors.file[0] : null;
                errorBox.textContent = firstFileError || response.error || response.message || 'Upload gagal. Periksa file dan coba lagi.';
                errorBox.classList.remove('hidden');
            });

            xhr.addEventListener('error', function() {
                submitButton.disabled = false;
                submitButton.textContent = 'Upload';
                progress.classList.add('hidden');
                errorBox.textContent = 'Koneksi terputus saat upload. Coba lagi.';
                errorBox.classList.remove('hidden');
            });

            xhr.open('POST', form.action);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.send(formData);
        });
    })();
</script>
