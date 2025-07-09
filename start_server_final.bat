@echo off
echo 🚀 تشغيل خادم AccessPOS Pro Laravel
echo =====================================
echo.
echo ⏳ جاري بدء تشغيل الخادم...
echo.

cd /d "c:\Users\OA\Desktop\isat mosstafa\accesspos-pro"

echo 🧹 تنظيف Cache أولاً...
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

echo.
echo ✅ تم تنظيف Cache بنجاح
echo.
echo 🌐 تشغيل الخادم على http://127.0.0.1:8000
echo.
echo 📋 روابط مهمة:
echo    - لوحة القيادة: http://127.0.0.1:8000/admin/tableau-de-bord-moderne
echo    - تسجيل الدخول: http://127.0.0.1:8000/login
echo.
echo ⚠️  للإيقاف اضغط Ctrl+C
echo.

php artisan serve --host=127.0.0.1 --port=8000

pause
