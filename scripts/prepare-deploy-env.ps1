param(
    [string]$EnvPath = "deploy\.env"
)

$ErrorActionPreference = "Stop"

if (-not (Test-Path -Path $EnvPath)) {
    throw "File .env deploy tidak ditemukan: $EnvPath"
}

$content = Get-Content -Raw -Path $EnvPath

if ($content -match '(?m)^APP_KEY=\s*$' -or $content -match '(?m)^APP_KEY=base64:A{43}=$') {
    $bytes = New-Object byte[] 32
    $rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()

    try {
        $rng.GetBytes($bytes)
    } finally {
        $rng.Dispose()
    }

    $key = 'base64:' + [Convert]::ToBase64String($bytes)
    $content = $content -replace '(?m)^APP_KEY=\s*$', ('APP_KEY=' + $key)
    $content = $content -replace '(?m)^APP_KEY=base64:A{43}=$', ('APP_KEY=' + $key)
    Set-Content -Path $EnvPath -Value $content -NoNewline
}

$updatedContent = Get-Content -Raw -Path $EnvPath

if ($updatedContent -notmatch '(?m)^APP_KEY=base64:[A-Za-z0-9+/]{43}=$') {
    throw "APP_KEY deploy kosong atau tidak valid. Isi APP_KEY di $EnvPath atau jalankan ulang build-image.bat."
}
