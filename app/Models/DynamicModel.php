<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DynamicModel extends Model
{
    protected $connection = 'accesspos';
    public $timestamps = false;
    
    protected static $tableCache = [];
    
    public static function findActualTable($expectedTable, $searchTerms)
    {
        if (isset(static::$tableCache[$expectedTable])) {
            return static::$tableCache[$expectedTable];
        }
        
        try {
            // Get all table names
            $tables = DB::connection('accesspos')->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
            $allTables = collect($tables)->pluck('TABLE_NAME')->map('strtolower')->toArray();
            
            // Try exact match first
            if (in_array(strtolower($expectedTable), $allTables)) {
                $actualTable = collect($tables)->pluck('TABLE_NAME')->first(function($table) use ($expectedTable) {
                    return strtolower($table) === strtolower($expectedTable);
                });
                static::$tableCache[$expectedTable] = $actualTable;
                return $actualTable;
            }
            
            // Try search terms
            foreach ($searchTerms as $term) {
                foreach ($tables as $table) {
                    if (stripos($table->TABLE_NAME, $term) !== false) {
                        static::$tableCache[$expectedTable] = $table->TABLE_NAME;
                        return $table->TABLE_NAME;
                    }
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            return null;
        }
    }
}

class DynamicSale extends DynamicModel
{
    protected $primaryKey = 'id';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $actualTable = static::findActualTable('sales', [
            'vente', 'sale', 'commande', 'order', 'facture', 'cmd'
        ]);
        
        if ($actualTable) {
            $this->table = $actualTable;
        } else {
            $this->table = 'sales'; // fallback
        }
    }
}

class DynamicArticle extends DynamicModel
{
    protected $primaryKey = 'id';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $actualTable = static::findActualTable('articles', [
            'article', 'produit', 'item', 'product', 'art'
        ]);
        
        if ($actualTable) {
            $this->table = $actualTable;
        } else {
            $this->table = 'articles'; // fallback
        }
    }
}

class DynamicCustomer extends DynamicModel
{
    protected $primaryKey = 'id';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $actualTable = static::findActualTable('customers', [
            'client', 'customer', 'clientele', 'cust'
        ]);
        
        if ($actualTable) {
            $this->table = $actualTable;
        } else {
            $this->table = 'customers'; // fallback
        }
    }
}

class DynamicEmployee extends DynamicModel
{
    protected $primaryKey = 'id';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $actualTable = static::findActualTable('employees', [
            'employe', 'employee', 'personnel', 'staff', 'emp'
        ]);
        
        if ($actualTable) {
            $this->table = $actualTable;
        } else {
            $this->table = 'employees'; // fallback
        }
    }
}
