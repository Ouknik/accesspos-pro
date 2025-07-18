<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج التقرير المخصص</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 40px;
            text-align: center;
        }
        .message {
            color: #6c757d;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease;
            margin: 10px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }
        .btn-success:hover {
            box-shadow: 0 5px 15px rgba(40,167,69,0.4);
        }
        .redirect-note {
            background: #e8f5e8;
            border: 1px solid #d4edda;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 نموذج التقرير المخصص</h1>
        </div>
        
        <div class="content">
            <div class="redirect-note">
                <h3 style="margin: 0 0 15px 0;">🔄 تم تحديث النظام</h3>
                <p style="margin: 0; line-height: 1.6;">
                    تم تطوير نظام تقارير جديد محسن!<br>
                    الآن يمكنك الوصول إلى جميع التقارير من صفحة واحدة.
                </p>
            </div>

            <p class="message">
                يتم الآن توجيهك إلى صفحة التقارير الجديدة والمحسنة<br>
                التي تحتوي على جميع التقارير الأربعة بتصميم أفضل.
            </p>

            <div>
                <a href="/admin/excel-reports/test" class="btn btn-success">
                    🚀 الانتقال لصفحة التقارير الجديدة
                </a>
                <a href="/admin/excel-reports/papier-de-travail" class="btn">
                    📊 تحميل التقرير الشامل مباشرة
                </a>
            </div>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                <p style="color: #6c757d; font-size: 14px; margin: 0;">
                    <strong>ملاحظة:</strong> هذا المسار محفوظ للمتوافقية مع الإصدارات القديمة
                </p>
            </div>
        </div>
    </div>

    <script>
        // إعادة توجيه تلقائية بعد 3 ثوانٍ
        setTimeout(function() {
            window.location.href = '/admin/excel-reports/test';
        }, 3000);
    </script>
</body>
</html>
