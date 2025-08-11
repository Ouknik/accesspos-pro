@extends('layouts.sb-admin')

@section('title', 'Alertes de Stock')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-exclamation-triangle text-danger"></i> Alertes de Stock
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stock.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button class="btn btn-success btn-sm" onclick="markAllAsRead()">
                <i class="fas fa-check"></i> Marquer toutes comme lues
            </button>
        </div>
    </div>

    <!-- Résumé des alertes -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Articles en Rupture de Stock
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $alertes->where('type_alerte', 'rupture')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Articles en Stock Minimum
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $alertes->where('type_alerte', 'minimum')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des alertes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-bell"></i> Liste des Alertes - {{ $alertes->count() }} alertes actives
            </h6>
        </div>
        <div class="card-body">
            @if($alertes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Priorité</th>
                                <th>Article</th>
                                <th>Stock Actuel</th>
                                <th>Stock Minimum</th>
                                <th>Message</th>
                                <th>Recommandation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alertes as $alerte)
                            <tr class="{{ $alerte->type_alerte == 'rupture' ? 'table-danger' : 'table-warning' }}">
                                <td class="text-center">
                                    @if($alerte->type_alerte == 'rupture')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle"></i> CRITIQUE
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-exclamation-triangle"></i> ALERTE
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $alerte->ART_DESIGNATION }}</div>
                                    <small class="text-muted">Réf: {{ $alerte->ART_REF }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="h6 mb-0 font-weight-bold 
                                        {{ $alerte->STK_QTE == 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ number_format($alerte->STK_QTE, 2) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $alerte->ART_STOCK_MIN ?: '0' }}</td>
                                <td>{{ $alerte->message }}</td>
                                <td>
                                    @if($alerte->type_alerte == 'rupture')
                                        <span class="text-danger font-weight-bold">
                                            <i class="fas fa-shopping-cart"></i> Commande urgente requise
                                        </span>
                                    @else
                                        @php
                                            $aCommander = max(0, ($alerte->ART_STOCK_MAX ?: $alerte->ART_STOCK_MIN * 2) - $alerte->STK_QTE);
                                        @endphp
                                        <span class="text-primary">
                                            <i class="fas fa-plus"></i> Commander {{ $aCommander }} unités
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-primary btn-sm" 
                                                onclick="showAjustement('{{ $alerte->ART_REF }}', this)"
                                                data-art-ref="{{ $alerte->ART_REF }}"
                                                data-designation="{{ $alerte->ART_DESIGNATION }}"
                                                data-stock="{{ number_format($alerte->STK_QTE, 2) }}"
                                                title="Ajuster le stock">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-success btn-sm" 
                                                onclick="createCommande('{{ $alerte->ART_REF }}')"
                                                title="Créer une commande">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                        <button class="btn btn-info btn-sm" 
                                                onclick="showHistorique('{{ $alerte->ART_REF }}')"
                                                title="Voir l'historique">
                                            <i class="fas fa-history"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Actions en masse -->
                <div class="mt-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Actions en masse</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <button class="btn btn-outline-primary btn-block" onclick="exportAlertes()">
                                        <i class="fas fa-file-excel"></i> Exporter la liste
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-outline-success btn-block" onclick="generateCommandes()">
                                        <i class="fas fa-shopping-cart"></i> Générer commandes
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-outline-info btn-block" onclick="sendNotifications()">
                                        <i class="fas fa-envelope"></i> Envoyer notifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Aucune alerte -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    <h4 class="text-success">Excellent !</h4>
                    <p class="text-muted">Aucune alerte de stock actuellement. Tous vos stocks sont au niveau optimal.</p>
                    <div class="mt-4">
                        <a href="{{ route('admin.stock.inventaire') }}" class="btn btn-primary">
                            <i class="fas fa-boxes"></i> Voir l'inventaire
                        </a>
                        <a href="{{ route('admin.stock.rapports') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Voir les rapports
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistiques et recommandations -->
    @if($alertes->count() > 0)
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recommandations</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        @if($alertes->where('type_alerte', 'rupture')->count() > 0)
                        <li class="mb-2">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                            <strong>{{ $alertes->where('type_alerte', 'rupture')->count() }} articles en rupture</strong> nécessitent une commande urgente
                        </li>
                        @endif
                        
                        @if($alertes->where('type_alerte', 'minimum')->count() > 0)
                        <li class="mb-2">
                            <i class="fas fa-bell text-warning"></i>
                            <strong>{{ $alertes->where('type_alerte', 'minimum')->count() }} articles en alerte</strong> à surveiller de près
                        </li>
                        @endif
                        
                        <li class="mb-2">
                            <i class="fas fa-calendar text-info"></i>
                            Planifiez une révision des seuils de stock minimum/maximum
                        </li>
                        
                        <li class="mb-2">
                            <i class="fas fa-chart-line text-success"></i>
                            Consultez l'analyse de rotation pour optimiser les stocks
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Configuration des Alertes</h6>
                </div>
                <div class="card-body">
                    <form id="alerteConfigForm">
                        <div class="form-group">
                            <label>Notifications par email</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="email_notifications" checked>
                                <label class="custom-control-label" for="email_notifications">Activer les notifications email</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Fréquence des notifications</label>
                            <select class="form-control">
                                <option>Temps réel</option>
                                <option>Quotidienne</option>
                                <option>Hebdomadaire</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Email de notification</label>
                            <input type="email" class="form-control" placeholder="manager@entreprise.com">
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save"></i> Sauvegarder
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal Ajustement Stock -->
<div class="modal fade" id="ajustementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajustement de Stock</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="ajustementForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Article</label>
                        <input type="text" id="ajust_article" class="form-control" readonly>
                        <input type="hidden" id="ajust_art_ref">
                    </div>
                    <div class="form-group">
                        <label>Stock Actuel</label>
                        <input type="text" id="ajust_stock_actuel" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nouveau Stock</label>
                        <input type="number" id="ajust_nouveau_stock" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Motif</label>
                        <select id="ajust_motif" class="form-control" required>
                            <option value="">Sélectionner un motif</option>
                            <option value="Réapprovisionnement">Réapprovisionnement</option>
                            <option value="Correction d'inventaire">Correction d'inventaire</option>
                            <option value="Retour fournisseur">Retour fournisseur</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Commentaire</label>
                        <textarea id="ajust_commentaire" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Valider l'ajustement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showAjustement(artRef, element) {
    // إذا تم تمرير element، استخدم البيانات منه
    if (element && element.dataset) {
        const designation = element.dataset.designation;
        const stockActuel = element.dataset.stock;
        
        document.getElementById('ajust_art_ref').value = artRef;
        document.getElementById('ajust_article').value = designation;
        document.getElementById('ajust_stock_actuel').value = stockActuel;
        document.getElementById('ajust_nouveau_stock').value = stockActuel;
        document.getElementById('ajust_motif').value = '';
        document.getElementById('ajust_commentaire').value = '';
        
        $('#ajustementModal').modal('show');
        
        // Focus sur le nouveau stock après ouverture du modal
        $('#ajustementModal').on('shown.bs.modal', function () {
            document.getElementById('ajust_nouveau_stock').focus();
            document.getElementById('ajust_nouveau_stock').select();
        });
        return;
    }
    
    // الطريقة الاحتياطية: البحث في الجدول
    const rows = document.querySelectorAll('table tbody tr');
    let targetRow = null;
    
    for (let row of rows) {
        const refElement = row.querySelector('small.text-muted');
        if (refElement && refElement.textContent.includes('Réf: ' + artRef)) {
            targetRow = row;
            break;
        }
    }
    
    if (targetRow) {
        const designation = targetRow.querySelector('td:nth-child(2) .font-weight-bold').textContent;
        const stockActuelElement = targetRow.querySelector('td:nth-child(3) .h6');
        const stockActuel = stockActuelElement.textContent.trim().replace(/[^\d.,]/g, '');
        
        document.getElementById('ajust_art_ref').value = artRef;
        document.getElementById('ajust_article').value = designation.trim();
        document.getElementById('ajust_stock_actuel').value = stockActuel;
        document.getElementById('ajust_nouveau_stock').value = stockActuel;
        document.getElementById('ajust_motif').value = '';
        document.getElementById('ajust_commentaire').value = '';
        
        $('#ajustementModal').modal('show');
        
        // Focus sur le nouveau stock après ouverture du modal
        $('#ajustementModal').on('shown.bs.modal', function () {
            document.getElementById('ajust_nouveau_stock').focus();
            document.getElementById('ajust_nouveau_stock').select();
        });
    } else {
        alert('Impossible de récupérer les données de l\'article avec la référence: ' + artRef);
        console.log('Article recherché:', artRef);
        console.log('Lignes trouvées:', rows.length);
    }
}

function createCommande(artRef) {
    window.location.href = `{{ route('admin.stock.achats.create') }}?article=${artRef}`;
}

function showHistorique(artRef) {
    window.location.href = `{{ url('admin/stock/mouvements/history') }}/${artRef}`;
}

function markAllAsRead() {
    if (confirm('Marquer toutes les alertes comme lues ?')) {
        fetch('{{ route("admin.stock.alertes.mark-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({all: true})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function exportAlertes() {
    window.open('{{ route("admin.stock.rapports.rupture") }}?export=true', '_blank');
}

function generateCommandes() {
    alert('Fonctionnalité en développement - Génération automatique des commandes');
}

function sendNotifications() {
    alert('Fonctionnalité en développement - Envoi de notifications');
}

// Gestion du formulaire d'ajustement
document.getElementById('ajustementForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('art_ref', document.getElementById('ajust_art_ref').value);
    formData.append('nouveau_stock', document.getElementById('ajust_nouveau_stock').value);
    formData.append('motif', document.getElementById('ajust_motif').value);
    formData.append('commentaire', document.getElementById('ajust_commentaire').value);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("admin.stock.ajustements.create") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#ajustementModal').modal('hide');
            location.reload();
        } else {
            alert('Erreur lors de l\'ajustement: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'ajustement');
    });
});
</script>

<style>
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.table-danger {
    background-color: rgba(231, 74, 59, 0.1) !important;
}

.table-warning {
    background-color: rgba(246, 194, 62, 0.1) !important;
}

.custom-switch .custom-control-label::before {
    border-radius: 0.5rem;
}

.custom-switch .custom-control-label::after {
    border-radius: 0.5rem;
}
</style>
@endsection
