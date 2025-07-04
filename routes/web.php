<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\TableauDeBordController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Auth\LoginController;

// Routes d'authentification
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Page d'accueil - Redirection vers le tableau de bord moderne
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/admin/tableau-de-bord-moderne');
    }
    return redirect('/login');
});

// Tableau de Bord Moderne Professionnel - Protégé par l'authentification
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/tableau-de-bord-moderne', [TableauDeBordController::class, 'index'])
        ->name('admin.tableau-de-bord-moderne');
    
    // API endpoint pour les données en temps réel
    Route::get('/api/live-data', [TableauDeBordController::class, 'getLiveData'])
        ->name('admin.live-data');
    
    // Routes pour le système de rapports
    // Route::prefix('rapports')->name('admin.reports.')->group(function () {
    //     Route::get('/', [ReportController::class, 'index'])->name('index');
    //     Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
    //     Route::get('/export/{type}/{format}', [ReportController::class, 'export'])->name('export');
    //     Route::get('/download/{filename}', [ReportController::class, 'download'])->name('download');
        
    //     // Routes pour le rapport complet
    //     Route::get('/complet', [ReportController::class, 'comprehensive'])->name('comprehensive');
    //     Route::get('/complet/pdf', [ReportController::class, 'comprehensivePdf'])->name('comprehensive.pdf');
    // });

    Route::prefix('rapports')->name('admin.reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        
    Route::get('/complet', [ReportController::class, 'rapportComplet'])->name('complet');
    Route::get('/complet/pdf', [ReportController::class, 'rapportCompletPDF'])->name('complet.pdf');
    Route::get('/rapide', [ReportController::class, 'rapportRapide'])->name('rapide');
});
});

// Redirection pour compatibilité avec les anciens liens
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return redirect('/admin/tableau-de-bord-moderne');
    });

    Route::get('/admin/tableau-de-bord', function () {
        return redirect('/admin/tableau-de-bord-moderne');
    });
});
