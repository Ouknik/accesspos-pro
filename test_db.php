<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Test database connection
    $articlesCount = DB::table('ARTICLE')->count();
    echo "عدد المقالات في قاعدة البيانات: " . $articlesCount . "\n";
    
    // Test if there are any articles
    if ($articlesCount > 0) {
        $article = DB::table('ARTICLE')->first();
        echo "أول مقال: " . json_encode($article, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
    
    // Test family count
    $famillesCount = DB::table('FAMILLE')->count();
    echo "عدد العائلات: " . $famillesCount . "\n";
    
    // Test stock count
    $stockCount = DB::table('STOCK')->count();
    echo "عدد حركات المخزون: " . $stockCount . "\n";
    
} catch (Exception $e) {
    echo "خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage() . "\n";
}
