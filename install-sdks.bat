@echo off
echo ========================================
echo Offload Images JS CSS - SDK Installer
echo ========================================
echo.
echo This will install the required cloud storage SDKs.
echo.

REM Check if composer is installed
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Composer is not installed!
    echo.
    echo Please install Composer first:
    echo 1. Visit: https://getcomposer.org/download/
    echo 2. Download and run Composer-Setup.exe
    echo 3. Follow the installation wizard
    echo 4. Restart this script
    echo.
    pause
    exit /b 1
)

echo Composer found! Installing SDKs...
echo.

REM Install dependencies
composer install --no-dev --optimize-autoloader

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo SUCCESS! SDKs installed successfully.
    echo ========================================
    echo.
    echo The following SDKs are now available:
    echo - AWS SDK for PHP (Amazon S3, DigitalOcean Spaces)
    echo - Google Cloud Storage SDK
    echo.
    echo You can now activate the plugin in WordPress!
    echo.
) else (
    echo.
    echo ========================================
    echo ERROR: Installation failed!
    echo ========================================
    echo.
    echo Please check the error messages above.
    echo.
    echo Need help? Contact: hello@gunjanjaswal.me
    echo.
)

pause
