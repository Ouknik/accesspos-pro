<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StockController;

/*
|--------------------------------------------------------------------------
| Stock Management Routes
|--------------------------------------------------------------------------
|
| Routes pour la gestion complète du stock basée sur la base de données RestoWinxo
|
*/

Route::group(['prefix' => 'admin/stock', 'as' => 'admin.stock.', 'middleware' => ['auth']], function () {
    
    // Tableau de bord du stock
    Route::get('/', [StockController::class, 'dashboard'])->name('dashboard');
    
    // Gestion de l'inventaire
    Route::get('/inventaire', [StockController::class, 'inventaire'])->name('inventaire');
    Route::post('/inventaire/update', [StockController::class, 'updateInventaire'])->name('inventaire.update');
    Route::get('/inventaire/export', [StockController::class, 'exportInventaire'])->name('inventaire.export');
    
    // Mouvements de stock
    Route::get('/mouvements', [StockController::class, 'mouvements'])->name('mouvements');
    Route::post('/mouvements/create', [StockController::class, 'createMouvement'])->name('mouvements.create');
    Route::get('/mouvements/history/{article}', [StockController::class, 'historiqueMouvements'])->name('mouvements.history');
    
    // Ajustements
    Route::get('/ajustements', [StockController::class, 'ajustements'])->name('ajustements');
    Route::post('/ajustements/create', [StockController::class, 'createAjustement'])->name('ajustements.create');
    Route::get('/ajustements/history', [StockController::class, 'historiqueAjustements'])->name('ajustements.history');
    
    // Gestion des achats
    Route::get('/achats', [StockController::class, 'achats'])->name('achats');
    Route::get('/achats/create', [StockController::class, 'createAchat'])->name('achats.create');
    Route::post('/achats/store', [StockController::class, 'storeAchat'])->name('achats.store');
    Route::get('/achats/{facture}', [StockController::class, 'showAchat'])->name('achats.show');
    
    // Réception de marchandises
    Route::get('/reception', [StockController::class, 'reception'])->name('reception');
    Route::post('/reception/confirm', [StockController::class, 'confirmReception'])->name('reception.confirm');
    Route::get('/reception/bon/{bl}', [StockController::class, 'showBonLivraison'])->name('reception.bon');
    
    // Rapports et analyses
    Route::get('/rapports', [StockController::class, 'rapports'])->name('rapports');
    Route::get('/rapports/valorisation', [StockController::class, 'rapportValorisation'])->name('rapports.valorisation');
    Route::get('/rapports/mouvements-periode', [StockController::class, 'rapportMouvementsPeriode'])->name('rapports.mouvements-periode');
    Route::get('/rapports/rupture', [StockController::class, 'rapportRupture'])->name('rapports.rupture');
    Route::get('/rapports/rotation', [StockController::class, 'rapportRotation'])->name('rapports.rotation');
    
    // Alertes de stock
    Route::get('/alertes', [StockController::class, 'alertes'])->name('alertes');
    Route::post('/alertes/mark-read', [StockController::class, 'markAlerteRead'])->name('alertes.mark-read');
    Route::get('/alertes/notifications', [StockController::class, 'getNotificationsAlertes'])->name('alertes.notifications');
    
    // API Routes pour les calls AJAX
    Route::group(['prefix' => 'api'], function () {
        Route::get('/articles/search', [StockController::class, 'searchArticles'])->name('api.articles.search');
        Route::get('/stock/stats', [StockController::class, 'getStockStats'])->name('api.stock.stats');
        Route::get('/mouvements/recent', [StockController::class, 'getRecentMouvements'])->name('api.mouvements.recent');
        Route::get('/alertes/count', [StockController::class, 'getAlertesCount'])->name('api.alertes.count');
        Route::get('/fournisseurs/{fournisseur}/articles', [StockController::class, 'getFournisseurArticles'])->name('api.fournisseurs.articles');
    });
});
