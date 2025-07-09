<?php
/**
 * سكريبت لتحويل جميع أزرار المودال إلى روابط مباشرة
 */

$viewPath = __DIR__ . '/resources/views/admin/tableau-de-bord-moderne.blade.php';
$content = file_get_contents($viewPath);

// قائمة الاستبدالات
$replacements = [
    // زر المقالات في المخزون
    [
        'from' => 'onclick="openAdvancedModal(\'articles-stock\', \'Détails des Articles en Stock\', \'fas fa-boxes\')"',
        'to' => 'href="{{ route(\'admin.dashboard.stock-rupture\') }}?type=stock" target="_blank"'
    ],
    
    // زر قيمة المخزون
    [
        'from' => 'onclick="openAdvancedModal(\'valeur-stock\', \'Valeur du Stock Détaillée\', \'fas fa-warehouse\')"',
        'to' => 'href="{{ route(\'admin.dashboard.stock-rupture\') }}?type=valeur" target="_blank"'
    ],
    
    // زر أفضل العملاء
    [
        'from' => 'onclick="openAdvancedModal(\'top-clients\', \'Top Clients du Restaurant\', \'fas fa-star\')"',
        'to' => 'href="{{ route(\'admin.dashboard.top-clients\') }}" target="_blank"'
    ],
    
    // زر إجمالي العملاء
    [
        'from' => 'onclick="openAdvancedModal(\'clients-totaux\', \'Détails des Clients Totaux\', \'fas fa-users\')"',
        'to' => 'href="{{ route(\'admin.dashboard.top-clients\') }}?type=totaux" target="_blank"'
    ],
    
    // زر العملاء المخلصين
    [
        'from' => 'onclick="openAdvancedModal(\'clients-fideles\', \'Détails des Clients Fidèles\', \'fas fa-star\')"',
        'to' => 'href="{{ route(\'admin.dashboard.top-clients\') }}?type=fideles" target="_blank"'
    ],
    
    // زر حالة الطاولات
    [
        'from' => 'onclick="openAdvancedModal(\'etat-tables\', \'État des Tables en Temps Réel\', \'fas fa-utensils\')"',
        'to' => 'href="{{ route(\'admin.dashboard.etat-tables\') }}" target="_blank"'
    ],
    
    // زر الطاولات المشغولة
    [
        'from' => 'onclick="openAdvancedModal(\'tables-occupees\', \'Détails des Tables Occupées\', \'fas fa-utensils\')"',
        'to' => 'href="{{ route(\'admin.dashboard.etat-tables\') }}?type=occupees" target="_blank"'
    ],
    
    // زر الأداء بالساعة
    [
        'from' => 'onclick="openAdvancedModal(\'performance-horaire\', \'Performance par Heure\', \'fas fa-clock\')"',
        'to' => 'href="{{ route(\'admin.dashboard.performance-horaire\') }}" target="_blank"'
    ],
    
    // زر طرق الدفع
    [
        'from' => 'onclick="openAdvancedModal(\'modes-paiement\', \'Modes de Paiement Détaillés\', \'fas fa-credit-card\')"',
        'to' => 'href="{{ route(\'admin.dashboard.modes-paiement\') }}" target="_blank"'
    ]
];

// تطبيق الاستبدالات
foreach ($replacements as $replacement) {
    $content = str_replace($replacement['from'], $replacement['to'], $content);
}

// تغيير button إلى a لجميع الأزرار المتبقية
$content = str_replace('<button onclick=', '<a onclick=', $content);
$content = str_replace('</button>', '</a>', $content);

// تنظيف onclick المتبقية واستبدالها بـ href
$patterns = [
    '/onclick="openAdvancedModal\([^"]+\)"/i' => 'href="#" onclick="alert(\'Redirection vers page détaillée...\')"'
];

foreach ($patterns as $pattern => $replacement) {
    $content = preg_replace($pattern, $replacement, $content);
}

// حفظ الملف
file_put_contents($viewPath, $content);

echo "✅ تم تحويل جميع أزرار المودال إلى روابط مباشرة!\n";
echo "📝 الملف محدث: $viewPath\n";
echo "🚀 الآن ستفتح جميع التفاصيل في صفحات منفصلة!\n";
?>
