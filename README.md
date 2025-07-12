# AccessPos Pro - SB Admin 2 Edition

## 🏆 نظرة عامة

AccessPos Pro هو نظام إدارة نقاط البيع المتطور، تم تحويله بالكامل لاستخدام قالب SB Admin 2 الاحترافي. النظام يوفر حلول شاملة لإدارة المبيعات، المخزون، العملاء والتقارير المالية.

## ✨ المميزات الرئيسية

### 🎨 التصميم والواجهة
- **SB Admin 2 Template**: تصميم احترافي وعصري
- **Responsive Design**: متوافق مع جميع الأجهزة والشاشات
- **Dark Mode**: وضع داكن للعمل الليلي
- **Arabic RTL Support**: دعم كامل للغة العربية
- **Accessibility**: متوافق مع معايير الوصولية

### 📊 لوحة التحكم
- **Real-time Analytics**: تحليلات لحظية للمبيعات
- **Interactive Charts**: رسوم بيانية تفاعلية
- **KPI Cards**: بطاقات مؤشرات الأداء الرئيسية
- **Live Notifications**: إشعارات فورية
- **Quick Actions**: إجراءات سريعة

### 📦 إدارة المنتجات
- **Product Catalog**: كتالوج منتجات شامل
- **Inventory Management**: إدارة المخزون
- **Stock Alerts**: تنبيهات المخزون المنخفض
- **Barcode Support**: دعم الباركود
- **Image Gallery**: معرض صور المنتجات

### 💰 إدارة المبيعات
- **POS System**: نظام نقطة بيع متطور
- **Multi-payment Methods**: طرق دفع متعددة
- **Invoice Generation**: توليد الفواتير
- **Sales Reports**: تقارير المبيعات
- **Customer Management**: إدارة العملاء

### 📈 التقارير والتحليلات
- **Financial Reports**: تقارير مالية شاملة
- **Sales Analytics**: تحليلات المبيعات
- **Customer Insights**: رؤى العملاء
- **Export Options**: خيارات التصدير (PDF, Excel, CSV)
- **Scheduled Reports**: تقارير مجدولة

## 🛠️ التقنيات المستخدمة

### Backend
- **Laravel 10**: إطار عمل PHP حديث
- **MySQL 8**: قاعدة بيانات متقدمة
- **Redis**: نظام تخزين مؤقت
- **PHP 8.1+**: أحدث إصدار PHP

### Frontend
- **SB Admin 2**: قالب Bootstrap احترافي
- **Bootstrap 4**: إطار عمل CSS
- **jQuery 3.6**: مكتبة JavaScript
- **Chart.js**: رسوم بيانية تفاعلية
- **DataTables**: جداول بيانات متقدمة
- **SweetAlert2**: إشعارات جميلة

### Build Tools
- **Vite**: أداة بناء حديثة
- **npm**: مدير الحزم
- **Composer**: مدير تبعيات PHP

## 📋 متطلبات النظام

### الحد الأدنى
- **PHP**: 8.1 أو أحدث
- **MySQL**: 8.0 أو أحدث
- **Node.js**: 18 أو أحدث
- **Memory**: 2GB RAM
- **Storage**: 10GB متاح

### الموصى به
- **PHP**: 8.2+
- **MySQL**: 8.0+
- **Redis**: 6.0+
- **Nginx**: 1.18+
- **Memory**: 4GB+ RAM
- **Storage**: 50GB+ SSD

## ⚙️ التثبيت

### 1. تحضير البيئة
```bash
# تحديث النظام
sudo apt update && sudo apt upgrade -y

# تثبيت المتطلبات
sudo apt install php8.1 mysql-server nginx redis-server nodejs npm
```

### 2. استنساخ المشروع
```bash
git clone https://github.com/your-repo/accesspos-pro.git
cd accesspos-pro
```

### 3. تثبيت التبعيات
```bash
# PHP dependencies
composer install

# Node.js dependencies
npm install
```

### 4. إعداد البيئة
```bash
# نسخ ملف البيئة
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate

# إعداد قاعدة البيانات
php artisan migrate --seed
```

### 5. بناء الأصول
```bash
# للتطوير
npm run dev

# للإنتاج
npm run build
```

### 6. إعداد الصلاحيات
```bash
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

## 🚀 النشر للإنتاج

### استخدام سكريبت النشر التلقائي
```bash
# Linux/macOS
chmod +x deploy.sh
./deploy.sh

# Windows PowerShell
.\deploy.ps1
```

### النشر اليدوي
```bash
# نسخ ملف الإنتاج
cp .env.production .env

# تحسين Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# بناء الأصول
npm run build

# تشغيل الترحيلات
php artisan migrate --force
```

## 📚 الاستخدام

### تسجيل الدخول الافتراضي
```
البريد الإلكتروني: admin@accesspos.com
كلمة المرور: password123
```

### الصفحات الأساسية
- **لوحة التحكم**: `/admin/dashboard`
- **إدارة المنتجات**: `/admin/articles`
- **التقارير**: `/admin/rapports`
- **الإعدادات**: `/admin/settings`

## 🧪 الاختبار

### تشغيل الاختبارات
```bash
# جميع الاختبارات
php artisan test

# اختبارات محددة
php artisan test --filter=ArticleTest
```

### صفحات الاختبار
- **اختبار المتصفحات**: `/admin/test-pages`
- **اختبار الاستجابة**: `/admin/responsive-test`
- **اختبار النماذج**: `/admin/forms-test`
- **اختبار JavaScript**: `/admin/javascript-test`

## 📖 التوثيق

### ملفات التوثيق
- **دليل المستخدم**: `docs/user-manual.md`
- **توثيق API**: `docs/api-documentation.md`
- **دليل الأنماط**: `docs/style-guide.md`
- **توثيق المكونات**: `docs/components-documentation.md`
- **خطة الترحيل**: `docs/migration-plan.md`

## 🔧 التخصيص

### تخصيص الألوان
```css
/* في resources/css/custom-sb-admin.css */
:root {
    --primary-color: #4e73df;
    --secondary-color: #858796;
    --success-color: #1cc88a;
    --warning-color: #f6c23e;
    --danger-color: #e74a3b;
}
```

### إضافة صفحة جديدة
```php
// في routes/web.php
Route::get('/admin/new-page', function() {
    return view('admin.new-page-sb-admin');
})->name('admin.new-page');
```

## 🔒 الأمان

### الميزات الأمنية
- **CSRF Protection**: حماية من هجمات CSRF
- **SQL Injection Prevention**: منع حقن SQL
- **XSS Protection**: حماية من XSS
- **Rate Limiting**: تحديد معدل الطلبات
- **Secure Headers**: رؤوس أمان إضافية

### أفضل الممارسات
- استخدم كلمات مرور قوية
- فعّل المصادقة الثنائية
- حدّث النظام بانتظام
- راجع سجلات الأمان دورياً

## 📊 مراقبة الأداء

### المؤشرات الأساسية
- **وقت الاستجابة**: < 2 ثانية
- **استخدام الذاكرة**: < 80%
- **استخدام المعالج**: < 70%
- **وقت التشغيل**: > 99.9%

### أدوات المراقبة
- **Laravel Telescope**: للتطوير
- **New Relic**: للإنتاج
- **Sentry**: لتتبع الأخطاء

## 🆘 الدعم

### الحصول على المساعدة
- **التوثيق**: راجع ملفات `docs/`
- **المشاكل الشائعة**: في `docs/troubleshooting.md`
- **الدعم التقني**: support@accesspos.com

### المساهمة
1. Fork المشروع
2. إنشاء branch للميزة الجديدة
3. Commit التغييرات
4. Push للـ branch
5. إنشاء Pull Request

## 📝 الترخيص

هذا المشروع مرخص تحت [MIT License](LICENSE).

## 🏆 الفريق

### فريق التطوير
- **مطور رئيسي**: فريق AccessPos
- **مصمم UI/UX**: فريق التصميم
- **مطور Frontend**: فريق الواجهة الأمامية
- **مطور Backend**: فريق الخادم

## 🔄 سجل التغييرات

### الإصدار 2.0.0 (ديسمبر 2024)
- ✅ تحويل كامل لـ SB Admin 2
- ✅ إضافة Dark Mode
- ✅ تحسين الأداء والسرعة
- ✅ إضافة ميزات الوصولية
- ✅ تحديث شامل للتوثيق
- ✅ إضافة أدوات النشر التلقائي

### الإصدار 1.0.0 (سابقاً)
- نظام إدارة نقاط البيع الأساسي
- لوحة تحكم بسيطة
- إدارة المنتجات والمبيعات

## 📞 معلومات الاتصال

**AccessPos Pro**
- **الموقع**: https://accesspos.com
- **البريد**: info@accesspos.com
- **الدعم**: support@accesspos.com
- **الهاتف**: +123 456 789

---

**© 2024 AccessPos Pro. جميع الحقوق محفوظة.**
