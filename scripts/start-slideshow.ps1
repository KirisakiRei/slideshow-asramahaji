param(
    [string]$ComposeFile = "docker-compose.yml",
    [string]$MediaRoot = "C:\slideshow\public",
    [switch]$Build
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    throw "Docker tidak ditemukan. Install dan jalankan Docker Desktop terlebih dahulu."
}

if (-not (Test-Path -LiteralPath $ComposeFile -PathType Leaf)) {
    throw "File Docker Compose tidak ditemukan: $ComposeFile"
}

$resolvedMediaRoot = [System.IO.Path]::GetFullPath($MediaRoot)
$mediaDirectories = @(
    (Join-Path $resolvedMediaRoot "photo"),
    (Join-Path $resolvedMediaRoot "video")
)

try {
    foreach ($directory in $mediaDirectories) {
        if (Test-Path -LiteralPath $directory) {
            if (-not (Get-Item -LiteralPath $directory).PSIsContainer) {
                throw "Path media bukan direktori: $directory"
            }

            continue
        }

        New-Item -ItemType Directory -Path $directory -Force | Out-Null
        Write-Host "Membuat direktori media: $directory"
    }
} catch [System.UnauthorizedAccessException] {
    throw "Windows menolak pembuatan direktori di $resolvedMediaRoot. Jalankan PowerShell sebagai Administrator atau gunakan -MediaRoot ke folder yang diizinkan."
}

$env:MEDIA_ROOT = $resolvedMediaRoot
$resolvedComposeFile = (Resolve-Path -LiteralPath $ComposeFile).Path
$dockerArguments = @("compose", "-f", $resolvedComposeFile, "up", "-d")

if ($Build) {
    $dockerArguments += "--build"
}

& docker @dockerArguments

if ($LASTEXITCODE -ne 0) {
    throw "Docker Compose gagal dijalankan (exit code: $LASTEXITCODE)."
}
