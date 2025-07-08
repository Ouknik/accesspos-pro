@echo off
chcp 65001 >nul
echo ===============================================
echo    استخراج سريع لبنية قاعدة البيانات
echo    AccessPOS Pro - Database Structure Extractor
echo ===============================================
echo.

echo 🔄 بدء الاستخراج...
php artisan db:extract-structure

echo.
echo 📁 النتائج محفوظة في:
echo    - public\db.text (التقرير الكامل)
echo.

if exist "public\db.text" (
    echo ✅ تم إنجاز المهمة بنجاح!
    echo 📊 معلومات الملف:
    dir "public\db.text" | findstr "db.text"
) else (
    echo ❌ فشل في إنتاج الملف
)

echo.
echo اضغط أي مفتاح للإغلاق...
pause >nul
