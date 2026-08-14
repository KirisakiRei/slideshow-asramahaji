# Photo Slideshow — Digital Signage Admin

Aplikasi web untuk mengelola **slideshow digital signage** di layar portrait (TV/display), lengkap dengan admin panel untuk mengatur media, grup slideshow, fasilitas, event, dan running text.

## Fitur

- **Admin panel** (portrait-friendly, responsive)
  - Upload & kelola foto/video
  - Grup slideshow dengan urutan & durasi per grup
  - Pilih grup untuk hero display (multi-group berurutan)
  - Kelola running text (ticker) di bagian bawah layar
  - 7 jenis transisi: fade, slide (atas/bawah/kiri/kanan), zoom in/out
- **Halaman display public** (`/display`)
  - Canvas portrait 1080×1920 yang otomatis menyesuaikan layar
  - Auto-advance fot0 & video, crossfade dual-layer
  - Auto-refresh konten tanpa campur tangan (polling hash 5 detik)
  - Error recovery & rate-limited reload agar kiosk tetap hidup
- **Deployment satu-klik** ke PC target via Docker image + script PowerShell

## Tech Stack

| Bagian | Teknologi |
|--------|-----------|
| Backend | Laravel 12 (PHP 8.4) |
| Frontend | Blade + Tailwind CSS v4 + Vite |
| Database | MySQL 8.0 (via Docker) |
| Runtime target | Docker Compose (nginx + php-fpm + supervisor) |

## Struktur Penting

```text
app/Http/Controllers/     Controller aplikasi
resources/views/admin/    Halaman admin (dashboard, media, grup, signage)
resources/views/display/  Halaman display (self-contained, tanpa dependency eksternal)
deploy/                   Paket deploy (gitignored — berisi image tar + env pribadi)
docker/                   Dockerfile, entrypoint, nginx, supervisor
scripts/                  Helper PowerShell build/deploy
docker-compose.yml        Build & dev di laptop developer
docker-compose.prod.yml   Basis untuk compose produksi di PC target
```

## Setup Pengembangan (Local)

Prasyarat: PHP 8.4+, Composer, Node 20+, MySQL, atau Docker.

```bash
cp .env.example .env
php artisan key:generate

composer install
npm install
npm run build

# isi kredensial DB di .env, lalu:
php artisan migrate --seed
php artisan serve
```

Buka `http://localhost:8000` (atau `http://localhost:2026` bila via Docker).

## Build & Deploy ke PC Display

Alur build hanya perlu di laptop developer; PC target cukup Docker Desktop.

```bash
build-image.bat
```

Menghasilkan folder `deploy/` berisi image tar, compose, env, dan panduan deploy. Salin folder tersebut ke PC target, lalu:

```powershell
docker load -i slideshow-photo.tar
.\start-slideshow.ps1
```

Lihat `DEPLOY.md` (di folder `deploy`) untuk panduan detail. Port aplikasi: **2026**.

> Catatan keamanan: file `.env*` (termasuk `.env.production`) dan dokumen lokal (`*.md` selain `README.md`) tidak di-commit. Kredensial DB disuntikkan lewat environment variable saat runtime, bukan hardcoded di compose.

## Login Default

| | |
|---|---|
| Admin | `admin` / `password123` |

**Segera ganti password setelah login pertama** (menu Settings di pojok kanan navbar).

## Halaman Display

Membuka tampilan display di browser fullscreen:

```text
http://localhost:2026/display
```

Untuk orientasi portrait 1080×1920, gunakan kiosk mode browser.

## Lisensi

Proyek internal — tidak untuk distribusi publik tanpa izin.