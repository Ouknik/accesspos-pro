# توثيق API - AccessPos Pro

## 📋 نظرة عامة

يوفر نظام AccessPos Pro مجموعة من الـ API endpoints للتفاعل مع البيانات والوظائف المختلفة. جميع الـ APIs محمية بنظام المصادقة ويتطلب تسجيل الدخول أولاً.

## 🔐 المصادقة (Authentication)

### نقاط النهاية للمصادقة

#### تسجيل الدخول
```http
POST /login
```

**المعاملات:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**الاستجابة:**
```json
{
  "success": true,
  "message": "تم تسجيل الدخول بنجاح",
  "redirect": "/admin/dashboard"
}
```

#### تسجيل الخروج
```http
POST /logout
```

#### طلب إعادة تعيين كلمة المرور
```http
POST /forgot-password
```

**المعاملات:**
```json
{
  "email": "user@example.com"
}
```

#### إعادة تعيين كلمة المرور
```http
POST /reset-password
```

**المعاملات:**
```json
{
  "token": "reset_token",
  "email": "user@example.com",
  "password": "new_password",
  "password_confirmation": "new_password"
}
```

## 📊 Dashboard APIs

### البيانات المباشرة للوحة التحكم
```http
GET /admin/api/live-data
```

**الاستجابة:**
```json
{
  "total_sales": 15420.50,
  "orders_count": 125,
  "customers_count": 89,
  "profit": 3240.75,
  "charts_data": {
    "sales_chart": [...],
    "products_chart": [...],
    "branches_chart": [...]
  },
  "recent_orders": [...],
  "low_stock_alerts": [...]
}
```

### تفاصيل رقم الأعمال
```http
GET /admin/api/chiffre-affaires-details
```

**معاملات الاستعلام:**
- `start_date`: تاريخ البداية (YYYY-MM-DD)
- `end_date`: تاريخ النهاية (YYYY-MM-DD)
- `branch_id`: معرف الفرع (اختياري)

**الاستجابة:**
```json
{
  "total_revenue": 25600.00,
  "daily_breakdown": {
    "2024-12-01": 1200.00,
    "2024-12-02": 1350.00,
    "..."
  },
  "by_branch": {
    "branch_1": 15000.00,
    "branch_2": 10600.00
  },
  "by_payment_method": {
    "cash": 12000.00,
    "card": 8600.00,
    "mobile": 5000.00
  }
}
```

### تفاصيل المنتجات منخفضة المخزون
```http
GET /admin/api/stock-rupture-details
```

**الاستجابة:**
```json
{
  "low_stock_products": [
    {
      "id": 1,
      "name": "منتج 1",
      "current_stock": 5,
      "minimum_stock": 10,
      "status": "danger"
    },
    "..."
  ],
  "out_of_stock_count": 3,
  "low_stock_count": 7,
  "total_affected": 10
}
```

### تفاصيل العملاء المميزين
```http
GET /admin/api/top-clients-details
```

**الاستجابة:**
```json
{
  "top_customers": [
    {
      "id": 1,
      "name": "أحمد محمد",
      "total_purchases": 5600.00,
      "orders_count": 15,
      "last_purchase": "2024-12-01",
      "loyalty_points": 560
    },
    "..."
  ],
  "total_customers": 89,
  "active_customers": 67
}
```

### تفاصيل الأداء الساعي
```http
GET /admin/api/performance-horaire-details
```

**معاملات الاستعلام:**
- `date`: التاريخ (YYYY-MM-DD، افتراضي: اليوم)

**الاستجابة:**
```json
{
  "hourly_sales": {
    "09:00": 245.50,
    "10:00": 356.75,
    "11:00": 445.25,
    "..."
  },
  "peak_hour": "14:00",
  "peak_sales": 678.90,
  "total_daily_sales": 4567.80
}
```

### تفاصيل طرق الدفع
```http
GET /admin/api/modes-paiement-details
```

**الاستجابة:**
```json
{
  "payment_methods": [
    {
      "method": "نقداً",
      "total": 12500.00,
      "percentage": 45.5,
      "transactions_count": 89
    },
    {
      "method": "بطاقة ائتمان",
      "total": 8900.00,
      "percentage": 32.4,
      "transactions_count": 56
    },
    "..."
  ],
  "total_amount": 27500.00
}
```

### تفاصيل حالة الطاولات
```http
GET /admin/api/etat-tables-details
```

**الاستجابة:**
```json
{
  "tables": [
    {
      "id": 1,
      "number": "T001",
      "status": "occupied",
      "customer_name": "محمد أحمد",
      "order_time": "2024-12-01 14:30:00",
      "total": 125.50
    },
    "..."
  ],
  "summary": {
    "total_tables": 20,
    "occupied": 12,
    "available": 8,
    "reserved": 0
  }
}
```

## 📦 Articles/Products APIs

### قائمة المنتجات
```http
GET /admin/articles
```

**معاملات الاستعلام:**
- `page`: رقم الصفحة (افتراضي: 1)
- `per_page`: عدد العناصر لكل صفحة (افتراضي: 15)
- `search`: البحث في الاسم أو الوصف
- `family_id`: فلترة حسب العائلة
- `status`: فلترة حسب الحالة (active, inactive)

### إنشاء منتج جديد
```http
POST /admin/articles
```

**المعاملات:**
```json
{
  "nom": "اسم المنتج",
  "description": "وصف المنتج",
  "prix_achat": 100.00,
  "prix_vente": 150.00,
  "stock_initial": 50,
  "stock_minimum": 10,
  "famille_id": 1,
  "sous_famille_id": 2,
  "code_barre": "1234567890",
  "image": "base64_image_data"
}
```

### عرض منتج محدد
```http
GET /admin/articles/{id}
```

### تحديث منتج
```http
PUT /admin/articles/{id}
```

### تبديل حالة المنتج
```http
PATCH /admin/articles/{id}/toggle-status
```

### إضافة مخزون
```http
POST /admin/articles/{id}/add-stock
```

**المعاملات:**
```json
{
  "quantity": 25,
  "reason": "استلام شحنة جديدة"
}
```

### احصائيات المنتجات
```http
GET /admin/articles/api/stats
```

**الاستجابة:**
```json
{
  "total_products": 156,
  "active_products": 142,
  "low_stock_products": 8,
  "out_of_stock_products": 3,
  "total_value": 45600.00,
  "top_selling": [
    {
      "id": 1,
      "name": "منتج 1",
      "sales_count": 45
    },
    "..."
  ]
}
```

### قائمة العائلات
```http
GET /admin/articles/api/families
```

**الاستجابة:**
```json
[
  {
    "id": 1,
    "nom": "مشروبات",
    "products_count": 25
  },
  {
    "id": 2,
    "nom": "أطعمة",
    "products_count": 45
  }
]
```

### قائمة العائلات الفرعية
```http
GET /admin/articles/api/sub-families/{family_id}
```

**الاستجابة:**
```json
[
  {
    "id": 1,
    "nom": "مشروبات ساخنة",
    "famille_id": 1,
    "products_count": 12
  },
  {
    "id": 2,
    "nom": "مشروبات باردة",
    "famille_id": 1,
    "products_count": 13
  }
]
```

## 📈 Reports APIs

### فهرس التقارير
```http
GET /admin/rapports
```

### توليد تقرير
```http
POST /admin/rapports/generate
```

**المعاملات:**
```json
{
  "type": "sales|products|customers",
  "start_date": "2024-11-01",
  "end_date": "2024-11-30",
  "format": "pdf|excel|csv",
  "branch_id": 1
}
```

### تقرير شامل
```http
GET /admin/rapports/complet
```

### تقرير شامل PDF
```http
GET /admin/rapports/complet/pdf
```

### تقرير سريع
```http
GET /admin/rapports/rapide
```

## 📤 Export APIs

### تصدير بيانات لوحة التحكم
```http
GET /admin/api/dashboard-export
```

**معاملات الاستعلام:**
- `format`: نوع التصدير (excel, csv, pdf)
- `data_type`: نوع البيانات (sales, products, customers, all)

### تصدير منتجات
```http
GET /admin/articles/export
```

**معاملات الاستعلام:**
- `format`: excel|csv|pdf
- `family_id`: معرف العائلة (اختياري)
- `status`: حالة المنتجات (اختياري)

## 🔧 أكواد الاستجابة

### أكواد النجاح
- `200`: نجح الطلب
- `201`: تم الإنشاء بنجاح
- `204`: تم الحذف بنجاح

### أكواد الخطأ
- `400`: طلب غير صحيح
- `401`: غير مصادق عليه
- `403`: غير مسموح
- `404`: غير موجود
- `422`: خطأ في التحقق من صحة البيانات
- `500`: خطأ في الخادم

## 📝 أمثلة الاستخدام

### JavaScript - جلب البيانات المباشرة
```javascript
async function fetchLiveData() {
    try {
        const response = await fetch('/admin/api/live-data', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        if (!response.ok) {
            throw new Error('فشل في جلب البيانات');
        }
        
        const data = await response.json();
        updateDashboard(data);
    } catch (error) {
        console.error('خطأ:', error);
        showAlert('فشل في تحديث البيانات', 'error');
    }
}
```

### JavaScript - إضافة منتج جديد
```javascript
async function createProduct(productData) {
    try {
        const response = await fetch('/admin/articles', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(productData)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showAlert('تم إنشاء المنتج بنجاح', 'success');
            window.location.href = '/admin/articles';
        } else {
            showAlert(result.message || 'فشل في إنشاء المنتج', 'error');
        }
    } catch (error) {
        console.error('خطأ:', error);
        showAlert('حدث خطأ غير متوقع', 'error');
    }
}
```

### PHP - استدعاء API داخلياً
```php
// في Controller
public function getDashboardData()
{
    $liveData = app(TableauDeBordController::class)->getLiveData();
    $stockData = app(TableauDeBordController::class)->getStockRuptureDetails();
    
    return response()->json([
        'dashboard' => $liveData,
        'stock' => $stockData
    ]);
}
```

## 🔄 معدل التحديث

### البيانات المباشرة
- **Dashboard Live Data**: كل 30 ثانية
- **Stock Alerts**: كل دقيقتين
- **Order Status**: كل 10 ثواني

### كاش البيانات
- **Products List**: 5 دقائق
- **Categories**: 15 دقيقة
- **Reports**: 1 ساعة

## 🛡️ الأمان

### التوكنات والحماية
- جميع طلبات POST/PUT/DELETE تتطلب CSRF token
- المصادقة مطلوبة لجميع APIs
- تسجيل جميع العمليات الحساسة
- تحديد معدل الطلبات (Rate Limiting)

### Middleware المستخدم
- `auth`: مصادقة المستخدم
- `admin`: صلاحيات الإدارة
- `throttle`: تحديد معدل الطلبات

## 📚 موارد إضافية

### أدوات التطوير
- **Postman Collection**: متوفرة عند الطلب
- **API Testing**: متضمن في نظام الاختبار
- **Documentation**: تحديث مستمر

### الدعم
- **فريق التطوير**: dev@accesspos.com
- **API Support**: api-support@accesspos.com
- **Documentation Updates**: تحديث شهري

---

*آخر تحديث: ديسمبر 2024*
*إصدار API: v2.0*
