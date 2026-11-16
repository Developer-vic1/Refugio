[CmdletBinding()]
param(
    [switch]$NoBrowser,
    [ValidateRange(1024, 65535)][int]$Port = 8000
)
$ErrorActionPreference = 'Stop'
$project = Split-Path $PSScriptRoot -Parent
$stateDirectory = Join-Path $project 'storage\framework\launcher'
$logDirectory = Join-Path $project 'storage\logs'
$mutex = $null
$locked = $false

function Test-TcpPort([int]$Number) {
    $client = New-Object Net.Sockets.TcpClient
    try {
        $pending = $client.BeginConnect('127.0.0.1', $Number, $null, $null)
        if (-not $pending.AsyncWaitHandle.WaitOne(500)) { return $false }
        $client.EndConnect($pending)
        return $true
    } catch { return $false } finally { $client.Dispose() }
}

function Get-LocalStatus {
    $json = & $script:php -d display_errors=0 (Join-Path $project 'scripts\launcher-status.php') 2> (Join-Path $logDirectory 'launcher-check.log')
    if ($LASTEXITCODE -ne 0) { throw 'No se pudo leer .env. Revisa la configuración local del proyecto.' }
    $status = ($json -join "`n") | ConvertFrom-Json
    if ($status.error) { throw $status.error }
    return $status
}

function Get-WebStatus([int]$Number) {
    try {
        $result = Invoke-RestMethod -Uri "http://127.0.0.1:$Number/__refugio/health" -TimeoutSec 3 -Proxy $null
        if ($result.app -eq 'huella-serena' -and $result.instance -eq $script:local.instance) { return $result }
    } catch { }
    return $null
}

try {
    New-Item -ItemType Directory -Path $stateDirectory, $logDirectory -Force | Out-Null
    $hash = [Security.Cryptography.SHA256]::Create()
    try { $key = ([BitConverter]::ToString($hash.ComputeHash([Text.Encoding]::UTF8.GetBytes($project.ToLowerInvariant())))).Replace('-', '').Substring(0, 24) }
    finally { $hash.Dispose() }
    $mutex = New-Object Threading.Mutex($false, "Local\HuellaSerena-$key")
    try { $locked = $mutex.WaitOne(30000) } catch [Threading.AbandonedMutexException] { $locked = $true }
    if (-not $locked) { throw 'Refugio ya se está iniciando. Espera unos segundos y vuelve a abrirlo.' }

    $candidates = @()
    $command = Get-Command php.exe -ErrorAction SilentlyContinue
    if ($command) { $candidates += $command.Source }
    $laragon = Split-Path (Split-Path $project -Parent) -Parent
    $candidates += @(Get-ChildItem (Join-Path $laragon 'bin\php\*\php.exe') -ErrorAction SilentlyContinue | Sort-Object FullName -Descending | ForEach-Object FullName)
    $script:php = $candidates | Where-Object { Test-Path -LiteralPath $_ -PathType Leaf } | Select-Object -First 1
    if (-not $script:php) { throw 'No se encontró PHP. Instala PHP 8.2 o superior, o conserva PHP en la carpeta bin de Laragon.' }

    $script:local = Get-LocalStatus
    if (-not $local.php_ready -or @($local.missing_extensions).Count -gt 0) { throw 'PHP necesita la versión 8.2 o superior y las extensiones pdo_mysql, dom, mbstring, gd, fileinfo y openssl.' }
    if (-not $local.environment) { throw 'Falta .env. Configura la conexión privada de este proyecto antes de abrir Refugio.' }
    if (-not $local.dependencies) { throw 'Faltan dependencias PHP. Ejecuta composer install en la carpeta del proyecto una sola vez.' }

    $profileFile = Join-Path $stateDirectory 'mysql-local.json'
    if ($local.database -and $local.mysql_profile) {
        $local.mysql_profile | ConvertTo-Json | Set-Content -LiteralPath $profileFile -Encoding UTF8
    }
    if (-not $local.database) {
        if (-not $local.database_local -or (Test-TcpPort $local.database_port)) {
            throw 'MySQL no acepta la conexión configurada. Revisa DB_HOST, DB_PORT, DB_USER y DB_PASSWORD en tu .env privado.'
        }
        if (-not (Test-Path -LiteralPath $profileFile)) {
            throw 'Abre MySQL una vez y vuelve a iniciar Refugio. Se recordará su instalación para los siguientes arranques sin abrir Laragon.'
        }
        $profile = Get-Content -LiteralPath $profileFile -Raw | ConvertFrom-Json
        $mysql = Join-Path $profile.base_dir 'bin\mysqld.exe'
        $configuration = Join-Path $profile.base_dir 'my.ini'
        if ($profile.version -notmatch '^8\.' -or [int]$profile.port -ne $local.database_port -or
            -not (Test-Path -LiteralPath $mysql -PathType Leaf) -or
            -not (Test-Path -LiteralPath $configuration -PathType Leaf) -or
            -not (Test-Path -LiteralPath (Join-Path $profile.data_dir 'mysql') -PathType Container)) {
            throw 'La instalación recordada de MySQL cambió. Inicia el MySQL habitual una vez para actualizarla; no se creará ni reemplazará ninguna base.'
        }
        $mysqlArguments = @(('"--defaults-file={0}"' -f $configuration), ('"--basedir={0}"' -f $profile.base_dir.TrimEnd('\', '/')),
            ('"--datadir={0}"' -f $profile.data_dir.TrimEnd('\', '/')), "--port=$($profile.port)", '--bind-address=127.0.0.1', ('"--log-error={0}"' -f (Join-Path $logDirectory 'launcher-mysql.error.log')))
        $mysqlProcess = Start-Process -FilePath $mysql -ArgumentList $mysqlArguments -WorkingDirectory $profile.base_dir -WindowStyle Hidden -PassThru
        for ($attempt = 0; $attempt -lt 40; $attempt++) {
            Start-Sleep -Milliseconds 500
            if (Test-TcpPort $local.database_port) {
                $script:local = Get-LocalStatus
                if ($local.database) { break }
            }
            if ($mysqlProcess.HasExited) { break }
        }
        if (-not $local.database) { throw 'MySQL no pudo arrancar. Revisa storage\logs\launcher-mysql.error.log y la conexión de .env.' }
    }

    $selected = $null
    $web = $null
    foreach ($candidate in $Port..([Math]::Min($Port + 10, 65535))) {
        if (Test-TcpPort $candidate) {
            $web = Get-WebStatus $candidate
            if ($web) { $selected = $candidate; break }
        } elseif ($null -eq $selected) {
            # Antes de crear otro servidor, comprobar los demás puertos por si ya existe uno propio.
            $selected = $candidate
        }
    }
    if ($null -eq $selected) { throw 'No hay un puerto local disponible. Cierra otra aplicación local o usa el parámetro -Port.' }
    $server = $null
    if (-not $web) {
        # La consola independiente evita heredar las tuberías de `php start`.
        $arguments = @('-d', 'display_errors=0', '-d', 'log_errors=1', '-d', ('"error_log={0}"' -f (Join-Path $logDirectory "launcher-web-$selected.error.log")), '-S', "127.0.0.1:$selected", '-t', ('"{0}"' -f (Join-Path $project 'public')), ('"{0}"' -f (Join-Path $project 'public\dev-router.php')))
        $server = Start-Process -FilePath $script:php -ArgumentList $arguments -WorkingDirectory $project -WindowStyle Hidden -PassThru
        for ($attempt = 0; $attempt -lt 30; $attempt++) {
            Start-Sleep -Milliseconds 300
            $web = Get-WebStatus $selected
            if ($web -or $server.HasExited) { break }
        }
        if (-not $web) { throw 'No se pudo abrir Refugio. Revisa storage\logs\launcher-web y vuelve a intentarlo.' }
    }
    if (-not $web.database) { throw 'Refugio está abierto, pero MySQL todavía no responde. Revisa tu conexión local.' }
    $url = "http://127.0.0.1:$selected/"
    $result = [ordered]@{ app = 'Huella Serena'; url = $url; reused = ($null -eq $server); server_pid = $(if ($server) { $server.Id } else { $null }); instance = $local.instance }
    $result | ConvertTo-Json | Set-Content -LiteralPath (Join-Path $stateDirectory 'last-start.json') -Encoding UTF8
    if (-not $NoBrowser) { Start-Process $url }
    $result | ConvertTo-Json -Compress
} catch {
    $message = $_.Exception.Message
    if (-not $NoBrowser) {
        Add-Type -AssemblyName System.Windows.Forms
        [Windows.Forms.MessageBox]::Show($message, 'Huella Serena - Inicio', 'OK', 'Warning') | Out-Null
    } else { [Console]::Error.WriteLine($message) }
    exit 1
} finally {
    if ($locked) { $mutex.ReleaseMutex() }
    if ($mutex) { $mutex.Dispose() }
}
