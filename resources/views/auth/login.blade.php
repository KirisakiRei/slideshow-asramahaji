@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    @php
        $loginError = null;
        if ($errors->has('username')) {
            $message = $errors->first('username');
            if ($message === 'Username atau password salah' || str_contains($message, 'Terlalu banyak percobaan')) {
                $loginError = $message;
            }
        }
    @endphp

    @if ($loginError)
        <div id="login-toast" role="alert"
             class="login-toast fixed top-5 right-5 z-50 hidden items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium text-white max-w-sm {{ str_contains($loginError, 'Terlalu banyak') ? 'bg-amber-600' : 'bg-red-600' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="flex-1">{{ $loginError }}</span>
            <button type="button" data-toast-close class="text-white/80 hover:text-white flex-shrink-0" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-800">Masuk</h2>
        <p class="text-sm text-slate-500 mt-2">Masukkan username dan password untuk mengakses panel admin.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                value="{{ old('username') }}"
                maxlength="255"
                required
                autofocus
                autocomplete="username"
                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors @error('username') border-red-500 @enderror"
                placeholder="admin"
            >
            @error('username')
                @if ($message !== 'Username atau password salah' && !str_contains($message, 'Terlalu banyak percobaan'))
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @endif
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                minlength="6"
                maxlength="128"
                required
                autocomplete="current-password"
                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors @error('password') border-red-500 @enderror"
                placeholder="••••••••"
            >
            @error('password')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full bg-slate-800 text-white py-2.5 px-4 rounded-lg hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 text-sm font-medium transition-colors"
        >
            Sign In
        </button>
    </form>

    <p class="mt-8 text-center text-xs text-slate-400">
        Photo Slideshow &copy; {{ date('Y') }}
    </p>

    @if ($loginError)
        <script>
            (function () {
                const toast = document.getElementById('login-toast');
                if (!toast) return;

                const dismiss = () => {
                    toast.classList.remove('login-toast-show');
                    setTimeout(() => {
                        toast.classList.remove('flex');
                        toast.classList.add('hidden');
                    }, 200);
                };

                toast.classList.remove('hidden');
                toast.classList.add('flex');
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    toast.classList.add('login-toast-show');
                }));

                const timer = setTimeout(dismiss, 5000);
                toast.querySelector('[data-toast-close]').addEventListener('click', () => {
                    clearTimeout(timer);
                    dismiss();
                });
            })();
        </script>
    @endif
@endsection
