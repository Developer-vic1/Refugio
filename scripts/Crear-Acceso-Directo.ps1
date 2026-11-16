[CmdletBinding()]
param()
$ErrorActionPreference = 'Stop'
$project = Split-Path $PSScriptRoot -Parent
$shell = New-Object -ComObject WScript.Shell
$desktop = [Environment]::GetFolderPath('Desktop')
foreach ($directory in @($project, $desktop)) {
    $path = Join-Path $directory 'Huella Serena - Refugio.lnk'
    $shortcut = $shell.CreateShortcut($path)
    $target = Join-Path $PSHOME 'powershell.exe'
    # También funciona al ejecutar este instalador desde PowerShell 7.
    if (-not (Test-Path -LiteralPath $target)) { $target = Join-Path $env:WINDIR 'System32\WindowsPowerShell\v1.0\powershell.exe' }
    $arguments = '-NoProfile -ExecutionPolicy RemoteSigned -WindowStyle Hidden -File "{0}"' -f (Join-Path $PSScriptRoot 'Iniciar-Refugio.ps1')
    if ((Test-Path -LiteralPath $path) -and $shortcut.Arguments -ne $arguments -and $shortcut.Arguments -ne ($arguments -replace 'RemoteSigned', 'Bypass')) {
        throw "Ya existe otro acceso con ese nombre: $path. No se reemplazó."
    }
    $shortcut.TargetPath = $target
    $shortcut.Arguments = $arguments
    $shortcut.WorkingDirectory = $project
    $shortcut.IconLocation = (Join-Path $project 'public\favicon.ico') + ',0'
    $shortcut.Description = 'Abrir Huella Serena: refugio, adopciones y comunidad'
    $shortcut.WindowStyle = 7
    $shortcut.Save()
    Write-Output $path
}
