@echo off
setlocal
set "REFUGIO_LAUNCHER=%~dp0scripts\Iniciar-Refugio.ps1"
powershell.exe -NoProfile -ExecutionPolicy RemoteSigned -Command "Start-Process powershell.exe -WindowStyle Hidden -ArgumentList ('-NoProfile -ExecutionPolicy RemoteSigned -WindowStyle Hidden -File ""' + $env:REFUGIO_LAUNCHER + '""')"
endlocal
