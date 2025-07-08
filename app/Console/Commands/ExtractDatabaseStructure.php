<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExtractDatabaseStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:extract-structure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'استخراج بنية قاعدة البيانات (الجداول والأعمدة) مع إحصائيات تفصيلية وحفظها في ملف db.text';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('بدء استخراج بنية قاعدة البيانات...');
        
        try {
            // الحصول على جميع الجداول
            $tables = $this->getAllTables();
            
            $output = "";
            $output .= "=== بنية قاعدة البيانات: " . config('database.connections.sqlsrv.database') . " ===\n";
            $output .= "تاريخ الاستخراج: " . now()->format('Y-m-d H:i:s') . "\n";
            $output .= "عدد الجداول: " . count($tables) . "\n";
            $output .= "نوع قاعدة البيانات: SQL Server\n";
            $output .= "إجمالي الأعمدة: سيتم حسابه...\n";
            $output .= str_repeat("=", 80) . "\n\n";

            $this->info("تم العثور على " . count($tables) . " جدول");
            
            $totalColumns = 0;
            $tablesWithData = [];

            foreach ($tables as $table) {
                $tableName = $table->TABLE_NAME;
                $this->info("معالجة الجدول: {$tableName}");
                
                // الحصول على أعمدة الجدول
                $columns = $this->getTableColumns($tableName);
                $totalColumns += count($columns);
                
                $output .= "الجدول: {$tableName}\n";
                $output .= str_repeat("-", 40) . "\n";
                $output .= "عدد الأعمدة: " . count($columns) . "\n";
                
                foreach ($columns as $column) {
                    $columnInfo = sprintf(
                        "  - %s (%s",
                        $column->COLUMN_NAME,
                        $column->DATA_TYPE
                    );
                    
                    // إضافة طول الحقل إذا كان متاحاً
                    if ($column->CHARACTER_MAXIMUM_LENGTH) {
                        $columnInfo .= "(" . $column->CHARACTER_MAXIMUM_LENGTH . ")";
                    } elseif ($column->NUMERIC_PRECISION) {
                        $columnInfo .= "(" . $column->NUMERIC_PRECISION;
                        if ($column->NUMERIC_SCALE) {
                            $columnInfo .= "," . $column->NUMERIC_SCALE;
                        }
                        $columnInfo .= ")";
                    }
                    
                    $columnInfo .= ") " . ($column->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL');
                    
                    if ($column->COLUMN_DEFAULT) {
                        $columnInfo .= " DEFAULT: {$column->COLUMN_DEFAULT}";
                    }
                    
                    $output .= $columnInfo . "\n";
                }
                
                $output .= "\n";
                
                // حفظ إحصائيات الجدول
                $tablesWithData[] = [
                    'name' => $tableName,
                    'columns' => count($columns)
                ];
            }
            
            // إضافة ملخص إحصائي في النهاية
            $output .= str_repeat("=", 80) . "\n";
            $output .= "ملخص إحصائي:\n";
            $output .= str_repeat("=", 80) . "\n";
            $output .= "إجمالي عدد الجداول: " . count($tables) . "\n";
            $output .= "إجمالي عدد الأعمدة: " . $totalColumns . "\n";
            $output .= "متوسط الأعمدة لكل جدول: " . round($totalColumns / count($tables), 2) . "\n\n";
            
            // أكبر الجداول من ناحية عدد الأعمدة
            usort($tablesWithData, function($a, $b) {
                return $b['columns'] - $a['columns'];
            });
            
            $output .= "أكبر 10 جداول من ناحية عدد الأعمدة:\n";
            $output .= str_repeat("-", 40) . "\n";
            
            for ($i = 0; $i < min(10, count($tablesWithData)); $i++) {
                $output .= sprintf("  %d. %s (%d عمود)\n", 
                    $i + 1, 
                    $tablesWithData[$i]['name'], 
                    $tablesWithData[$i]['columns']
                );
            }
            
            $output .= "\n" . str_repeat("=", 80) . "\n";
            $output .= "تم إنتاج هذا التقرير بواسطة نظام AccessPOS Pro\n";
            $output .= "Laravel Artisan Command: db:extract-structure\n";
            $output .= str_repeat("=", 80) . "\n";

            // حفظ النتائج في ملف db.text
            $filePath = public_path('db.text');
            File::put($filePath, $output);
            
            $this->info("تم حفظ بنية قاعدة البيانات في: {$filePath}");
            $this->info("حجم الملف: " . File::size($filePath) . " بايت");
            $this->info("إجمالي الجداول: " . count($tables));
            $this->info("إجمالي الأعمدة: " . $totalColumns);
            $this->info("تم الانتهاء بنجاح!");
            
        } catch (\Exception $e) {
            $this->error("حدث خطأ: " . $e->getMessage());
            $this->error("تفاصيل الخطأ: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }

    /**
     * الحصول على جميع الجداول في قاعدة البيانات
     */
    private function getAllTables()
    {
        return DB::select("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_TYPE = 'BASE TABLE' 
            AND TABLE_CATALOG = ?
            ORDER BY TABLE_NAME
        ", [config('database.connections.sqlsrv.database')]);
    }

    /**
     * الحصول على أعمدة جدول معين
     */
    private function getTableColumns($tableName)
    {
        return DB::select("
            SELECT 
                COLUMN_NAME,
                DATA_TYPE,
                IS_NULLABLE,
                COLUMN_DEFAULT,
                CHARACTER_MAXIMUM_LENGTH,
                NUMERIC_PRECISION,
                NUMERIC_SCALE
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = ? 
            AND TABLE_CATALOG = ?
            ORDER BY ORDINAL_POSITION
        ", [$tableName, config('database.connections.sqlsrv.database')]);
    }
}
