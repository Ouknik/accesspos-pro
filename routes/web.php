<?php

use App\Http\Controllers\Admin\ArticleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\TableauDeBordController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ExcelReportsController;
use App\Http\Controllers\Admin\ReportsManagerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Routes d'authentification
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Routes de réinitialisation de mot de passe
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

// Page d'accueil - Redirection vers le tableau de bord moderne SB Admin
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/admin/dashboard');
    }
    return redirect('/login');
});

// Tableau de Bord Moderne Professionnel - Protégé par l'authentification
Route::middleware('auth')->prefix('admin')->group(function () {
    // Route principale du tableau de bord - SB Admin
    Route::get('/dashboard', [TableauDeBordController::class, 'index'])
        ->name('admin.dashboard');
    
    // Route pour tableau-de-bord-moderne (compatibility)
    Route::get('/tableau-de-bord-moderne', [TableauDeBordController::class, 'index'])
        ->name('admin.tableau-de-bord-moderne');
    
    // Testing Routes - TASK 13
    
    
    Route::get('/console-errors-test', function() {
        return view('admin.console-errors-test-sb-admin');
    })->name('admin.console-errors-test');
    
    // Routes pour les pages de détails (versions SB Admin)
    Route::get('/details/chiffre-affaires', function() {
        return view('admin.chiffre-affaires-details-sb-admin');
    })->name('admin.dashboard.chiffre-affaires');
    
    Route::get('/details/stock-rupture', function() {
        return view('admin.stock-rupture-details-sb-admin');
    })->name('admin.dashboard.stock-rupture');
    
    Route::get('/details/top-clients', function() {
        return view('admin.top-clients-details-sb-admin');
    })->name('admin.dashboard.top-clients');
    
    Route::get('/details/performance-horaire', function() {
        return view('admin.performance-horaire-details-sb-admin');
    })->name('admin.dashboard.performance-horaire');
    
    Route::get('/details/modes-paiement', function() {
        return view('admin.modes-paiement-details-sb-admin');
    })->name('admin.dashboard.modes-paiement');
    
    Route::get('/details/etat-tables', function() {
        return view('admin.etat-tables-details-sb-admin');
    })->name('admin.dashboard.etat-tables');
    
    // API endpoint pour les données en temps réel
    Route::get('/api/live-data', [TableauDeBordController::class, 'getLiveData'])
        ->name('admin.live-data');
    
    // Routes للتقارير الرئيسية
    Route::prefix('reports')->name('admin.reports.')->group(function () {
        // واجهة إدارة التقارير الرئيسية
        Route::get('/manager', [ReportsManagerController::class, 'index'])
            ->name('manager');
        Route::get('/dashboard', [ReportsManagerController::class, 'dashboard'])
            ->name('dashboard');
    });

    // Routes pour le système de rapports القديم
    Route::prefix('rapports')->name('admin.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        
        Route::get('/complet', [ReportController::class, 'rapportComplet'])->name('complet');
        Route::get('/complet/pdf', [ReportController::class, 'rapportCompletPDF'])->name('complet.pdf');
        Route::get('/rapide', [ReportController::class, 'rapportRapide'])->name('rapide');
    });

    // Routes des rapports Excel - Papier de Travail
    Route::prefix('excel-reports')->name('admin.excel-reports.')->group(function () {
        // التقرير الشامل الجديد
        Route::get('/papier-de-travail', [ExcelReportsController::class, 'generatePapierDeTravail'])
            ->name('papier-de-travail');
        
        
        // المسارات القديمة للمتوافقية
        Route::get('/custom-form', [ExcelReportsController::class, 'showCustomReportForm'])
            ->name('custom-form');
        Route::post('/generate', [ExcelReportsController::class, 'generateCustomReport'])
            ->name('generate');
    });

    // Routes pour la gestion des articles/produits
    Route::prefix('articles')->name('admin.articles.')->group(function () {
        // Routes de base CRUD
        Route::get('/', [ArticleController::class, 'index'])->name('index');
        Route::get('/create', [ArticleController::class, 'create'])->name('create');
        Route::post('/', [ArticleController::class, 'store'])->name('store');
        Route::get('/{article}', [ArticleController::class, 'show'])->name('show');
        Route::get('/{article}/edit', [ArticleController::class, 'edit'])->name('edit');
        Route::put('/{article}', [ArticleController::class, 'update'])->name('update');
        
        // Routes administrateur uniquement (protection renforcée)
        Route::middleware('admin')->group(function () {
            Route::patch('/{article}/toggle-status', [ArticleController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{article}/add-stock', [ArticleController::class, 'addStock'])->name('add-stock');
        });
        
        // Routes d'analyse et rapports
        Route::get('/analytics/dashboard', [ArticleController::class, 'analyticsDashboard'])->name('analytics');
        Route::get('/export', [ArticleController::class, 'export'])->name('export');
        
        // API pour récupération données dynamiques
        Route::get('/api/families', [ArticleController::class, 'getFamilies'])->name('api.families');
        Route::get('/api/sub-families/{family}', [ArticleController::class, 'getSubFamilies'])->name('api.sub-families');
        Route::get('/api/stats', [ArticleController::class, 'getStats'])->name('api.stats');
    });

    // API routes pour les modals (si nécessaires dans le futur)
    Route::get('/api/chiffre-affaires-details', [TableauDeBordController::class, 'getChiffreAffairesDetails'])
        ->name('admin.chiffre-affaires-details');
    Route::get('/api/stock-rupture-details', [TableauDeBordController::class, 'getStockRuptureDetails'])
        ->name('admin.articles-rupture-details');
    Route::get('/api/top-clients-details', [TableauDeBordController::class, 'getTopClientsDetails'])
        ->name('admin.top-clients-details');
    Route::get('/api/performance-horaire-details', [TableauDeBordController::class, 'getPerformanceHoraireDetails'])
        ->name('admin.performance-horaire-details');
    Route::get('/api/modes-paiement-details', [TableauDeBordController::class, 'getModesPaiementDetails'])
        ->name('admin.modes-paiement-details');
    Route::get('/api/etat-tables-details', [TableauDeBordController::class, 'getEtatTablesDetails'])
        ->name('admin.etat-tables-details');
    
    // Route pour export des données
    Route::get('/api/dashboard-export', [TableauDeBordController::class, 'exportModalData'])
        ->name('admin.dashboard.export');
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
