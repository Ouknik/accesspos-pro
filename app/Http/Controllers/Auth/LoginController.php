<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login-sb-admin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ], [
            'login.required' => 'Le nom d\'utilisateur est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.'
        ]);

        // Find user by login - more flexible search
        $user = User::where('USR_LOGIN', $credentials['login'])
                   ->orWhere('USR_LOGIN', 'LIKE', '%' . $credentials['login'] . '%')
                   ->whereNotNull('USR_LOGIN')
                   ->where('USR_LOGIN', '!=', '')
                   ->first();

        if (!$user) {
            // Try case-insensitive search
            $user = User::whereRaw('LOWER(USR_LOGIN) = ?', [strtolower($credentials['login'])])
                       ->whereNotNull('USR_LOGIN')
                       ->where('USR_LOGIN', '!=', '')
                       ->first();
        }

        if (!$user) {
            return back()->withErrors([
                'login' => 'Aucun utilisateur trouvé avec ce nom d\'utilisateur.',
            ])->onlyInput('login');
        }

        // Check password (assuming it's stored as plain text or simple hash)
        // You might need to adjust this based on how passwords are stored in your system
        if ($this->checkPassword($credentials['password'], $user->USR_PASS)) {
            // Manually log the user in
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();
            
            return redirect()->intended('/admin/dashboard')
                ->with('success', 'Connexion réussie ! Bienvenue ' . $user->name . '.');
        }

        return back()->withErrors([
            'password' => 'Le mot de passe est incorrect.',
        ])->onlyInput('login');
    }

    /**
     * Check if the provided password matches the stored password
     */
    private function checkPassword($inputPassword, $storedPassword)
    {
        // If the stored password is empty, check against empty input
        if (empty($storedPassword)) {
            return empty($inputPassword);
        }

        // First try direct comparison (for plain text passwords)
        if ($inputPassword === $storedPassword) {
            return true;
        }

        // Try with common simple hashes if they might be used
        if (md5($inputPassword) === $storedPassword) {
            return true;
        }

        if (sha1($inputPassword) === $storedPassword) {
            return true;
        }

        // Try Laravel's Hash::check in case some passwords are hashed
        try {
            if (Hash::check($inputPassword, $storedPassword)) {
                return true;
            }
        } catch (\Exception $e) {
            // Ignore hash check errors
        }

        return false;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
