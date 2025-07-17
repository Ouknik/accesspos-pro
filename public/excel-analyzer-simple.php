<?php
/**
 * سكريبت بسيط لتحليل ملف Excel بدون مكتبات خارجية
 * Simple Excel Analyzer without external libraries
 */

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محلل Excel البسيط</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .header { 
            background: linear-gradient(45deg, #4CAF50, #2196F3); 
            color: white; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
        }
        .card { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            margin: 15px 0; 
            overflow: hidden; 
        }
        .card-header { 
            background: #f8f9fa; 
            padding: 15px; 
            border-bottom: 1px solid #ddd; 
            font-weight: bold; 
        }
        .card-body { 
            padding: 15px; 
        }
        .success { 
            background: #d4edda; 
            border: 1px solid #c3e6cb; 
            color: #155724; 
            padding: 10px; 
            border-radius: 4px; 
            margin: 10px 0; 
        }
        .error { 
            background: #f8d7da; 
            border: 1px solid #f5c6cb; 
            color: #721c24; 
            padding: 10px; 
            border-radius: 4px; 
            margin: 10px 0; 
        }
        .info { 
            background: #d1ecf1; 
            border: 1px solid #bee5eb; 
            color: #0c5460; 
            padding: 10px; 
            border-radius: 4px; 
            margin: 10px 0; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: right; 
        }
        th { 
            background: #f2f2f2; 
        }
        .badge { 
            background: #007bff; 
            color: white; 
            padding: 4px 8px; 
            border-radius: 4px; 
            font-size: 12px; 
            margin: 2px; 
        }
        .btn { 
            background: #007bff; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            margin: 5px; 
        }
        .btn:hover { 
            background: #0056b3; 
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>📊 محلل ملف Excel - Papier de Travail</h1>
        <p>تحليل مبسط لملف العمل الخاص بك</p>
    </div>

    <?php
    
    // البحث عن الملف
    $searchPaths = [
        __DIR__ . '/excel/Papier de Travail (1).xlsx',
        __DIR__ . '/Papier de Travail (1).xlsx',
        __DIR__ . '/../storage/excel/Papier de Travail (1).xlsx'
    ];
    
    $filePath = null;
    foreach ($searchPaths as $path) {
        if (file_exists($path)) {
            $filePath = $path;
            break;
        }
    }
    
    echo '<div class="card">';
    echo '<div class="card-header">🔍 البحث عن الملف</div>';
    echo '<div class="card-body">';
    
    if ($filePath) {
        echo '<div class="success">';
        echo '<strong>✅ تم العثور على الملف!</strong><br>';
        echo 'المسار: ' . htmlspecialchars($filePath) . '<br>';
        echo 'الحجم: ' . formatFileSize(filesize($filePath)) . '<br>';
        echo 'آخر تعديل: ' . date('Y-m-d H:i:s', filemtime($filePath));
        echo '</div>';
        
        // تحليل الملف
        analyzeExcelFile($filePath);
        
    } else {
        echo '<div class="error">';
        echo '<strong>❌ لم يتم العثور على الملف!</strong><br>';
        echo 'المسارات المفحوصة:<br>';
        foreach ($searchPaths as $path) {
            echo '• ' . htmlspecialchars($path) . '<br>';
        }
        echo '</div>';
        
        echo '<div class="info">';
        echo '<strong>💡 إرشادات:</strong><br>';
        echo '1. تأكد من رفع الملف إلى مجلد public/excel/<br>';
        echo '2. تأكد من أن اسم الملف صحيح: "Papier de Travail (1).xlsx"<br>';
        echo '3. تحقق من صلاحيات القراءة للملف';
        echo '</div>';
    }
    
    echo '</div></div>';
    
    /**
     * تحليل ملف Excel كـ ZIP
     */
    function analyzeExcelFile($filePath) {
        
        echo '<div class="card">';
        echo '<div class="card-header">📁 هيكل الملف (ZIP Analysis)</div>';
        echo '<div class="card-body">';
        
        if (!class_exists('ZipArchive')) {
            echo '<div class="error">ZipArchive غير متوفر على هذا السيرفر</div>';
            analyzeFileBasic($filePath);
            echo '</div></div>';
            return;
        }
        
        $zip = new ZipArchive();
        $result = $zip->open($filePath);
        
        if ($result !== TRUE) {
            echo '<div class="error">فشل في فتح الملف كـ ZIP. كود الخطأ: ' . $result . '</div>';
            echo '</div></div>';
            return;
        }
        
        echo '<div class="success">تم فتح الملف بنجاح! عدد الملفات الداخلية: ' . $zip->numFiles . '</div>';
        
        // عرض محتويات الملف
        echo '<h4>📂 محتويات الملف:</h4>';
        echo '<table>';
        echo '<thead><tr><th>اسم الملف</th><th>الحجم</th><th>النوع</th></tr></thead>';
        echo '<tbody>';
        
        $worksheetCount = 0;
        $sharedStrings = null;
        
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $fileName = $stat['name'];
            $fileSize = formatFileSize($stat['size']);
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($fileName) . '</td>';
            echo '<td>' . $fileSize . '</td>';
            echo '<td>' . getFileType($fileName) . '</td>';
            echo '</tr>';
            
            if (strpos($fileName, 'worksheet') !== false) {
                $worksheetCount++;
            }
            
            if ($fileName === 'xl/sharedStrings.xml') {
                $sharedStrings = $zip->getFromName($fileName);
            }
        }
        
        echo '</tbody></table>';
        
        // تحليل النصوص المشتركة
        if ($sharedStrings) {
            analyzeSharedStrings($sharedStrings);
        }
        
        // تحليل أوراق العمل
        analyzeWorksheets($zip, $worksheetCount);
        
        $zip->close();
        echo '</div></div>';
    }
    
    /**
     * تحليل النصوص المشتركة
     */
    function analyzeSharedStrings($xmlContent) {
        echo '<div class="card">';
        echo '<div class="card-header">📝 النصوص المشتركة (Shared Strings)</div>';
        echo '<div class="card-body">';
        
        try {
            $xml = simplexml_load_string($xmlContent);
            $strings = [];
            
            if ($xml && $xml->si) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $strings[] = (string)$si->t;
                    }
                }
            }
            
            echo '<div class="success">تم العثور على ' . count($strings) . ' نص مشترك</div>';
            
            if (count($strings) > 0) {
                // تصنيف النصوص
                $categories = classifyStrings($strings);
                
                echo '<h4>🏷️ تصنيف المحتوى:</h4>';
                
                foreach ($categories as $category => $items) {
                    if (!empty($items)) {
                        echo '<h5>' . $category . ' (' . count($items) . '):</h5>';
                        $sample = array_slice($items, 0, 15);
                        foreach ($sample as $item) {
                            echo '<span class="badge">' . htmlspecialchars($item) . '</span> ';
                        }
                        if (count($items) > 15) {
                            echo '<br><small>... و ' . (count($items) - 15) . ' عنصر آخر</small>';
                        }
                        echo '<br><br>';
                    }
                }
                
                // تحليل نوع البيانات واقتراح التقارير
                suggestReports($categories);
            }
            
        } catch (Exception $e) {
            echo '<div class="error">خطأ في تحليل النصوص: ' . $e->getMessage() . '</div>';
        }
        
        echo '</div></div>';
    }
    
    /**
     * تصنيف النصوص حسب المحتوى
     */
    function classifyStrings($strings) {
        $categories = [
            'عناوين وحقول' => [],
            'بيانات مالية' => [],
            'تواريخ' => [],
            'أرقام ومقادير' => [],
            'أسماء وعملاء' => [],
            'أخرى' => []
        ];
        
        $financialKeywords = ['prix', 'montant', 'total', 'facture', 'dh', 'dinars', 'vente', 'achat', 'credit', 'debit'];
        $headerKeywords = ['nom', 'date', 'code', 'ref', 'designation', 'quantite', 'numero', 'client'];
        
        foreach ($strings as $string) {
            $string = trim($string);
            if (empty($string)) continue;
            
            $lowerString = mb_strtolower($string);
            
            // تحقق من التواريخ
            if (preg_match('/\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4}/', $string) || 
                preg_match('/\d{4}-\d{2}-\d{2}/', $string)) {
                $categories['تواريخ'][] = $string;
            }
            // تحقق من الأرقام
            elseif (is_numeric(str_replace([',', '.', ' ', 'DH'], '', $string)) && 
                    strlen(str_replace([',', '.', ' ', 'DH'], '', $string)) > 0) {
                $categories['أرقام ومقادير'][] = $string;
            }
            // تحقق من الكلمات المالية
            elseif (array_intersect(explode(' ', $lowerString), $financialKeywords)) {
                $categories['بيانات مالية'][] = $string;
            }
            // تحقق من العناوين
            elseif (strlen($string) < 30 && 
                    (array_intersect(explode(' ', $lowerString), $headerKeywords) || 
                     preg_match('/^[A-Za-z\s]{3,20}$/', $string))) {
                $categories['عناوين وحقول'][] = $string;
            }
            // تحقق من الأسماء
            elseif (strlen($string) > 2 && strlen($string) < 50 && 
                    preg_match('/[A-Za-z\s]/', $string)) {
                $categories['أسماء وعملاء'][] = $string;
            }
            else {
                $categories['أخرى'][] = $string;
            }
        }
        
        return $categories;
    }
    
    /**
     * اقتراح التقارير بناءً على المحتوى
     */
    function suggestReports($categories) {
        echo '<div class="card">';
        echo '<div class="card-header">💡 اقتراحات التقارير</div>';
        echo '<div class="card-body">';
        
        $suggestions = [];
        
        if (!empty($categories['بيانات مالية']) || !empty($categories['أرقام ومقادير'])) {
            $suggestions[] = '📊 تقرير مالي شامل (المبيعات، الأرباح، النفقات)';
            $suggestions[] = '💰 تحليل التدفق النقدي';
        }
        
        if (!empty($categories['تواريخ'])) {
            $suggestions[] = '📅 تقرير زمني (أداء حسب الفترة)';
            $suggestions[] = '📈 تحليل الاتجاهات الزمنية';
        }
        
        if (!empty($categories['أسماء وعملاء'])) {
            $suggestions[] = '👥 تقرير العملاء (أداء العملاء، الديون)';
            $suggestions[] = '🎯 تحليل قاعدة العملاء';
        }
        
        if (!empty($categories['عناوين وحقول'])) {
            $suggestions[] = '📋 تقرير البيانات الهيكلية';
        }
        
        if (empty($suggestions)) {
            $suggestions[] = '📄 تقرير عام للبيانات';
        }
        
        echo '<h4>🎯 التقارير المقترحة لهذا الملف:</h4>';
        echo '<ul>';
        foreach ($suggestions as $suggestion) {
            echo '<li>' . $suggestion . '</li>';
        }
        echo '</ul>';
        
        echo '<div class="info">';
        echo '<strong>🔧 خطوات التنفيذ:</strong><br>';
        echo '1. استخراج البيانات من هذا الملف<br>';
        echo '2. تنظيف وتنسيق البيانات<br>';
        echo '3. ربط البيانات مع نظام AccessPos Pro<br>';
        echo '4. إنشاء واجهات التقارير المناسبة<br>';
        echo '5. جدولة التقارير التلقائية';
        echo '</div>';
        
        echo '</div></div>';
    }
    
    /**
     * تحليل أوراق العمل
     */
    function analyzeWorksheets($zip, $count) {
        echo '<div class="card">';
        echo '<div class="card-header">📊 أوراق العمل (' . $count . ' ورقة)</div>';
        echo '<div class="card-body">';
        
        // قراءة معلومات الأوراق من workbook.xml
        $workbookXML = $zip->getFromName('xl/workbook.xml');
        if ($workbookXML) {
            try {
                $xml = simplexml_load_string($workbookXML);
                if ($xml && $xml->sheets && $xml->sheets->sheet) {
                    echo '<table>';
                    echo '<thead><tr><th>اسم الورقة</th><th>معرف</th><th>الحالة</th></tr></thead>';
                    echo '<tbody>';
                    
                    foreach ($xml->sheets->sheet as $sheet) {
                        $name = (string)$sheet['name'];
                        $id = (string)$sheet['sheetId'];
                        
                        echo '<tr>';
                        echo '<td><strong>' . htmlspecialchars($name) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($id) . '</td>';
                        echo '<td><span class="badge">نشطة</span></td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                }
            } catch (Exception $e) {
                echo '<div class="error">خطأ في قراءة معلومات الأوراق: ' . $e->getMessage() . '</div>';
            }
        }
        
        echo '</div></div>';
    }
    
    /**
     * تحليل أساسي للملف
     */
    function analyzeFileBasic($filePath) {
        echo '<div class="card">';
        echo '<div class="card-header">📋 تحليل أساسي للملف</div>';
        echo '<div class="card-body">';
        
        $info = [
            'الاسم' => basename($filePath),
            'المسار' => $filePath,
            'الحجم' => formatFileSize(filesize($filePath)),
            'النوع' => mime_content_type($filePath),
            'آخر تعديل' => date('Y-m-d H:i:s', filemtime($filePath)),
            'قابل للقراءة' => is_readable($filePath) ? 'نعم' : 'لا'
        ];
        
        echo '<table>';
        foreach ($info as $key => $value) {
            echo '<tr><td><strong>' . $key . '</strong></td><td>' . htmlspecialchars($value) . '</td></tr>';
        }
        echo '</table>';
        
        echo '<div class="info">';
        echo '<strong>📝 ملاحظات:</strong><br>';
        echo '• هذا ملف Excel (.xlsx) وهو في الأساس ملف ZIP مضغوط<br>';
        echo '• يحتوي على XML files تصف البيانات والتنسيق<br>';
        echo '• يمكن قراءته برمجياً لاستخراج البيانات<br>';
        echo '• مناسب لإنشاء تقارير "ورقة العمل" المطلوبة';
        echo '</div>';
        
        echo '</div></div>';
    }
    
    /**
     * تحديد نوع الملف
     */
    function getFileType($fileName) {
        if (strpos($fileName, 'worksheet') !== false) return '📊 ورقة عمل';
        if (strpos($fileName, 'sharedStrings') !== false) return '📝 نصوص مشتركة';
        if (strpos($fileName, 'workbook') !== false) return '📚 كتاب العمل';
        if (strpos($fileName, 'styles') !== false) return '🎨 أنماط';
        if (strpos($fileName, 'theme') !== false) return '🎨 قالب';
        if (strpos($fileName, '.rels') !== false) return '🔗 علاقات';
        if (strpos($fileName, '.xml') !== false) return '📄 XML';
        return '📁 أخرى';
    }
    
    /**
     * تنسيق حجم الملف
     */
    function formatFileSize($bytes) {
        $units = array('B', 'KB', 'MB', 'GB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    ?>
    
    <!-- أدوات إضافية -->
    <div class="card">
        <div class="card-header">🛠️ أدوات مفيدة</div>
        <div class="card-body">
            <button onclick="window.location.reload()" class="btn">🔄 إعادة تحميل</button>
            <button onclick="window.print()" class="btn">🖨️ طباعة التقرير</button>
            <button onclick="window.history.back()" class="btn">← رجوع</button>
            
            <div style="margin-top: 15px;">
                <strong>📞 للدعم:</strong><br>
                • في حالة الحاجة لمساعدة في إنشاء التقارير<br>
                • لربط هذا الملف مع نظام AccessPos Pro<br>
                • لتطوير تقارير مخصصة
            </div>
        </div>
    </div>

</div>

</body>
</html>
