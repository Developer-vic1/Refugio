@echo off
setlocal
powershell.exe -NoProfile -ExecutionPolicy RemoteSigned -File "%~dp0scripts\Crear-Acceso-Directo.ps1"
if errorlevel 1 pause
endlocal
