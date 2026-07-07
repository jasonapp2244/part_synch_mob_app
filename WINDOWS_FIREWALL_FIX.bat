@echo off
echo ========================================
echo Windows Firewall Fix for SMTP
echo ========================================
echo.
echo This script will help you configure Windows Firewall
echo to allow SMTP connections on ports 587 and 465.
echo.
echo NOTE: You may need to run this as Administrator
echo.
pause

echo.
echo Step 1: Finding PHP path...
set PHP_PATH=C:\xampp\php\php.exe
if exist "%PHP_PATH%" (
    echo Found PHP at: %PHP_PATH%
) else (
    echo PHP not found at default location.
    echo Please update PHP_PATH in this script.
    pause
    exit
)

echo.
echo Step 2: Opening Windows Firewall...
echo Please manually add these rules:
echo.
echo 1. Allow PHP.exe through firewall:
echo    - Click "Allow an app through firewall"
echo    - Browse to: %PHP_PATH%
echo    - Check both Private and Public
echo.
echo 2. Allow Port 587 (Outbound):
echo    - Advanced Settings ^> Outbound Rules ^> New Rule
echo    - Port ^> TCP ^> 587
echo    - Allow connection
echo.
echo 3. Allow Port 465 (Outbound):
echo    - Advanced Settings ^> Outbound Rules ^> New Rule
echo    - Port ^> TCP ^> 465
echo    - Allow connection
echo.
pause

echo.
echo Opening Windows Firewall now...
start firewall.cpl

echo.
echo ========================================
echo After configuring firewall:
echo 1. Clear Laravel cache: php artisan config:clear
echo 2. Test: http://your-domain/api/test-email
echo ========================================
pause


