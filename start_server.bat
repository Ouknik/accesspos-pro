@echo off
echo ===== تشغيل خادم Laravel =====
echo.
echo سيتم تشغيل الخادم على العنوان: http://localhost:8000
echo للوصول للوحة القيادة: http://localhost:8000/admin/tableau-de-bord-moderne
echo.
echo اضغط Ctrl+C لإيقاف الخادم
echo.
php artisan serve --port=8000
