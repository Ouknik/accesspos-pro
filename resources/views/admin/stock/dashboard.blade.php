@extends('layouts.sb-admin')

@section('title', 'Tableau de bord Stock')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-warehouse text-primary"></i> Tableau de bord Stock
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stock.inventaire') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-boxes"></i> Inventaire
            </a>
            <a href="{{ route('admin.stock.mouvements') }}" class="btn btn-success btn-sm">
                <i class="fas fa-exchange-alt"></i> Mouvements
            </a>
            <a href="{{ route('admin.stock.rapports') }}" class="btn btn-info btn-sm">
                <i class="fas fa-chart-bar"></i> Rapports
            </a>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row">
        <!-- Total Articles -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Articles en Stock
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_articles']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valeur Stock -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Valeur Totale du Stock
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['valeur_totale_stock'], 2) }} €
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles en Rupture -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Articles en Rupture
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['articles_rupture'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles en Alerte -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Articles en Alerte
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['articles_alerte'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Répartition du Stock par Famille -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition du Stock par Famille</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Famille</th>
                                    <th>Nb Articles</th>
                                    <th>Quantité Totale</th>
                                    <th>Valeur Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockParFamille as $famille)
                                <tr>
                                    <td>{{ $famille->famille ?: 'Non classée' }}</td>
                                    <td>{{ $famille->nombre_articles }}</td>
                                    <td>{{ number_format($famille->quantite_totale, 2) }}</td>
                                    <td>{{ number_format($famille->valeur_stock, 2) }} €</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes de Stock -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Alertes Stock</h6>
                    <a href="{{ route('admin.stock.alertes') }}" class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <div class="card-body">
                    @if($alertes->count() > 0)
                        @foreach($alertes->take(8) as $alerte)
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                @if($alerte->type_alerte == 'rupture')
                                    <i class="fas fa-times-circle text-danger"></i>
                                @else
                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                @endif
                            </div>
                            <div class="flex-fill">
                                <div class="font-weight-bold">{{ $alerte->ART_DESIGNATION }}</div>
                                <div class="small text-muted">{{ $alerte->message }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p>Aucune alerte stock</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Articles Vendus ce Mois -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Articles Vendus ce Mois</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Qty Vendue</th>
                                    <th>CA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topArticles as $article)
                                <tr>
                                    <td>{{ Str::limit($article->designation, 30) }}</td>
                                    <td>{{ number_format($article->quantite_vendue) }}</td>
                                    <td>{{ number_format($article->chiffre_affaires, 2) }} €</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Derniers Mouvements -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Derniers Mouvements</h6>
                    <a href="{{ route('admin.stock.mouvements') }}" class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <div class="card-body">
                    @foreach($recentMouvements as $mouvement)
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                            @if($mouvement->type_mouvement == 'ENTREE')
                                <i class="fas fa-arrow-up text-success"></i>
                            @else
                                <i class="fas fa-arrow-down text-danger"></i>
                            @endif
                        </div>
                        <div class="flex-fill">
                            <div class="font-weight-bold">{{ Str::limit($mouvement->article_designation, 25) }}</div>
                            <div class="small text-muted">
                                {{ $mouvement->libelle_mouvement }} - Qty: {{ abs($mouvement->quantite) }}
                            </div>
                        </div>
                        <div class="text-muted small">
                            {{ Carbon\Carbon::parse($mouvement->date_mouvement)->format('d/m H:i') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endsection
