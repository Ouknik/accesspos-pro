@extends('layouts.sb-admin')

@section('title', 'Rapports de Stock')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar text-primary"></i> Rapports de Stock
        </h1>
        <a href="{{ route('admin.stock.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>

    <!-- Résumé valorisation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Valorisation du Stock</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Valeur Totale Stock
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($valorisationStock['total'] ?? 0, 2) }} €
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Articles Stockés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($valorisationStock['nombre_articles'] ?? 0) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Valeur Moyenne
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($valorisationStock['valeur_moyenne'] ?? 0, 2) }} €
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Familles Actives
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($valorisationStock['familles_actives'] ?? 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Articles en Rupture -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-danger">Articles en Rupture de Stock</h6>
                    <span class="badge badge-danger">{{ $articlesRupture->count() }}</span>
                </div>
                <div class="card-body">
                    @if($articlesRupture->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Article</th>
                                        <th>Famille</th>
                                        <th>Stock Min</th>
                                        <th>Prix Achat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($articlesRupture->take(10) as $article)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ Str::limit($article->ART_DESIGNATION, 25) }}</div>
                                            <small class="text-muted">{{ $article->ART_REF }}</small>
                                        </td>
                                        <td>{{ $article->famille ?: '-' }}</td>
                                        <td class="text-center">{{ $article->ART_STOCK_MIN ?: '0' }}</td>
                                        <td class="text-right">{{ number_format($article->ART_PRIX_ACHAT, 2) }} €</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($articlesRupture->count() > 10)
                            <div class="text-center">
                                <small class="text-muted">... et {{ $articlesRupture->count() - 10 }} autres articles</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-success">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p>Aucun article en rupture de stock</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Articles en Alerte -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">Articles en Alerte Stock</h6>
                    <span class="badge badge-warning">{{ $articlesAlerte->count() }}</span>
                </div>
                <div class="card-body">
                    @if($articlesAlerte->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Article</th>
                                        <th>Stock Actuel</th>
                                        <th>Stock Min</th>
                                        <th>À Commander</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($articlesAlerte->take(10) as $article)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ Str::limit($article->ART_DESIGNATION, 25) }}</div>
                                            <small class="text-muted">{{ $article->ART_REF }}</small>
                                        </td>
                                        <td class="text-center text-warning">{{ $article->STK_QTE }}</td>
                                        <td class="text-center">{{ $article->ART_STOCK_MIN }}</td>
                                        <td class="text-center text-primary">
                                            {{ max(0, ($article->ART_STOCK_MAX ?: $article->ART_STOCK_MIN * 2) - $article->STK_QTE) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($articlesAlerte->count() > 10)
                            <div class="text-center">
                                <small class="text-muted">... et {{ $articlesAlerte->count() - 10 }} autres articles</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-success">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p>Tous les stocks sont au niveau optimal</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Articles Vendus -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Top 10 - Articles les Plus Vendus</h6>
                </div>
                <div class="card-body">
                    @if($topVentes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rang</th>
                                        <th>Article</th>
                                        <th>Qty Vendue</th>
                                        <th>CA Généré</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topVentes as $index => $article)
                                    <tr>
                                        <td class="text-center">
                                            @if($index < 3)
                                                <span class="badge badge-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'info') }}">
                                                    {{ $index + 1 }}
                                                </span>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ Str::limit($article->designation, 30) }}</div>
                                        </td>
                                        <td class="text-center">{{ number_format($article->quantite_vendue) }}</td>
                                        <td class="text-right">{{ number_format($article->chiffre_affaires, 2) }} €</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <p>Aucune donnée de vente disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Articles à Rotation Lente -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Articles à Rotation Lente</h6>
                </div>
                <div class="card-body">
                    @if($rotationLente->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Article</th>
                                        <th>Stock Actuel</th>
                                        <th>Valeur Stock</th>
                                        <th>Dernière Vente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rotationLente->take(10) as $article)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ Str::limit($article->ART_DESIGNATION, 25) }}</div>
                                            <small class="text-muted">{{ $article->ART_REF }}</small>
                                        </td>
                                        <td class="text-center">{{ number_format($article->STK_QTE, 2) }}</td>
                                        <td class="text-right">{{ number_format($article->valeur_stock, 2) }} €</td>
                                        <td class="text-center">
                                            @if($article->derniere_vente)
                                                {{ Carbon\Carbon::parse($article->derniere_vente)->diffForHumans() }}
                                            @else
                                                <span class="text-muted">Jamais</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($rotationLente->count() > 10)
                            <div class="text-center">
                                <small class="text-muted">... et {{ $rotationLente->count() - 10 }} autres articles</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-success">
                            <i class="fas fa-sync-alt fa-2x mb-2"></i>
                            <p>Tous les articles ont une bonne rotation</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    
</div>

<style>
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.table th {
    border-top: none;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection
