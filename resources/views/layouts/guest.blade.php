<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - Photo Slideshow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white">
    <div class="flex min-h-screen">
        <!-- Left Panel: Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-slate-800 relative overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <!-- Content -->
            <div class="relative flex flex-col items-center justify-center w-full px-12">
                <!-- Icon -->
                <div class="mb-8 p-6 bg-white/10 rounded-2xl backdrop-blur-sm">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>

                <h1 class="text-4xl font-bold text-white mb-3">Photo Slideshow</h1>
                <p class="text-slate-300 text-center text-lg max-w-sm">
                    Kelola foto, video, dan konten display untuk tampil rapi di layar digital Anda.
                </p>

                <!-- Decorative dots -->
                <div class="mt-12 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-white/60"></div>
                    <div class="w-2 h-2 rounded-full bg-white/40"></div>
                    <div class="w-2 h-2 rounded-full bg-white/20"></div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Form -->
        <div class="flex-1 flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                <!-- Mobile branding (shown on small screens) -->
                <div class="lg:hidden text-center mb-8">
                    <h1 class="text-2xl font-bold text-slate-800">Photo Slideshow</h1>
                    <p class="text-sm text-slate-500 mt-1">Sistem manajemen konten display</p>
                </div>

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
