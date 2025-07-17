<?php
/**
 * سكريبت قراءة وتحليل ملفات Excel
 * Simple Excel Reader and Analyzer
 */

// إعدادات الخطأ لعرض جميع الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);

// بداية HTML
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قارئ ملفات Excel - Excel Reader</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem 0; }
        .card { border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-radius: 10px; margin-bottom: 2rem; }
        .card-header { background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-bottom: 2px solid #dee2e6; }
        .table-responsive { max-height: 400px; overflow-y: auto; }
        .file-info { background: #e3f2fd; border-radius: 8px; padding: 1rem; margin: 1rem 0; }
        .error-box { background: #ffebee; border: 1px solid #f44336; border-radius: 8px; padding: 1rem; color: #d32f2f; }
        .success-box { background: #e8f5e8; border: 1px solid #4caf50; border-radius: 8px; padding: 1rem; color: #2e7d32; }
        .badge-custom { background: linear-gradient(45deg, #667eea, #764ba2); color: white; }
    </style>
</head>
<body>

<div class="header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1><i class="fas fa-file-excel"></i> قارئ ملفات Excel</h1>
                <p class="mb-0">أداة تحليل وقراءة ملفات Excel بسهولة</p>
            </div>
            <div class="col-md-4 text-end">
                <button onclick="window.location.reload()" class="btn btn-light">
                    <i class="fas fa-refresh"></i> إعادة تحميل
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <?php
    
    // قائمة بمسارات الملفات المحتملة
    $possiblePaths = [
        __DIR__ . '/excel/Papier de Travail.xlsx',
        __DIR__ . '/excel/Papier de Travail (1).xlsx',
        __DIR__ . '/Papier de Travail.xlsx',
        __DIR__ . '/Papier de Travail (1).xlsx',
        dirname(__DIR__) . '/storage/app/excel/Papier de Travail.xlsx',
        dirname(__DIR__) . '/storage/app/excel/Papier de Travail (1).xlsx'
    ];
    
    $filePath = null;
    
    // البحث عن الملف في المسارات المحتملة
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $filePath = $path;
            break;
        }
    }
    
    // عرض معلومات البحث
    echo '<div class="card">';
    echo '<div class="card-header"><h5><i class="fas fa-search"></i> البحث عن الملف</h5></div>';
    echo '<div class="card-body">';
    
    if ($filePath) {
        echo '<div class="success-box">';
        echo '<h6><i class="fas fa-check-circle"></i> تم العثور على الملف!</h6>';
        echo '<p><strong>المسار:</strong> ' . htmlspecialchars($filePath) . '</p>';
        echo '<p><strong>الحجم:</strong> ' . formatBytes(filesize($filePath)) . '</p>';
        echo '<p><strong>آخر تعديل:</strong> ' . date('Y-m-d H:i:s', filemtime($filePath)) . '</p>';
        echo '</div>';
    } else {
        echo '<div class="error-box">';
        echo '<h6><i class="fas fa-exclamation-triangle"></i> لم يتم العثور على الملف!</h6>';
        echo '<p>تم البحث في المسارات التالية:</p>';
        echo '<ul>';
        foreach ($possiblePaths as $path) {
            echo '<li>' . htmlspecialchars($path) . '</li>';
        }
        echo '</ul>';
        
        // محاولة إنشاء مجلد excel إذا لم يكن موجوداً
        $excelDir = __DIR__ . '/excel';
        if (!is_dir($excelDir)) {
            if (mkdir($excelDir, 0755, true)) {
                echo '<p class="text-info"><i class="fas fa-folder-plus"></i> تم إنشاء مجلد excel جديد في: ' . $excelDir . '</p>';
                echo '<p>يرجى رفع ملف "Papier de Travail (1).xlsx" إلى هذا المجلد.</p>';
            }
        }
        echo '</div>';
    }
    
    echo '</div></div>';
    
    // إذا وُجد الملف، نحاول قراءته
    if ($filePath) {
        
        // محاولة قراءة الملف بطرق مختلفة
        echo '<div class="card">';
        echo '<div class="card-header"><h5><i class="fas fa-cog"></i> طرق القراءة المتاحة</h5></div>';
        echo '<div class="card-body">';
        
        $readers = [
            'SimpleXLSX' => function_exists('ZipArchive'),
            'CSV Conversion' => true,
            'Manual Parsing' => true
        ];
        
        foreach ($readers as $readerName => $available) {
            $status = $available ? 'متاح' : 'غير متاح';
            $badgeClass = $available ? 'bg-success' : 'bg-danger';
            echo '<span class="badge ' . $badgeClass . ' me-2">' . $readerName . ': ' . $status . '</span>';
        }
        
        echo '</div></div>';
        
        // محاولة قراءة الملف كـ ZIP (لأن Excel هو ملف ZIP)
        if (class_exists('ZipArchive')) {
            readExcelAsZip($filePath);
        } else {
            echo '<div class="error-box">ZipArchive غير متوفر. سنحاول طرق أخرى...</div>';
        }
        
        // تحليل عام للملف
        analyzeFileGeneral($filePath);
    }
    
    /**
     * قراءة ملف Excel كـ ZIP
     */
    function readExcelAsZip($filePath) {
        echo '<div class="card">';
        echo '<div class="card-header"><h5><i class="fas fa-archive"></i> محتويات الملف (ZIP Structure)</h5></div>';
        echo '<div class="card-body">';
        
        try {
            $zip = new ZipArchive();
            if ($zip->open($filePath) === TRUE) {
                echo '<div class="success-box">';
                echo '<h6>تم فتح الملف بنجاح كـ ZIP!</h6>';
                echo '<p><strong>عدد الملفات الداخلية:</strong> ' . $zip->numFiles . '</p>';
                echo '</div>';
                
                echo '<div class="table-responsive">';
                echo '<table class="table table-striped">';
                echo '<thead><tr><th>اسم الملف</th><th>الحجم</th><th>النوع</th></tr></thead>';
                echo '<tbody>';
                
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    $fileName = $stat['name'];
                    $fileSize = formatBytes($stat['size']);
                    $fileType = getFileTypeFromName($fileName);
                    
                    echo '<tr>';
                    echo '<td><code>' . htmlspecialchars($fileName) . '</code></td>';
                    echo '<td>' . $fileSize . '</td>';
                    echo '<td><span class="badge badge-custom">' . $fileType . '</span></td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
                echo '</div>';
                
                // قراءة محتوى SharedStrings (النصوص المشتركة)
                readSharedStrings($zip);
                
                // قراءة بيانات الأوراق
                readWorksheetData($zip);
                
                $zip->close();
            } else {
                echo '<div class="error-box">فشل في فتح الملف كـ ZIP</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error-box">خطأ: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div></div>';
    }
    
    /**
     * قراءة النصوص المشتركة من Excel
     */
    function readSharedStrings($zip) {
        $sharedStringsXML = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXML) {
            echo '<div class="card mt-3">';
            echo '<div class="card-header"><h6><i class="fas fa-font"></i> النصوص المشتركة (Shared Strings)</h6></div>';
            echo '<div class="card-body">';
            
            try {
                $xml = simplexml_load_string($sharedStringsXML);
                $strings = [];
                
                foreach ($xml->si as $si) {
                    $strings[] = (string)$si->t;
                }
                
                echo '<div class="success-box">';
                echo '<p><strong>عدد النصوص:</strong> ' . count($strings) . '</p>';
                echo '<p><strong>عينة من النصوص:</strong></p>';
                echo '<div class="row">';
                
                $sample = array_slice($strings, 0, 20);
                foreach ($sample as $index => $string) {
                    if (!empty(trim($string))) {
                        echo '<div class="col-md-6 col-lg-4 mb-2">';
                        echo '<span class="badge bg-info">' . htmlspecialchars($string) . '</span>';
                        echo '</div>';
                    }
                }
                
                echo '</div>';
                echo '</div>';
                
                // محاولة تحليل المحتوى
                analyzeStrings($strings);
                
            } catch (Exception $e) {
                echo '<div class="error-box">خطأ في قراءة النصوص: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            
            echo '</div></div>';
        }
    }
    
    /**
     * قراءة بيانات الأوراق
     */
    function readWorksheetData($zip) {
        // قراءة معلومات الأوراق من workbook.xml
        $workbookXML = $zip->getFromName('xl/workbook.xml');
        if ($workbookXML) {
            echo '<div class="card mt-3">';
            echo '<div class="card-header"><h6><i class="fas fa-table"></i> أوراق العمل (Worksheets)</h6></div>';
            echo '<div class="card-body">';
            
            try {
                $xml = simplexml_load_string($workbookXML);
                $sheets = [];
                
                foreach ($xml->sheets->sheet as $sheet) {
                    $sheets[] = [
                        'name' => (string)$sheet['name'],
                        'id' => (string)$sheet['sheetId'],
                        'rId' => (string)$sheet['r:id']
                    ];
                }
                
                echo '<div class="table-responsive">';
                echo '<table class="table table-striped">';
                echo '<thead><tr><th>اسم الورقة</th><th>معرف الورقة</th><th>معرف العلاقة</th><th>البيانات</th></tr></thead>';
                echo '<tbody>';
                
                foreach ($sheets as $sheet) {
                    echo '<tr>';
                    echo '<td><strong>' . htmlspecialchars($sheet['name']) . '</strong></td>';
                    echo '<td>' . htmlspecialchars($sheet['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($sheet['rId']) . '</td>';
                    echo '<td>';
                    
                    // محاولة قراءة بيانات الورقة
                    $sheetXML = $zip->getFromName('xl/worksheets/sheet' . $sheet['id'] . '.xml');
                    if ($sheetXML) {
                        $sheetData = analyzeSheetData($sheetXML);
                        echo '<span class="badge bg-success">موجودة (' . $sheetData['cellCount'] . ' خلية)</span>';
                    } else {
                        echo '<span class="badge bg-warning">غير موجودة</span>';
                    }
                    
                    echo '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="error-box">خطأ في قراءة الأوراق: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            
            echo '</div></div>';
        }
    }
    
    /**
     * تحليل بيانات ورقة عمل
     */
    function analyzeSheetData($sheetXML) {
        try {
            $xml = simplexml_load_string($sheetXML);
            $cellCount = 0;
            $dimensions = '';
            
            // الحصول على أبعاد الورقة
            if (isset($xml->dimension['ref'])) {
                $dimensions = (string)$xml->dimension['ref'];
            }
            
            // عد الخلايا
            if (isset($xml->sheetData->row)) {
                foreach ($xml->sheetData->row as $row) {
                    if (isset($row->c)) {
                        $cellCount += count($row->c);
                    }
                }
            }
            
            return [
                'cellCount' => $cellCount,
                'dimensions' => $dimensions
            ];
            
        } catch (Exception $e) {
            return ['cellCount' => 0, 'dimensions' => ''];
        }
    }
    
    /**
     * تحليل النصوص لاستخراج معلومات مفيدة
     */
    function analyzeStrings($strings) {
        echo '<div class="card mt-3">';
        echo '<div class="card-header"><h6><i class="fas fa-chart-bar"></i> تحليل المحتوى</h6></div>';
        echo '<div class="card-body">';
        
        // تصنيف النصوص
        $categories = [
            'تواريخ' => [],
            'أرقام' => [],
            'عناوين محتملة' => [],
            'كلمات مالية' => [],
            'أخرى' => []
        ];
        
        $financialKeywords = ['prix', 'montant', 'total', 'dh', 'dinars', 'vente', 'achat', 'facture', 'client', 'fournisseur'];
        $headerKeywords = ['nom', 'date', 'code', 'reference', 'designation', 'quantite'];
        
        foreach ($strings as $string) {
            $string = trim($string);
            if (empty($string)) continue;
            
            if (preg_match('/\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}/', $string)) {
                $categories['تواريخ'][] = $string;
            } elseif (is_numeric(str_replace([',', '.', ' '], '', $string))) {
                $categories['أرقام'][] = $string;
            } elseif (strlen($string) < 20 && (
                str_contains(strtolower($string), 'nom') ||
                str_contains(strtolower($string), 'date') ||
                str_contains(strtolower($string), 'code') ||
                in_array(strtolower($string), $headerKeywords)
            )) {
                $categories['عناوين محتملة'][] = $string;
            } elseif (array_intersect(explode(' ', strtolower($string)), $financialKeywords)) {
                $categories['كلمات مالية'][] = $string;
            } else {
                $categories['أخرى'][] = $string;
            }
        }
        
        echo '<div class="row">';
        foreach ($categories as $category => $items) {
            if (!empty($items)) {
                echo '<div class="col-md-6 mb-3">';
                echo '<div class="card">';
                echo '<div class="card-header"><strong>' . $category . ' (' . count($items) . ')</strong></div>';
                echo '<div class="card-body">';
                
                $sample = array_slice($items, 0, 10);
                foreach ($sample as $item) {
                    echo '<span class="badge bg-secondary me-1 mb-1">' . htmlspecialchars($item) . '</span>';
                }
                
                if (count($items) > 10) {
                    echo '<br><small class="text-muted">... و ' . (count($items) - 10) . ' عنصر آخر</small>';
                }
                
                echo '</div></div></div>';
            }
        }
        echo '</div>';
        
        // اقتراحات للتقارير
        echo '<div class="file-info">';
        echo '<h6><i class="fas fa-lightbulb"></i> اقتراحات التقارير المناسبة:</h6>';
        
        $suggestions = [];
        
        if (!empty($categories['تواريخ'])) {
            $suggestions[] = 'تقارير زمنية (حسب التاريخ)';
        }
        if (!empty($categories['كلمات مالية'])) {
            $suggestions[] = 'تقارير مالية ومحاسبية';
        }
        if (!empty($categories['أرقام'])) {
            $suggestions[] = 'تقارير إحصائية';
        }
        
        if (empty($suggestions)) {
            $suggestions[] = 'تقرير عام للبيانات';
        }
        
        foreach ($suggestions as $suggestion) {
            echo '<span class="badge badge-custom me-2">' . $suggestion . '</span>';
        }
        
        echo '</div>';
        
        echo '</div></div>';
    }
    
    /**
     * تحليل عام للملف
     */
    function analyzeFileGeneral($filePath) {
        echo '<div class="card">';
        echo '<div class="card-header"><h5><i class="fas fa-info-circle"></i> تحليل عام للملف</h5></div>';
        echo '<div class="card-body">';
        
        $fileInfo = [
            'الاسم' => basename($filePath),
            'الحجم' => formatBytes(filesize($filePath)),
            'النوع' => 'Excel Worksheet (.xlsx)',
            'آخر تعديل' => date('Y-m-d H:i:s', filemtime($filePath)),
            'إمكانية القراءة' => is_readable($filePath) ? 'نعم' : 'لا',
            'الامتداد' => pathinfo($filePath, PATHINFO_EXTENSION)
        ];
        
        echo '<div class="table-responsive">';
        echo '<table class="table table-borderless">';
        foreach ($fileInfo as $key => $value) {
            echo '<tr>';
            echo '<td><strong>' . $key . ':</strong></td>';
            echo '<td>' . htmlspecialchars($value) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        
        // نصائح للاستخدام
        echo '<div class="file-info">';
        echo '<h6><i class="fas fa-tips"></i> نصائح:</h6>';
        echo '<ul>';
        echo '<li>هذا الملف يبدو أنه "ورقة عمل" محاسبية</li>';
        echo '<li>يمكن استخدامه لإنشاء تقارير مالية متقدمة</li>';
        echo '<li>يُنصح بتحويله إلى قاعدة بيانات لتحليل أفضل</li>';
        echo '<li>يمكن ربطه مع نظام AccessPos Pro لإنشاء تقارير تلقائية</li>';
        echo '</ul>';
        echo '</div>';
        
        echo '</div></div>';
    }
    
    /**
     * تحديد نوع الملف من اسمه
     */
    function getFileTypeFromName($fileName) {
        if (strpos($fileName, '.xml') !== false) return 'XML';
        if (strpos($fileName, '.rels') !== false) return 'Relationships';
        if (strpos($fileName, 'shared') !== false) return 'Shared Data';
        if (strpos($fileName, 'worksheet') !== false) return 'Worksheet';
        if (strpos($fileName, 'workbook') !== false) return 'Workbook';
        if (strpos($fileName, 'theme') !== false) return 'Theme';
        if (strpos($fileName, 'style') !== false) return 'Style';
        return 'Other';
    }
    
    /**
     * تنسيق حجم الملف
     */
    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    ?>
    
    <!-- روابط مفيدة -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-link"></i> روابط مفيدة</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <a href="../" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-home"></i> العودة للنظام الرئيسي
                    </a>
                </div>
                <div class="col-md-4">
                    <button onclick="window.location.reload()" class="btn btn-secondary w-100 mb-2">
                        <i class="fas fa-refresh"></i> إعادة تحميل الصفحة
                    </button>
                </div>
                <div class="col-md-4">
                    <button onclick="window.print()" class="btn btn-info w-100 mb-2">
                        <i class="fas fa-print"></i> طباعة التقرير
                    </button>
                </div>
            </div>
        </div>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
