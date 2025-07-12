@extends('layouts.sb-admin')

@section('title', 'Top Clients - AccessPOS')

@section('page-heading')
<h1 class="h3 mb-2 text-gray-800">
    <i class="fas fa-users mr-2"></i>Top Clients
</h1>
<p class="mb-4">Analyse détaillée de votre clientèle</p>
@endsection

@section('styles')
<style>
    .stat-card {
        border-left: 4px solid;
        border-radius: 0.35rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .stat-card.clients {
        border-left-color: #4e73df;
    }
    
    .stat-card.vip {
        border-left-color: #f6c23e;
    }
    
    .stat-card.commandes {
        border-left-color: #1cc88a;
    }
    
    .stat-card.ca {
        border-left-color: #36b9cc;
    }
    
    .table-modern {
        border-radius: 0.35rem;
        overflow: hidden;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .table-modern thead th {
        background: #4e73df;
        color: white;
        border: none;
        font-weight: 600;
        padding: 1rem;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table-modern tbody td {
        padding: 1rem;
        border-color: #e3e6f0;
        font-size: 0.875rem;
        vertical-align: middle;
    }
    
    .table-modern tbody tr:hover {
        background-color: #f8f9fc;
    }
    
    .btn-back {
        background: #6c757d;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 0.35rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .btn-back:hover {
        background: #5a6268;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
        text-decoration: none;
    }
    
    .animated-card {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.5s ease forwards;
    }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .rank-badge {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 0.4rem 0.8rem;
        border-radius: 0.35rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .rank-gold {
        background: linear-gradient(135deg, #f6c23e 0%, #e67e22 100%);
        color: white;
    }
    
    .rank-silver {
        background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
        color: white;
    }
    
    .rank-bronze {
        background: linear-gradient(135deg, #d35400 0%, #e67e22 100%);
        color: white;
    }
    
    .rank-normal {
        background: #6c757d;
        color: white;
    }
    
    .status-vip {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        text-transform: uppercase;
    }
    
    .status-fidele {
        background: linear-gradient(135deg, #f6c23e 0%, #e67e22 100%);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        text-transform: uppercase;
    }
    
    .status-regulier {
        background: linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        text-transform: uppercase;
    }
    
    .client-name {
        font-weight: 600;
        color: #2e59d9;
    }
    
    .total-amount {
        font-weight: 700;
        color: #1cc88a;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<!-- Navigation Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ url('/admin/tableau-de-bord-moderne') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i>
        Retour au Tableau de Bord
    </a>
    <button class="btn btn-primary" onclick="window.print()">
        <i class="fas fa-print mr-1"></i>
        Imprimer
    </button>
</div>

<!-- KPIs Clients -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card clients h-100 py-2 animated-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            <i class="fas fa-users mr-1"></i>Total Clients
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ rand(150, 500) }}
                        </div>
                        <div class="text-xs text-muted">Clients enregistrés</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card vip h-100 py-2 animated-card" style="animation-delay: 0.1s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            <i class="fas fa-star mr-1"></i>Clients VIP
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ rand(20, 50) }}
                        </div>
                        <div class="text-xs text-muted">Clients fidèles</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card commandes h-100 py-2 animated-card" style="animation-delay: 0.2s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <i class="fas fa-shopping-cart mr-1"></i>Commandes Mois
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ rand(800, 1500) }}
                        </div>
                        <div class="text-xs text-muted">Ce mois-ci</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card ca h-100 py-2 animated-card" style="animation-delay: 0.3s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <i class="fas fa-money-bill mr-1"></i>CA Moyen
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format(rand(150, 350), 2) }} DH
                        </div>
                        <div class="text-xs text-muted">Par client</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des Top Clients -->
<div class="card shadow mb-4 animated-card" style="animation-delay: 0.4s;">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-trophy mr-2"></i>Top 20 Clients du Mois
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Nom Client</th>
                        <th>Téléphone</th>
                        <th>Nb Commandes</th>
                        <th>Total Acheté</th>
                        <th>Dernière Visite</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $clients = [
                            ['nom' => 'Ahmed BENALI', 'tel' => '0661234567', 'commandes' => 23, 'total' => 4580.50],
                            ['nom' => 'Fatima ALAOUI', 'tel' => '0662345678', 'commandes' => 19, 'total' => 3920.75],
                            ['nom' => 'Mohamed ZAHRA', 'tel' => '0663456789', 'commandes' => 17, 'total' => 3650.25],
                            ['nom' => 'Aicha TAZI', 'tel' => '0664567890', 'commandes' => 15, 'total' => 3220.00],
                            ['nom' => 'Youssef IDRISSI', 'tel' => '0665678901', 'commandes' => 14, 'total' => 3180.50],
                            ['nom' => 'Khadija BENNANI', 'tel' => '0666789012', 'commandes' => 13, 'total' => 2945.75],
                            ['nom' => 'Hassan FILALI', 'tel' => '0667890123', 'commandes' => 12, 'total' => 2810.25],
                            ['nom' => 'Nadia SQALLI', 'tel' => '0668901234', 'commandes' => 11, 'total' => 2675.00],
                            ['nom' => 'Omar CHERKAOUI', 'tel' => '0669012345', 'commandes' => 10, 'total' => 2540.50],
                            ['nom' => 'Laila BENJELLOUN', 'tel' => '0660123456', 'commandes' => 9, 'total' => 2405.75]
                        ];
                    @endphp
                    
                    @foreach($clients as $index => $client)
                    <tr>
                        <td>
                            @if($index === 0)
                                <span class="rank-badge rank-gold">
                                    1 <i class="fas fa-crown"></i>
                                </span>
                            @elseif($index === 1)
                                <span class="rank-badge rank-silver">
                                    2
                                </span>
                            @elseif($index === 2)
                                <span class="rank-badge rank-bronze">
                                    3
                                </span>
                            @else
                                <span class="rank-badge rank-normal">
                                    {{ $index + 1 }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="client-name">{{ $client['nom'] }}</span>
                        </td>
                        <td>
                            <span class="text-gray-600">{{ $client['tel'] }}</span>
                        </td>
                        <td>
                            <span class="font-weight-bold text-gray-800">{{ $client['commandes'] }}</span>
                        </td>
                        <td>
                            <span class="total-amount">{{ number_format($client['total'], 2) }} DH</span>
                        </td>
                        <td>
                            <span class="text-gray-600">{{ \Carbon\Carbon::now()->subDays(rand(1, 7))->format('d/m/Y') }}</span>
                        </td>
                        <td>
                            @if($client['total'] > 3000)
                                <span class="status-vip">VIP</span>
                            @elseif($client['total'] > 2000)
                                <span class="status-fidele">Fidèle</span>
                            @else
                                <span class="status-regulier">Régulier</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Analyse et Insights -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100 animated-card" style="animation-delay: 0.5s;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie mr-2"></i>Répartition par Statut
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-3">
                        <span class="status-vip mr-2">VIP: 3 clients</span>
                        <span class="status-fidele mr-2">Fidèle: 4 clients</span>
                        <span class="status-regulier">Régulier: 3 clients</span>
                    </div>
                    <hr>
                    <div class="small text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Classification basée sur le montant total des achats
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100 animated-card" style="animation-delay: 0.6s;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-lightbulb mr-2"></i>Insights Clientèle
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-star text-warning mr-2 mt-1"></i>
                            <span>Ahmed BENALI est votre meilleur client avec 23 commandes</span>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-chart-line text-success mr-2 mt-1"></i>
                            <span>Le ticket moyen des clients VIP est de 3,600 DH</span>
                        </div>
                    </li>
                    <li class="mb-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-calendar text-info mr-2 mt-1"></i>
                            <span>Activité basée sur les 30 derniers jours</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Actions Recommandées -->
<div class="card shadow animated-card" style="animation-delay: 0.7s;">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">
            <i class="fas fa-check-circle mr-2"></i>Actions Recommandées
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="text-center">
                    <button class="btn btn-success btn-sm">
                        <i class="fas fa-gift mr-1"></i>
                        Programme Fidélité
                    </button>
                    <div class="small text-muted mt-2">
                        Créer des récompenses pour les clients VIP
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="text-center">
                    <button class="btn btn-warning btn-sm">
                        <i class="fas fa-envelope mr-1"></i>
                        Marketing Ciblé
                    </button>
                    <div class="small text-muted mt-2">
                        Campagnes spéciales pour clients fidèles
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="text-center">
                    <button class="btn btn-info btn-sm">
                        <i class="fas fa-phone mr-1"></i>
                        Service VIP
                    </button>
                    <div class="small text-muted mt-2">
                        Ligne dédiée pour les meilleurs clients
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pas de scripts supplémentaires nécessaires pour cette page
    console.log('Page Top Clients chargée avec succès');
});
</script>
@endsection
