@extends('layouts.sb-admin')

@section('title', 'Inventaire actuel')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-boxes text-primary"></i> Inventaire actuel
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stock.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('admin.stock.inventaire.export') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Exporter
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de recherche</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.stock.inventaire') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Recherche</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Nom, code-barres, référence..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Famille</label>
                            <select name="famille" class="form-control">
                                <option value="">Toutes les familles</option>
                                @foreach($familles as $famille)
                                    <option value="{{ $famille->FAM_REF }}" 
                                            {{ request('famille') == $famille->FAM_REF ? 'selected' : '' }}>
                                        {{ $famille->FAM_LIB }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Statut Stock</label>
                            <select name="statut" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="rupture" {{ request('statut') == 'rupture' ? 'selected' : '' }}>
                                    En rupture
                                </option>
                                <option value="alerte" {{ request('statut') == 'alerte' ? 'selected' : '' }}>
                                    En alerte
                                </option>
                                <option value="surplus" {{ request('statut') == 'surplus' ? 'selected' : '' }}>
                                    En surplus
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                                <a href="{{ route('admin.stock.inventaire') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Inventaire - {{ $inventaire->total() }} articles trouvés
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Code-barres</th>
                            <th>Famille</th>
                            <th>Stock Actuel</th>
                            <th>Min/Max</th>
                            <th>Unité</th>
                            <th>Prix Achat</th>
                            <th>Prix Vente</th>
                            <th>Valeur Stock</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventaire as $item)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $item->ART_DESIGNATION }}</div>
                                <small class="text-muted">Réf: {{ $item->ART_REF }}</small>
                            </td>
                            <td>{{ $item->ART_CODEBARR ?: '-' }}</td>
                            <td>
                                <div>{{ $item->famille ?: '-' }}</div>
                                @if($item->sous_famille)
                                    <small class="text-muted">{{ $item->sous_famille }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="font-weight-bold 
                                    @if($item->quantite_stock == 0) text-danger
                                    @elseif($item->quantite_stock <= $item->stock_minimum) text-warning
                                    @else text-success
                                    @endif">
                                    {{ number_format($item->quantite_stock, 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <small>
                                    Min: {{ $item->stock_minimum ?: '0' }}<br>
                                    Max: {{ $item->stock_maximum ?: '-' }}
                                </small>
                            </td>
                            <td>{{ $item->unite_mesure ?: 'Pce' }}</td>
                            <td class="text-right">{{ number_format($item->prix_achat, 2) }} €</td>
                            <td class="text-right">{{ number_format($item->prix_vente, 2) }} €</td>
                            <td class="text-right">{{ number_format($item->valeur_stock, 2) }} €</td>
                            <td class="text-center">
                                @switch($item->statut_stock)
                                    @case('rupture')
                                        <span class="badge badge-danger">Rupture</span>
                                        @break
                                    @case('alerte')
                                        <span class="badge badge-warning">Alerte</span>
                                        @break
                                    @case('surplus')
                                        <span class="badge badge-info">Surplus</span>
                                        @break
                                    @default
                                        <span class="badge badge-success">Normal</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-primary btn-sm" 
                                            onclick="showMouvements('{{ $item->ART_REF }}')"
                                            title="Historique des mouvements">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm" 
                                            onclick="showAjustement('{{ $item->ART_REF }}', this)"
                                            data-art-ref="{{ $item->ART_REF }}"
                                            data-designation="{{ $item->ART_DESIGNATION }}"
                                            data-stock="{{ number_format($item->quantite_stock, 2) }}"
                                            title="Ajuster le stock">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                Aucun article trouvé avec les critères de recherche
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $inventaire->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Résumé des totaux -->
    <div class="row">
        <div class="col-md-4">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Valeur Totale (page actuelle)
                    </div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($inventaire->sum('valeur_stock'), 2) }} €
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Articles en Alerte
                    </div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ $inventaire->where('statut_stock', 'alerte')->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                        Articles en Rupture
                    </div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ $inventaire->where('statut_stock', 'rupture')->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                        <label>Nouveau Stock <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" id="ajust_nouveau_stock" class="form-control" 
                                   step="0.01" min="0" required>
                            <div class="input-group-append">
                                <span class="input-group-text">unités</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Entrez la quantité exacte après comptage physique
                        </small>
                        <div id="difference_preview" class="mt-2" style="display: none;">
                            <span class="badge badge-info">Différence: <span id="diff_value">0</span></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Motif</label>
                        <select id="ajust_motif" class="form-control" required>
                            <option value="">Sélectionner un motif</option>
                            <option value="Inventaire physique">Inventaire physique</option>
                            <option value="Correction d'erreur">Correction d'erreur</option>
                            <option value="Perte/Vol">Perte/Vol</option>
                            <option value="Avarie">Avarie</option>
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
        
        // Masquer le preview de différence
        document.getElementById('difference_preview').style.display = 'none';
        
        $('#ajustementModal').modal('show');
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
        if (refElement && refElement.textContent.includes(artRef)) {
            targetRow = row;
            break;
        }
    }
    
    if (targetRow) {
        const designation = targetRow.querySelector('td:first-child .font-weight-bold').textContent;
        const stockActuelElement = targetRow.querySelector('td:nth-child(4) .font-weight-bold');
        const stockActuel = stockActuelElement.textContent.trim().replace(/[^\d.,]/g, '');
        
        document.getElementById('ajust_art_ref').value = artRef;
        document.getElementById('ajust_article').value = designation.trim();
        document.getElementById('ajust_stock_actuel').value = stockActuel;
        document.getElementById('ajust_nouveau_stock').value = stockActuel;
        document.getElementById('ajust_motif').value = '';
        document.getElementById('ajust_commentaire').value = '';
        
        // Masquer le preview de différence
        document.getElementById('difference_preview').style.display = 'none';
        
        // Focus sur le champ nouveau stock
        $('#ajustementModal').modal('show');
        $('#ajustementModal').on('shown.bs.modal', function () {
            document.getElementById('ajust_nouveau_stock').focus();
            document.getElementById('ajust_nouveau_stock').select();
        });
    } else {
        alert('Impossible de récupérer les données de l\'article avec la référence: ' + artRef);
    }
}

// Calculer la différence en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const nouveauStockInput = document.getElementById('ajust_nouveau_stock');
    const stockActuelInput = document.getElementById('ajust_stock_actuel');
    const differencePreview = document.getElementById('difference_preview');
    const diffValue = document.getElementById('diff_value');
    
    if (nouveauStockInput) {
        nouveauStockInput.addEventListener('input', function() {
            const stockActuel = parseFloat(stockActuelInput.value) || 0;
            const nouveauStock = parseFloat(this.value) || 0;
            const difference = nouveauStock - stockActuel;
            
            if (this.value && !isNaN(difference) && difference !== 0) {
                diffValue.textContent = (difference > 0 ? '+' : '') + difference.toFixed(2);
                differencePreview.style.display = 'block';
                
                // Changer la couleur selon le type de mouvement
                const badge = diffValue.parentElement;
                badge.className = 'badge ' + (difference > 0 ? 'badge-success' : 'badge-warning');
            } else {
                differencePreview.style.display = 'none';
            }
        });
    }
});

function showMouvements(artRef) {
    window.location.href = "{{ url('admin/stock/mouvements/history') }}/" + artRef;
}

// Gestion du formulaire d'ajustement
document.getElementById('ajustementForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
    submitBtn.disabled = true;
    
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
            
            // Afficher le succès avec détails
            if (data.difference !== undefined) {
                const differenceText = data.difference > 0 ? 
                    `+${data.difference.toFixed(2)}` : 
                    data.difference.toFixed(2);
                
                alert(`Ajustement réussi!\n\nStock ancien: ${data.ancien_stock}\nStock nouveau: ${data.nouveau_stock}\nDifférence: ${differenceText}`);
            } else {
                alert('Ajustement réussi!');
            }
            
            location.reload();
        } else {
            alert('Erreur lors de l\'ajustement: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur de connexion lors de l\'ajustement');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>

<style>
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.table th {
    white-space: nowrap;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Amélioration du modal d'ajustement */
#ajustementModal .modal-body {
    padding: 1.5rem;
}

#ajustementModal .form-group label {
    font-weight: 600;
    color: #5a5c69;
}

#ajustementModal .input-group-text {
    background-color: #f8f9fc;
    border-color: #d1d3e2;
    color: #6e707e;
}

#difference_preview {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.badge-success {
    background-color: #1cc88a !important;
}

.badge-warning {
    background-color: #f6c23e !important;
}
</style>
@endsection
