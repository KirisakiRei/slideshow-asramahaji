@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Settings'],
        ['label' => 'Change Password'],
    ]" />

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Change Password</h2>
        <p class="mt-1 text-sm text-slate-500">Perbarui password untuk login ke panel admin.</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('settings.password.update') }}">
            @csrf
            <div class="space-y-5">
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru
                        <x-field-tip text="Minimal 6 karakter. Password ini dipakai untuk login berikutnya." />
                    </label>
                    <input type="password" id="password" name="password" minlength="6" maxlength="128" required autocomplete="new-password"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" minlength="6" maxlength="128" required autocomplete="new-password"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500">
                </div>
            </div>

            <div class="flex justify-end pt-5 mt-5 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Update Password
                </button>
            </div>
        </form>
    </div>
@endsection
