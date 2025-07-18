<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsManagerController extends Controller
{
    /**
     * عرض صفحة إدارة التقارير الرئيسية
     */
    public function index()
    {
        return view('admin.reports.manager');
    }

    /**
     * عرض تفاصيل التقارير مع الإحصائيات
     */
    public function dashboard()
    {
        // يمكن إضافة إحصائيات عن التقارير هنا
        $reportStats = [
            'total_reports_generated' => 150,
            'most_used_report' => 'Papier de Travail',
            'last_generated' => now()->subHours(2)->format('d/m/Y H:i'),
        ];

        return view('admin.reports.dashboard', compact('reportStats'));
    }
}
