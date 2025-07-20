<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TableController extends Controller
{
    /**
     * عرض قائمة الطاولات
     */
    public function index()
    {
        try {
            // جلب جميع الطاولات مع المناطق
            $tables = DB::table('TABLE as t')
                ->leftJoin('ZONE as z', 't.ZON_REF', '=', 'z.ZON_REF')
                ->select(
                    't.TAB_REF',
                    't.ZON_REF',
                    't.ETT_ETAT',
                    't.TAB_LIB',
                    't.TAB_DESCRIPT',
                    't.TAB_NBR_Couvert',
                    'z.ZON_LIB'
                )
                ->orderBy('z.ZON_LIB')
                ->orderBy('t.TAB_LIB')
                ->get();

            // جلب المناطق للفلترة
            $zones = DB::table('ZONE')
                ->select('ZON_REF', 'ZON_LIB')
                ->orderBy('ZON_LIB')
                ->get();

             

            // حساب الإحصائيات
            $statistiques = [
                'total' => $tables->count(),
                'libres' => $tables->where('ETT_ETAT', 'LIBRE')->count(),
                'occupees' => $tables->where('ETT_ETAT', 'OCCUPEE')->count(),
                'reservees' => $tables->where('ETT_ETAT', 'RESERVEE')->count(),
                'hors_service' => $tables->where('ETT_ETAT', 'HORS_SERVICE')->count(),
                'total_couverts' => $tables->sum('TAB_NBR_Couvert'),
            ];

            return view('admin.tables.index', compact('tables', 'zones', 'statistiques'));

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement des tables: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج إنشاء طاولة جديدة
     */
    public function create()
    {
        try {
            // جلب المناطق
            $zones = DB::table('ZONE')
                ->select('ZON_REF', 'ZON_LIB')
                ->orderBy('ZON_LIB')
                ->get();

            return view('admin.tables.create', compact('zones'));

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement des zones: ' . $e->getMessage());
        }
    }

    /**
     * حفظ طاولة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'tab_lib' => 'required|string|max:250',
            'zon_ref' => 'required|string|max:32',
            'tab_nbr_couvert' => 'required|integer|min:1|max:50',
            'tab_descript' => 'nullable|string|max:250',
        ], [
            'tab_lib.required' => 'اسم الطاولة مطلوب',
            'zon_ref.required' => 'المنطقة مطلوبة',
            'tab_nbr_couvert.required' => 'عدد المقاعد مطلوب',
            'tab_nbr_couvert.min' => 'عدد المقاعد يجب أن يكون على الأقل 1',
            'tab_nbr_couvert.max' => 'عدد المقاعد لا يمكن أن يتجاوز 50',
        ]);

        try {
            // إنشاء مرجع جديد للطاولة
            $tabRef = 'TAB' . strtoupper(Str::random(8));

            // التأكد من عدم وجود مرجع مشابه
            while (DB::table('TABLE')->where('TAB_REF', $tabRef)->exists()) {
                $tabRef = 'TAB' . strtoupper(Str::random(8));
            }

            // إدخال الطاولة الجديدة
            DB::table('TABLE')->insert([
                'TAB_REF' => $tabRef,
                'ZON_REF' => $request->input('zon_ref'),
                'ETT_ETAT' => 'LIBRE', // حالة افتراضية
                'TAB_LIB' => $request->input('tab_lib'),
                'TAB_DESCRIPT' => $request->input('tab_descript', ''),
                'TAB_NBR_Couvert' => $request->input('tab_nbr_couvert'),
            ]);

            return redirect()->route('admin.tables.index')
                ->with('success', 'تم إنشاء الطاولة بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'خطأ في إنشاء الطاولة: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل طاولة محددة
     */
    public function show($tabRef)
    {
        try {
            $table = DB::table('TABLE as t')
                ->leftJoin('ZONE as z', 't.ZON_REF', '=', 'z.ZON_REF')
                ->select(
                    't.TAB_REF',
                    't.ZON_REF',
                    't.ETT_ETAT',
                    't.TAB_LIB',
                    't.TAB_DESCRIPT',
                    't.TAB_NBR_Couvert',
                    'z.ZON_LIB'
                )
                ->where('t.TAB_REF', $tabRef)
                ->first();

            if (!$table) {
                return back()->with('error', 'الطاولة غير موجودة');
            }

            // جلب الحجوزات الحالية أو آخر النشاطات
            $activities = $this->getTableActivities($tabRef);

            return view('admin.tables.show', compact('table', 'activities'));

        } catch (\Exception $e) {
            return back()->with('error', 'خطأ في عرض الطاولة: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج تعديل طاولة
     */
    public function edit($tabRef)
    {
        try {
            $table = DB::table('TABLE')
                ->where('TAB_REF', $tabRef)
                ->first();

            if (!$table) {
                return back()->with('error', 'الطاولة غير موجودة');
            }

            $zones = DB::table('ZONE')
                ->select('ZON_REF', 'ZON_LIB')
                ->orderBy('ZON_LIB')
                ->get();

            return view('admin.tables.edit', compact('table', 'zones'));

        } catch (\Exception $e) {
            return back()->with('error', 'خطأ في تحميل الطاولة: ' . $e->getMessage());
        }
    }

    /**
     * تحديث طاولة
     */
    public function update(Request $request, $tabRef)
    {
        $request->validate([
            'tab_lib' => 'required|string|max:250',
            'zon_ref' => 'required|string|max:32',
            'tab_nbr_couvert' => 'required|integer|min:1|max:50',
            'tab_descript' => 'nullable|string|max:250',
        ]);

        try {
            $updated = DB::table('TABLE')
                ->where('TAB_REF', $tabRef)
                ->update([
                    'ZON_REF' => $request->input('zon_ref'),
                    'TAB_LIB' => $request->input('tab_lib'),
                    'TAB_DESCRIPT' => $request->input('tab_descript', ''),
                    'TAB_NBR_Couvert' => $request->input('tab_nbr_couvert'),
                ]);

            if ($updated) {
                return redirect()->route('admin.tables.index')
                    ->with('success', 'تم تحديث الطاولة بنجاح');
            } else {
                return back()->with('error', 'فشل في تحديث الطاولة');
            }

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'خطأ في تحديث الطاولة: ' . $e->getMessage());
        }
    }

    /**
     * حذف طاولة
     */
    public function destroy($tabRef)
    {
        try {
            // التحقق من وجود الطاولة
            $table = DB::table('TABLE')->where('TAB_REF', $tabRef)->first();
            
            if (!$table) {
                return response()->json(['error' => 'الطاولة غير موجودة'], 404);
            }

            // التحقق من حالة الطاولة
            if ($table->ETT_ETAT === 'OCCUPEE') {
                return response()->json(['error' => 'لا يمكن حذف طاولة مشغولة'], 400);
            }

            // حذف الطاولة
            $deleted = DB::table('TABLE')->where('TAB_REF', $tabRef)->delete();

            if ($deleted) {
                return response()->json(['success' => 'تم حذف الطاولة بنجاح']);
            } else {
                return response()->json(['error' => 'فشل في حذف الطاولة'], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'خطأ في حذف الطاولة: ' . $e->getMessage()], 500);
        }
    }

    /**
     * تغيير حالة الطاولة
     */
    public function changeStatus(Request $request, $tabRef)
    {
        $request->validate([
            'status' => 'required|in:LIBRE,OCCUPEE,RESERVEE,HORS_SERVICE'
        ]);

        try {
            $updated = DB::table('TABLE')
                ->where('TAB_REF', $tabRef)
                ->update(['ETT_ETAT' => $request->input('status')]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تغيير حالة الطاولة بنجاح',
                    'new_status' => $request->input('status')
                ]);
            } else {
                return response()->json(['error' => 'فشل في تغيير حالة الطاولة'], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'خطأ في تغيير الحالة: ' . $e->getMessage()], 500);
        }
    }

    /**
     * الحصول على بيانات الطاولات في الوقت الفعلي (AJAX)
     */
    public function getTablesData()
    {
        try {
            $tables = DB::table('TABLE as t')
                ->leftJoin('ZONE as z', 't.ZON_REF', '=', 'z.ZON_REF')
                ->select(
                    't.TAB_REF',
                    't.ZON_REF',
                    't.ETT_ETAT',
                    't.TAB_LIB',
                    't.TAB_DESCRIPT',
                    't.TAB_NBR_Couvert',
                    'z.ZON_LIB'
                )
                ->orderBy('z.ZON_LIB')
                ->orderBy('t.TAB_LIB')
                ->get();

            $statistiques = [
                'total' => $tables->count(),
                'libres' => $tables->where('ETT_ETAT', 'LIBRE')->count(),
                'occupees' => $tables->where('ETT_ETAT', 'OCCUPEE')->count(),
                'reservees' => $tables->where('ETT_ETAT', 'RESERVEE')->count(),
                'hors_service' => $tables->where('ETT_ETAT', 'HORS_SERVICE')->count(),
                'total_couverts' => $tables->sum('TAB_NBR_Couvert'),
            ];

            return response()->json([
                'success' => true,
                'tables' => $tables,
                'statistiques' => $statistiques,
                'last_update' => now()->format('H:i:s')
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'خطأ في جلب البيانات: ' . $e->getMessage()], 500);
        }
    }

    /**
     * الحصول على أنشطة الطاولة
     */
    private function getTableActivities($tabRef)
    {
        // يمكن ربطها بجداول الحجوزات أو الفواتير لاحقاً
        // هنا مثال أساسي
        return [
            ['time' => '14:30', 'action' => 'تم احتلال الطاولة', 'user' => 'admin'],
            ['time' => '14:25', 'action' => 'تم تنظيف الطاولة', 'user' => 'staff1'],
        ];
    }
}
