<!-- Sidebar Overlay (mobile) — fullscreen dim, seragam dari awal biar gak ada seam saat drawer geser -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 lg:hidden opacity-0 pointer-events-none transition-opacity duration-300" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-50 w-64 h-full bg-slate-800 text-white shadow-2xl lg:shadow-none transform -translate-x-full lg:translate-x-0">
    <div class="flex items-center justify-center h-16 border-b border-slate-700">
        <svg class="w-7 h-7 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        <h2 class="sidebar-label text-lg font-semibold ml-3">Photo Slideshow</h2>
    </div>

    <nav class="mt-6 px-4 space-y-1">
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="sidebar-label">Dashboard</span>
        </a>

        <!-- Media Section -->
        <button type="button" class="sidebar-section sidebar-section-btn w-full flex items-center justify-between px-4 pt-4 pb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider hover:text-slate-300" data-group-toggle="media" aria-expanded="true">
            <span>Media</span>
            <svg class="sidebar-chevron w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="sidebar-group" data-group="media">
            <div class="sidebar-group-inner space-y-1">
                <a href="{{ route('photos.index') }}"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('photos.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="sidebar-label">Photos</span>
                </a>
                <a href="{{ route('videos.index') }}"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('videos.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span class="sidebar-label">Videos</span>
                </a>
            </div>
        </div>

        <!-- Slideshow Section -->
        <button type="button" class="sidebar-section sidebar-section-btn w-full flex items-center justify-between px-4 pt-4 pb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider hover:text-slate-300" data-group-toggle="slideshow" aria-expanded="true">
            <span>Slideshow</span>
            <svg class="sidebar-chevron w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="sidebar-group" data-group="slideshow">
            <div class="sidebar-group-inner space-y-1">
                <a href="{{ route('photo-groups.index') }}"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('photo-groups.*') || request()->routeIs('group-items.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span class="sidebar-label">Grup Slideshow</span>
                </a>
                <a href="{{ route('display.all') }}" target="_blank"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors text-slate-300 hover:bg-slate-700 hover:text-white">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span class="sidebar-label">Buka Display</span>
                    <svg class="sidebar-external w-3.5 h-3.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>

        <!-- Konten Display Section -->
        <button type="button" class="sidebar-section sidebar-section-btn w-full flex items-center justify-between px-4 pt-4 pb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider hover:text-slate-300" data-group-toggle="konten" aria-expanded="true">
            <span>Display Content</span>
            <svg class="sidebar-chevron w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="sidebar-group" data-group="konten">
            <div class="sidebar-group-inner space-y-1">
                <a href="{{ route('signage.main') }}"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('signage.main*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span class="sidebar-label">Main Slideshow</span>
                </a>
                <a href="{{ route('signage.facilities') }}"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('signage.facilities*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7zm10 0a2 2 0 012-2h4a2 2 0 012 2v10a2 2 0 01-2 2h-4a2 2 0 01-2-2V7z"/></svg>
                    <span class="sidebar-label">Facilities</span>
                </a>
                <a href="{{ route('signage.next-event') }}"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('signage.next-event*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="sidebar-label">Next Event</span>
                </a>
                <a href="{{ route('signage.header') }}"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('signage.header*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    <span class="sidebar-label">Header & Title</span>
                </a>
                <a href="{{ route('signage.footer') }}"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('signage.footer*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 20h18M5 20v-6m4 6v-6m6 6v-6m4 6v-6"/></svg>
                    <span class="sidebar-label">Footer Info</span>
                </a>
<a href="{{ route('running-texts.index') }}"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('running-texts.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h18M3 12h18M3 19h12"/></svg>
                    <span class="sidebar-label">Running Text</span>
                </a>
            </div>
        </div>

        <!-- Settings Section -->
        <button type="button" class="sidebar-section sidebar-section-btn w-full flex items-center justify-between px-4 pt-4 pb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider hover:text-slate-300" data-group-toggle="settings" aria-expanded="true">
            <span>Settings</span>
            <svg class="sidebar-chevron w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="sidebar-group" data-group="settings">
            <div class="sidebar-group-inner space-y-1">
                <a href="{{ route('settings.crop') }}"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('settings.crop*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span class="sidebar-label">Template Crop</span>
                </a>
                <a href="{{ route('settings.password') }}"
                   class="flex items-center px-4 py-2.5 text-sm rounded-md transition-colors {{ request()->routeIs('settings.password*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span class="sidebar-label">Change Password</span>
                </a>
            </div>
        </div>
    </nav>
</aside>

<script>
    (function() {
        const keyPrefix = 'sidebar-group-';
        document.querySelectorAll('#sidebar [data-group-toggle]').forEach(function(btn) {
            const name = btn.dataset.groupToggle;
            const group = document.querySelector('#sidebar [data-group="' + name + '"]');
            if (!group) return;

            let collapsed = localStorage.getItem(keyPrefix + name) === '1';

            function apply() {
                group.classList.toggle('sidebar-group-collapsed', collapsed);
                btn.setAttribute('aria-expanded', String(!collapsed));
                btn.querySelector('.sidebar-chevron').classList.toggle('-rotate-90', collapsed);
            }

            apply();
            btn.addEventListener('click', function() {
                collapsed = !collapsed;
                localStorage.setItem(keyPrefix + name, collapsed ? '1' : '0');
                apply();
            });
        });
    })();
</script>
