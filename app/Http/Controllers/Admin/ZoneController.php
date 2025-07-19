<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ZoneController extends Controller
{
    /**
     * عرض قائمة المناطق
     */
    public function index()
    {
        try {
            // جلب جميع المناطق مع عدد الطاولات في كل منطقة
            $zones = DB::table('ZONE as z')
                ->leftJoin('TABLE as t', 'z.ZON_REF', '=', 't.ZON_REF')
                ->select(
                    'z.ZON_REF',
                    'z.ZON_LIB',
                    DB::raw('COUNT(t.TAB_REF) as total_tables'),
                    DB::raw('COUNT(CASE WHEN t.ETT_ETAT = "LIBRE" THEN 1 END) as tables_libres'),
                    DB::raw('COUNT(CASE WHEN t.ETT_ETAT = "OCCUPEE" THEN 1 END) as tables_occupees'),
                    DB::raw('COUNT(CASE WHEN t.ETT_ETAT = "RESERVEE" THEN 1 END) as tables_reservees'),
                    DB::raw('SUM(COALESCE(t.TAB_NBR_Couvert, 0)) as total_couverts')
                )
                ->groupBy('z.ZON_REF', 'z.ZON_LIB')
                ->orderBy('z.ZON_LIB')
                ->get();

            return view('admin.zones.index', compact('zones'));

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement des zones: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج إنشاء منطقة جديدة
     */
    public function create()
    {
        return view('admin.zones.create');
    }

    /**
     * حفظ منطقة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'zon_lib' => 'required|string|max:250|unique:ZONE,ZON_LIB',
        ], [
            'zon_lib.required' => 'اسم المنطقة مطلوب',
            'zon_lib.unique' => 'اسم المنطقة موجود مسبقاً',
            'zon_lib.max' => 'اسم المنطقة طويل جداً',
        ]);

        try {
            // إنشاء مرجع جديد للمنطقة
            $zonRef = 'ZON' . strtoupper(Str::random(8));

            // التأكد من عدم وجود مرجع مشابه
            while (DB::table('ZONE')->where('ZON_REF', $zonRef)->exists()) {
                $zonRef = 'ZON' . strtoupper(Str::random(8));
            }

            // إدخال المنطقة الجديدة
            DB::table('ZONE')->insert([
                'ZON_REF' => $zonRef,
                'ZON_LIB' => $request->input('zon_lib'),
            ]);

            return redirect()->route('admin.zones.index')
                ->with('success', 'تم إنشاء المنطقة بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'خطأ في إنشاء المنطقة: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل منطقة محددة
     */
    public function show($zonRef)
    {
        try {
            $zone = DB::table('ZONE')->where('ZON_REF', $zonRef)->first();

            if (!$zone) {
                return back()->with('error', 'المنطقة غير موجودة');
            }

            // جلب طاولات هذه المنطقة
            $tables = DB::table('TABLE')
                ->where('ZON_REF', $zonRef)
                ->select('TAB_REF', 'TAB_LIB', 'ETT_ETAT', 'TAB_NBR_Couvert', 'TAB_DESCRIPT')
                ->orderBy('TAB_LIB')
                ->get();

            return view('admin.zones.show', compact('zone', 'tables'));

        } catch (\Exception $e) {
            return back()->with('error', 'خطأ في عرض المنطقة: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج تعديل منطقة
     */
    public function edit($zonRef)
    {
        try {
            $zone = DB::table('ZONE')->where('ZON_REF', $zonRef)->first();

            if (!$zone) {
                return back()->with('error', 'المنطقة غير موجودة');
            }

            return view('admin.zones.edit', compact('zone'));

        } catch (\Exception $e) {
            return back()->with('error', 'خطأ في تحميل المنطقة: ' . $e->getMessage());
        }
    }

    /**
     * تحديث منطقة
     */
    public function update(Request $request, $zonRef)
    {
        $request->validate([
            'zon_lib' => 'required|string|max:250|unique:ZONE,ZON_LIB,' . $zonRef . ',ZON_REF',
        ]);

        try {
            $updated = DB::table('ZONE')
                ->where('ZON_REF', $zonRef)
                ->update([
                    'ZON_LIB' => $request->input('zon_lib'),
                ]);

            if ($updated) {
                return redirect()->route('admin.zones.index')
                    ->with('success', 'تم تحديث المنطقة بنجاح');
            } else {
                return back()->with('error', 'فشل في تحديث المنطقة');
            }

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'خطأ في تحديث المنطقة: ' . $e->getMessage());
        }
    }

    /**
     * حذف منطقة
     */
    public function destroy($zonRef)
    {
        try {
            // التحقق من وجود طاولات في هذه المنطقة
            $tablesCount = DB::table('TABLE')->where('ZON_REF', $zonRef)->count();
            
            if ($tablesCount > 0) {
                return response()->json([
                    'error' => "لا يمكن حذف المنطقة لأنها تحتوي على {$tablesCount} طاولة"
                ], 400);
            }

            // حذف المنطقة
            $deleted = DB::table('ZONE')->where('ZON_REF', $zonRef)->delete();

            if ($deleted) {
                return response()->json(['success' => 'تم حذف المنطقة بنجاح']);
            } else {
                return response()->json(['error' => 'فشل في حذف المنطقة'], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'خطأ في حذف المنطقة: ' . $e->getMessage()], 500);
        }
    }
}
