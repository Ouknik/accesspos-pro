<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // التحقق من صلاحيات المدير
        $user = Auth::user();
        
        // يمكنك تخصيص هذا الشرط حسب بنية جدول المستخدمين لديك
        // مثال: if (!$user->is_admin || $user->role !== 'admin')
        if (!$this->isAdmin($user)) {
            return redirect()->route('admin.tableau-de-bord-moderne')
                ->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }

        return $next($request);
    }

    /**
     * تحديد ما إذا كان المستخدم مديراً
     *
     * @param mixed $user
     * @return bool
     */
    private function isAdmin($user): bool
    {
        // يمكنك تخصيص هذا المنطق حسب بنية قاعدة البيانات لديك
        
        // إذا كان لديك عمود is_admin
        if (property_exists($user, 'is_admin') || isset($user->is_admin)) {
            return (bool) $user->is_admin;
        }
        
        // إذا كان لديك عمود role
        if (property_exists($user, 'role') || isset($user->role)) {
            return in_array(strtolower($user->role), ['admin', 'administrator', 'مدير']);
        }
        
        // إذا كان لديك عمود user_type
        if (property_exists($user, 'user_type') || isset($user->user_type)) {
            return in_array(strtolower($user->user_type), ['admin', 'administrator', 'مدير']);
        }
        
        // إذا كان لديك عمود niveau أو level
        if (property_exists($user, 'niveau') || isset($user->niveau)) {
            return (int) $user->niveau >= 3; // مستوى 3 أو أعلى = مدير
        }
        
        if (property_exists($user, 'level') || isset($user->level)) {
            return (int) $user->level >= 3;
        }
        
        // إذا كان المستخدم له صلاحيات خاصة (مثل معرف المدير)
        if (property_exists($user, 'id') || isset($user->id)) {
            // يمكنك تحديد معرفات المدراء هنا
            $adminIds = [1]; // معرف المدير الرئيسي
            return in_array((int) $user->id, $adminIds);
        }
        
        // افتراضياً، إذا لم نجد أي معايير، نرفض الوصول
        return false;
    }
}
