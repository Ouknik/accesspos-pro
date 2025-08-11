<?php

use App\Http\Controllers\Admin\ArticleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\TableauDeBordController;
use App\Http\Controllers\Admin\TableController;
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
    Route::get('/details/chiffre-affaires', [App\Http\Controllers\Admin\ChiffreAffairesController::class, 'index'])
        ->name('admin.dashboard.chiffre-affaires');
    
    // Routes pour les rapports de chiffre d'affaires - تقارير رقم الأعمال
    Route::prefix('chiffre-affaires')->name('admin.chiffre-affaires.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ChiffreAffairesController::class, 'index'])
            ->name('index');
        Route::get('/serveur', [App\Http\Controllers\Admin\ChiffreAffairesController::class, 'rapportServeur'])
            ->name('serveur');
        Route::get('/famille', [App\Http\Controllers\Admin\ChiffreAffairesController::class, 'rapportFamille'])
            ->name('famille');
        Route::get('/article', [App\Http\Controllers\Admin\ChiffreAffairesController::class, 'rapportArticle'])
            ->name('article');
        Route::get('/paiement', [App\Http\Controllers\Admin\ChiffreAffairesController::class, 'rapportPaiement'])
            ->name('paiement');
        Route::get('/client', [App\Http\Controllers\Admin\ChiffreAffairesController::class, 'rapportClient'])
            ->name('client');
        Route::get('/caissier', [App\Http\Controllers\Admin\ChiffreAffairesController::class, 'rapportCaissier'])
            ->name('caissier');
        Route::get('/export', [App\Http\Controllers\Admin\ChiffreAffairesController::class, 'export'])
            ->name('export');
    });
    
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
        
        // Routes d'export - NEW
        Route::get('/export/excel', [ArticleController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [ArticleController::class, 'exportPDF'])->name('export.pdf');
        Route::get('/export/print', [ArticleController::class, 'printArticles'])->name('export.print');
        
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

    
    
    // Route pour export des données
    Route::get('/api/dashboard-export', [TableauDeBordController::class, 'exportModalData'])
        ->name('admin.dashboard.export');

    // Routes pour la gestion des tables/طاولات
    Route::prefix('tables')->name('admin.tables.')->group(function () {
        Route::get('/', [TableController::class, 'index'])->name('index');
        Route::get('/create', [TableController::class, 'create'])->name('create');
        Route::post('/', [TableController::class, 'store'])->name('store');
        Route::get('/{table}', [TableController::class, 'show'])->name('show');
        Route::get('/{table}/edit', [TableController::class, 'edit'])->name('edit');
        Route::put('/{table}', [TableController::class, 'update'])->name('update');
        Route::delete('/{table}', [TableController::class, 'destroy'])->name('destroy');
        
        // Routes AJAX pour إدارة الطاولات
        Route::post('/{table}/status', [TableController::class, 'changeStatus'])->name('change-status');
        Route::get('/api/data', [TableController::class, 'getTablesData'])->name('data');
    });

    // Routes pour la gestion des zones/مناطق
    Route::prefix('zones')->name('admin.zones.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ZoneController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\ZoneController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\ZoneController::class, 'store'])->name('store');
        Route::get('/{zone}', [App\Http\Controllers\Admin\ZoneController::class, 'show'])->name('show');
        Route::get('/{zone}/edit', [App\Http\Controllers\Admin\ZoneController::class, 'edit'])->name('edit');
        Route::put('/{zone}', [App\Http\Controllers\Admin\ZoneController::class, 'update'])->name('update');
        Route::delete('/{zone}', [App\Http\Controllers\Admin\ZoneController::class, 'destroy'])->name('destroy');
    });

    // Routes pour la gestion des tickets/التذاكر
    Route::prefix('tickets')->name('admin.tickets.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('index');
        Route::get('/details', [App\Http\Controllers\Admin\TicketController::class, 'details'])->name('details');
        Route::get('/print', [App\Http\Controllers\Admin\TicketController::class, 'print'])->name('print');
        Route::post('/update-status', [App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('update-status');
        Route::post('/delete', [App\Http\Controllers\Admin\TicketController::class, 'delete'])->name('delete');
    });

    // Routes pour la gestion des factures/الفواتير
    Route::prefix('factures')->name('admin.factures.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\FactureController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\FactureController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\FactureController::class, 'store'])->name('store');
        Route::get('/{facture}', [App\Http\Controllers\Admin\FactureController::class, 'show'])->name('show');
        Route::get('/{facture}/edit', [App\Http\Controllers\Admin\FactureController::class, 'edit'])->name('edit');
        Route::put('/{facture}', [App\Http\Controllers\Admin\FactureController::class, 'update'])->name('update');
        Route::delete('/{facture}', [App\Http\Controllers\Admin\FactureController::class, 'destroy'])->name('destroy');
        Route::get('/{facture}/print', [App\Http\Controllers\Admin\FactureController::class, 'print'])->name('print');
        Route::get('/convert/{cmdRef}', [App\Http\Controllers\Admin\FactureController::class, 'convertFromCommand'])->name('convert');
        Route::post('/export', [App\Http\Controllers\Admin\FactureController::class, 'export'])->name('export');
    });

    // API Routes pour les factures - البحث والبيانات الديناميكية
    Route::prefix('api/factures')->name('api.factures.')->group(function () {
        Route::get('search-clients', [App\Http\Controllers\Admin\FactureController::class, 'searchClients'])->name('search-clients');
        Route::post('create-client', [App\Http\Controllers\Admin\FactureController::class, 'createClient'])->name('create-client');
        Route::get('search-articles', [App\Http\Controllers\Admin\FactureController::class, 'searchArticles'])->name('search-articles');
        Route::get('client/{id}', [App\Http\Controllers\Admin\FactureController::class, 'getClientDetails'])->name('client-details');
        Route::get('article/{ref}', [App\Http\Controllers\Admin\FactureController::class, 'getArticleDetails'])->name('article-details');
        Route::post('validate-stock', [App\Http\Controllers\Admin\FactureController::class, 'validateStock'])->name('validate-stock');
        Route::get('statistics', [App\Http\Controllers\Admin\FactureController::class, 'getStatistics'])->name('statistics');
        Route::get('daily-summary', [App\Http\Controllers\Admin\FactureController::class, 'getDailySummary'])->name('daily-summary');
    });

    // Routes pour la gestion du stock - Stock Management
    Route::prefix('stock')->name('admin.stock.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\StockController::class, 'dashboard'])->name('dashboard');
        
        // Gestion de l'inventaire
        Route::get('/inventaire', [App\Http\Controllers\Admin\StockController::class, 'inventaire'])->name('inventaire');
        Route::post('/inventaire/update', [App\Http\Controllers\Admin\StockController::class, 'updateInventaire'])->name('inventaire.update');
        Route::get('/inventaire/export', [App\Http\Controllers\Admin\StockController::class, 'exportInventaire'])->name('inventaire.export');
        
        // Mouvements de stock
        Route::get('/mouvements', [App\Http\Controllers\Admin\StockController::class, 'mouvements'])->name('mouvements');
        Route::post('/mouvements/create', [App\Http\Controllers\Admin\StockController::class, 'createMouvement'])->name('mouvements.create');
        Route::get('/mouvements/history/{article}', [App\Http\Controllers\Admin\StockController::class, 'historiqueMouvements'])->name('mouvements.history');
        
        // Ajustements
        Route::get('/ajustements', [App\Http\Controllers\Admin\StockController::class, 'ajustements'])->name('ajustements');
        Route::post('/ajustements/create', [App\Http\Controllers\Admin\StockController::class, 'createAjustement'])->name('ajustements.create');
        Route::get('/ajustements/history', [App\Http\Controllers\Admin\StockController::class, 'historiqueAjustements'])->name('ajustements.history');
        
        // Gestion des achats
        Route::get('/achats', [App\Http\Controllers\Admin\StockController::class, 'achats'])->name('achats');
        Route::get('/achats/create', [App\Http\Controllers\Admin\StockController::class, 'createAchat'])->name('achats.create');
        Route::post('/achats/store', [App\Http\Controllers\Admin\StockController::class, 'storeAchat'])->name('achats.store');
        Route::get('/achats/{facture}', [App\Http\Controllers\Admin\StockController::class, 'showAchat'])->name('achats.show');
        Route::get('/achats/{facture}/details', [App\Http\Controllers\Admin\StockController::class, 'showAchatDetails'])->name('achats.details');
        Route::post('/achats/{facture}/validate', [App\Http\Controllers\Admin\StockController::class, 'validateAchat'])->name('achats.validate');
        Route::get('/achats/{facture}/print', [App\Http\Controllers\Admin\StockController::class, 'printAchat'])->name('achats.print');
        
        // Réception de marchandises
        Route::get('/reception', [App\Http\Controllers\Admin\StockController::class, 'reception'])->name('reception');
        Route::post('/reception/confirm', [App\Http\Controllers\Admin\StockController::class, 'confirmReception'])->name('reception.confirm');
        Route::get('/reception/bon/{bl}', [App\Http\Controllers\Admin\StockController::class, 'showBonLivraison'])->name('reception.bon');
        
        // Rapports et analyses
        Route::get('/rapports', [App\Http\Controllers\Admin\StockController::class, 'rapports'])->name('rapports');
        Route::get('/rapports/valorisation', [App\Http\Controllers\Admin\StockController::class, 'rapportValorisation'])->name('rapports.valorisation');
        Route::get('/rapports/mouvements-periode', [App\Http\Controllers\Admin\StockController::class, 'rapportMouvementsPeriode'])->name('rapports.mouvements-periode');
        Route::get('/rapports/rupture', [App\Http\Controllers\Admin\StockController::class, 'rapportRupture'])->name('rapports.rupture');
        Route::get('/rapports/rotation', [App\Http\Controllers\Admin\StockController::class, 'rapportRotation'])->name('rapports.rotation');
        
        // Alertes de stock
        Route::get('/alertes', [App\Http\Controllers\Admin\StockController::class, 'alertes'])->name('alertes');
        Route::post('/alertes/mark-read', [App\Http\Controllers\Admin\StockController::class, 'markAlerteRead'])->name('alertes.mark-read');
        Route::get('/alertes/notifications', [App\Http\Controllers\Admin\StockController::class, 'getNotificationsAlertes'])->name('alertes.notifications');
    });
    
    // API Routes pour le stock - AJAX calls
    Route::prefix('api/stock')->name('admin.stock.api.')->group(function () {
        Route::get('/articles/search', [App\Http\Controllers\Admin\StockController::class, 'searchArticles'])->name('articles.search');
        Route::get('/stats', [App\Http\Controllers\Admin\StockController::class, 'getStockStats'])->name('stats');
        Route::get('/mouvements/recent', [App\Http\Controllers\Admin\StockController::class, 'getRecentMouvementsApi'])->name('mouvements.recent');
        Route::get('/alertes/count', [App\Http\Controllers\Admin\StockController::class, 'getAlertesCount'])->name('alertes.count');
        Route::get('/fournisseurs/{fournisseur}/articles', [App\Http\Controllers\Admin\StockController::class, 'getFournisseurArticles'])->name('fournisseurs.articles');
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
