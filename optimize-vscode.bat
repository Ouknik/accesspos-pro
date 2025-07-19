@echo off
echo =================================
echo  تحسين أداء Visual Studio Code
echo =================================

echo.
echo 1. إيقاف جميع عمليات VS Code...
taskkill /f /im Code.exe 2>nul
timeout /t 3 >nul

echo.
echo 2. تنظيف ملفات الـ Cache...
if exist "%APPDATA%\Code\logs" (
    rd /s /q "%APPDATA%\Code\logs" 2>nul
)

if exist "%APPDATA%\Code\CachedExtensions" (
    rd /s /q "%APPDATA%\Code\CachedExtensions" 2>nul
)

echo.
echo 3. تنظيف ملفات Workspace المؤقتة...
if exist "%APPDATA%\Code\User\workspaceStorage" (
    for /d %%i in ("%APPDATA%\Code\User\workspaceStorage\*") do (
        if exist "%%i\workspace.json" (
            del /q "%%i\workspace.json" 2>nul
        )
    )
)

echo.
echo 4. تنظيف ملفات Laravel الثقيلة...
if exist "storage\logs\laravel.log" (
    echo. > "storage\logs\laravel.log"
    echo تم تنظيف ملف Laravel Log
)

if exist "storage\framework\cache\data" (
    rd /s /q "storage\framework\cache\data" 2>nul
    echo تم تنظيف Cache Laravel
)

if exist "bootstrap\cache\config.php" (
    del /q "bootstrap\cache\config.php" 2>nul
)

if exist "bootstrap\cache\routes-v7.php" (
    del /q "bootstrap\cache\routes-v7.php" 2>nul
)

if exist "bootstrap\cache\services.php" (
    del /q "bootstrap\cache\services.php" 2>nul
)

echo.
echo 5. إعادة إنشاء Cache Laravel...
php artisan config:clear 2>nul
php artisan route:clear 2>nul
php artisan view:clear 2>nul
php artisan cache:clear 2>nul

echo.
echo =================================
echo  تم تحسين الأداء بنجاح!
echo  يمكنك الآن فتح VS Code
echo =================================
echo.
pause
