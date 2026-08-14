@echo off
setlocal EnableExtensions

REM Always run from this script's directory (project root).
cd /d "%~dp0"

echo.
echo ============================================
echo   PHOTO SLIDESHOW - Build dan Export Image
echo ============================================
echo.

REM ---- Pre-check: required files ----
set "MISSING="
if not exist "Dockerfile" set "MISSING=1" & echo ERROR: Dockerfile tidak ditemukan.
if not exist "docker-compose.prod.yml" set "MISSING=1" & echo ERROR: docker-compose.prod.yml tidak ditemukan.
if not exist ".env.production" set "MISSING=1" & echo ERROR: .env.production tidak ditemukan.
if not exist "scripts\start-slideshow.ps1" set "MISSING=1" & echo ERROR: scripts\start-slideshow.ps1 tidak ditemukan.
if not exist "scripts\prepare-deploy-env.ps1" set "MISSING=1" & echo ERROR: scripts\prepare-deploy-env.ps1 tidak ditemukan.
if not exist "docker\entrypoint.sh" set "MISSING=1" & echo ERROR: docker\entrypoint.sh tidak ditemukan.
if defined MISSING (
    echo.
    echo Pastikan script dijalankan dari root project slideshow-photo.
    pause
    exit /b 1
)

REM ---- Pre-check: Docker ----
where docker >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Perintah "docker" tidak ditemukan di PATH.
    echo Install Docker Desktop, lalu buka ulang terminal.
    pause
    exit /b 1
)

docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker tidak merespons.
    echo Pastikan Docker Desktop sudah running, lalu coba lagi.
    pause
    exit /b 1
)

echo [1/3] Building Docker image "slideshow-photo:latest"...
echo      (Proses ini memakan waktu 3-5 menit)
echo.
docker build -t slideshow-photo:latest .
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Build gagal!
    pause
    exit /b 1
)

echo.
echo [2/3] Exporting image ke file tar...
echo      (Ukuran file biasanya ratusan MB)
echo.
if exist "slideshow-photo.tar" del /F /Q "slideshow-photo.tar" >nul 2>&1
docker save slideshow-photo:latest -o slideshow-photo.tar
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Export image gagal!
    pause
    exit /b 1
)
if not exist "slideshow-photo.tar" (
    echo.
    echo ERROR: File slideshow-photo.tar tidak terbentuk setelah export.
    pause
    exit /b 1
)

echo.
echo [3/3] Menyiapkan folder deploy...
if not exist "deploy" mkdir deploy

move /Y slideshow-photo.tar deploy\slideshow-photo.tar >nul
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal memindahkan slideshow-photo.tar ke folder deploy.
    pause
    exit /b 1
)

copy /Y docker-compose.prod.yml deploy\docker-compose.yml >nul
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal copy docker-compose.prod.yml ke deploy\docker-compose.yml
    pause
    exit /b 1
)

copy /Y scripts\start-slideshow.ps1 deploy\start-slideshow.ps1 >nul
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal copy scripts\start-slideshow.ps1 ke deploy\
    pause
    exit /b 1
)

copy /Y DEPLOY.md deploy\DEPLOY.md >nul
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal copy DEPLOY.md ke deploy\
    pause
    exit /b 1
)

REM Jangan timpa .env deploy yang sudah dipakai di target (APP_KEY tetap).
if not exist "deploy\.env" (
    copy /Y .env.production deploy\.env >nul
    if %errorlevel% neq 0 (
        echo.
        echo ERROR: Gagal copy .env.production ke deploy\.env
        pause
        exit /b 1
    )
)

powershell -NoProfile -ExecutionPolicy Bypass -File scripts\prepare-deploy-env.ps1 deploy\.env
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal menyiapkan APP_KEY di deploy\.env
    pause
    exit /b 1
)

echo.
echo ============================================
echo   SELESAI! Paket deploy siap.
echo ============================================
echo.
echo   Folder: deploy\
echo.
echo     deploy\slideshow-photo.tar   Docker image
echo     deploy\docker-compose.yml    Compose production (tanpa build)
echo     deploy\.env                  Env app + DB + MEDIA_ROOT
echo     deploy\start-slideshow.ps1   Siapkan folder media + compose up
echo     deploy\DEPLOY.md             Panduan install di PC lokasi
echo.
echo   Di PC target:
echo     1. Copy folder deploy\
echo     2. Buka terminal di folder deploy
echo     3. docker load -i slideshow-photo.tar
echo     4. powershell -ExecutionPolicy Bypass -File .\start-slideshow.ps1
echo     5. Buka http://localhost:2026
echo        Login: admin / password123
echo.
echo   Baca juga: deploy\DEPLOY.md
echo   Opsional: set MEDIA_ROOT di deploy\.env jika folder media
echo   bukan C:/slideshow/public
echo.
pause
endlocal
