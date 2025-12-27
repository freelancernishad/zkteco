@echo off
cd /d "d:\Softweb system solution\School Management\zkteco"

:: Update IP Address in .env
@REM powershell -ExecutionPolicy Bypass -File update_ip.ps1

:: Start the Laravel Server in a new window
start "Laravel Server" php artisan serve --host=0.0.0.0

:: Wait for a few seconds to let the server start (optional but good practice)
timeout /t 5

:: Start the Scheduler in this window
php artisan schedule:work

pause
