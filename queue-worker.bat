@echo off
echo ========================================
echo Laravel Queue Worker
echo ========================================
echo.
echo Starting queue worker...
echo Press Ctrl+C to stop
echo.
echo ========================================
echo.

cd /d "%~dp0"
php artisan queue:work --tries=3 --timeout=60

pause
