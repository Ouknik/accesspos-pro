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

// =================================================================
// ROUTES POUR LES MODALS AVANCÉES - ANALYSES DÉTAILLÉES
// =================================================================

Route::middleware('auth')->prefix('admin')->group(function () {
    // Modal 1: Chiffre d'Affaires du Jour
    Route::get('/api/chiffre-affaires-details', [TableauDeBordController::class, 'getChiffreAffairesDetails'])
        ->name('admin.chiffre-affaires-details');
    
    // Modal 2: Articles en Rupture
    Route::get('/api/articles-rupture-details', [TableauDeBordController::class, 'getArticlesRuptureDetails'])
        ->name('admin.articles-rupture-details');
    
    // Modal 3: Top Clients Détaillé
    Route::get('/api/top-clients-details', [TableauDeBordController::class, 'getTopClientsDetails'])
        ->name('admin.top-clients-details');
    
    // Modal 4: Performance par Heure
    Route::get('/api/performance-horaire-details', [TableauDeBordController::class, 'getPerformanceHoraireDetails'])
        ->name('admin.performance-horaire-details');
    
    // Modal 5: Modes de Paiement Détaillés
    Route::get('/api/modes-paiement-details', [TableauDeBordController::class, 'getModesPaiementDetails'])
        ->name('admin.modes-paiement-details');
    
    // Modal 6: État des Tables Restaurant
    Route::get('/api/etat-tables-details', [TableauDeBordController::class, 'getEtatTablesDetails'])
        ->name('admin.etat-tables-details');
    
    // API pour export des données de modals
    Route::post('/api/export-modal-data', [TableauDeBordController::class, 'exportModalData'])
        ->name('admin.export-modal-data');
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

// Routes pour les analyses avancées et modals
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::prefix('dashboard')->name('admin.dashboard.')->group(function () {
        Route::get('/chiffre-affaires-details', [\App\Http\Controllers\Admin\AdvancedAnalyticsController::class, 'getChiffreAffairesDetails'])
            ->name('chiffre-affaires-details');
        Route::get('/articles-rupture-details', [\App\Http\Controllers\Admin\AdvancedAnalyticsController::class, 'getArticlesRuptureDetails'])
            ->name('articles-rupture-details');
        Route::get('/top-clients-details', [\App\Http\Controllers\Admin\AdvancedAnalyticsController::class, 'getTopClientsDetails'])
            ->name('top-clients-details');
        Route::get('/performance-horaire-details', [\App\Http\Controllers\Admin\AdvancedAnalyticsController::class, 'getPerformanceHoraireDetails'])
            ->name('performance-horaire-details');
        Route::get('/modes-paiement-details', [\App\Http\Controllers\Admin\AdvancedAnalyticsController::class, 'getModesPaiementDetails'])
            ->name('modes-paiement-details');
        Route::get('/etat-tables-details', [\App\Http\Controllers\Admin\AdvancedAnalyticsController::class, 'getEtatTablesDetails'])
            ->name('etat-tables-details');
        
        // Route d'export générique pour tous les types de données
        Route::get('/export', [ReportController::class, 'exportModalData'])
            ->name('export');
    });
    
    // Routes supplémentaires pour les fonctionnalités avancées
    Route::prefix('analytics')->name('admin.analytics.')->group(function () {
        Route::get('/previsions', [\App\Http\Controllers\Admin\AdvancedAnalyticsController::class, 'getPrevisions'])
            ->name('previsions');
        Route::get('/alertes', [\App\Http\Controllers\Admin\AdvancedAnalyticsController::class, 'getAlertes'])
            ->name('alertes');
        Route::post('/notifications/marquer-lu', [\App\Http\Controllers\Admin\AdvancedAnalyticsController::class, 'marquerNotificationLue'])
            ->name('notifications.marquer-lu');
    });
    
    // Routes pour le système de notifications en temps réel
    Route::prefix('notifications')->name('admin.notifications.')->group(function () {
        Route::get('/live', [\App\Http\Controllers\Admin\NotificationController::class, 'getNotificationsEnTempsReel'])
            ->name('live');
        Route::post('/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'marquerCommeLue'])
            ->name('mark-read');
        Route::get('/resume', [\App\Http\Controllers\Admin\NotificationController::class, 'getResume'])
            ->name('resume');
        Route::get('/config', [\App\Http\Controllers\Admin\NotificationController::class, 'getConfigurationAlertes'])
            ->name('config');
        Route::post('/config', [\App\Http\Controllers\Admin\NotificationController::class, 'updateConfigurationAlertes'])
            ->name('config.update');
        Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])
            ->name('index');
    });
});
