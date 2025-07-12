<!-- =============================================================== -->
<!-- WIDGET DE NOTIFICATIONS SB ADMIN 2 - ACCESSPOS PRO -->
<!-- Système d'alertes et notifications intelligentes -->
<!-- =============================================================== -->

<!-- Widget de notifications intégré dans le topbar -->
<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        <!-- Compteur de notifications -->
        <span class="badge badge-danger badge-counter" id="notificationCount" style="display: none;">0</span>
    </a>
    
    <!-- Dropdown - Alerts -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
        <!-- Header -->
        <h6 class="dropdown-header bg-primary text-white">
            <i class="fas fa-bell mr-2"></i>Centre d'Alertes
            <button class="btn btn-sm btn-link text-white float-right p-0" onclick="refreshNotifications()" title="Actualiser">
                <i class="fas fa-sync-alt"></i>
            </button>
        </h6>
        
        <!-- Filtres -->
        <div class="px-3 py-2 bg-light border-bottom">
            <div class="btn-group btn-group-sm w-100" role="group">
                <button type="button" class="btn btn-outline-primary active notification-filter" data-filter="all">
                    Toutes <span class="badge badge-light" id="countAll">0</span>
                </button>
                <button type="button" class="btn btn-outline-danger notification-filter" data-filter="critique">
                    Critiques <span class="badge badge-light" id="countCritique">0</span>
                </button>
                <button type="button" class="btn btn-outline-warning notification-filter" data-filter="urgent">
                    Urgentes <span class="badge badge-light" id="countUrgent">0</span>
                </button>
            </div>
        </div>
        
        <!-- Contenu des notifications -->
        <div class="notification-scroll" id="notificationContent" style="max-height: 300px; overflow-y: auto;">
            <!-- Loading state -->
            <div class="text-center py-3" id="loadingNotifications">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <small class="text-muted ml-2">Chargement...</small>
            </div>
            
            <!-- Les notifications seront générées ici -->
        </div>
        
        <!-- Footer -->
        <div class="dropdown-footer text-center bg-light">
            <button class="btn btn-link btn-sm" onclick="markAllAsRead()">
                <i class="fas fa-check-double mr-1"></i>Tout marquer comme lu
            </button>
            <button class="btn btn-link btn-sm" onclick="openNotificationSettings()">
                <i class="fas fa-cog mr-1"></i>Paramètres
            </button>
        </div>
    </div>
</li>

<!-- Modal de paramètres des notifications -->
<div class="modal fade" id="notificationSettingsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-cog mr-2"></i>Paramètres des Notifications
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <!-- Alertes de Stock -->
                    <div class="col-md-6">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <h6 class="text-warning">
                                    <i class="fas fa-boxes mr-2"></i>Alertes de Stock
                                </h6>
                                
                                <div class="form-group">
                                    <label for="seuilStockFaible">Seuil stock faible:</label>
                                    <input type="number" class="form-control form-control-sm" id="seuilStockFaible" value="5">
                                </div>
                                
                                <div class="form-group">
                                    <label for="seuilStockCritique">Seuil stock critique:</label>
                                    <input type="number" class="form-control form-control-sm" id="seuilStockCritique" value="2">
                                </div>
                                
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="alerteStockActive" checked>
                                    <label class="custom-control-label" for="alerteStockActive">Activer les alertes stock</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alertes de Ventes -->
                    <div class="col-md-6">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <h6 class="text-success">
                                    <i class="fas fa-chart-line mr-2"></i>Alertes de Ventes
                                </h6>
                                
                                <div class="form-group">
                                    <label for="objectifVentesJour">Objectif ventes/jour (DH):</label>
                                    <input type="number" class="form-control form-control-sm" id="objectifVentesJour" value="5000">
                                </div>
                                
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="alerteVentesActive" checked>
                                    <label class="custom-control-label" for="alerteVentesActive">Alertes objectifs ventes</label>
                                </div>
                                
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="alerteVentesFaibles" checked>
                                    <label class="custom-control-label" for="alerteVentesFaibles">Alertes ventes faibles</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <!-- Fréquence des notifications -->
                    <div class="col-md-6">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <h6 class="text-info">
                                    <i class="fas fa-clock mr-2"></i>Fréquence
                                </h6>
                                
                                <div class="form-group">
                                    <label for="frequenceVerification">Vérification automatique:</label>
                                    <select class="form-control form-control-sm" id="frequenceVerification">
                                        <option value="30">30 secondes</option>
                                        <option value="60" selected>1 minute</option>
                                        <option value="300">5 minutes</option>
                                        <option value="600">10 minutes</option>
                                    </select>
                                </div>
                                
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="notificationsAudio" checked>
                                    <label class="custom-control-label" for="notificationsAudio">Son des notifications</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Types de notifications -->
                    <div class="col-md-6">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <h6 class="text-primary">
                                    <i class="fas fa-list mr-2"></i>Types de Notifications
                                </h6>
                                
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="notifStock" checked>
                                    <label class="custom-control-label" for="notifStock">Stock & Ruptures</label>
                                </div>
                                
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="notifVentes" checked>
                                    <label class="custom-control-label" for="notifVentes">Ventes & CA</label>
                                </div>
                                
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="notifCommandes" checked>
                                    <label class="custom-control-label" for="notifCommandes">Nouvelles commandes</label>
                                </div>
                                
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="notifSysteme" checked>
                                    <label class="custom-control-label" for="notifSysteme">Système & Erreurs</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveNotificationSettings()">
                    <i class="fas fa-save mr-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Styles CSS pour les notifications -->
<style>
.notification-scroll::-webkit-scrollbar {
    width: 6px;
}

.notification-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-scroll::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.notification-scroll::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.notification-item {
    border-bottom: 1px solid #e3e6f0;
    transition: all 0.3s ease;
}

.notification-item:hover {
    background-color: #f8f9fc;
}

.notification-item.unread {
    background-color: #eef2ff;
    border-left: 3px solid #4e73df;
}

.notification-item.critique {
    border-left: 3px solid #e74a3b;
}

.notification-item.urgent {
    border-left: 3px solid #f6c23e;
}

.notification-item.important {
    border-left: 3px solid #36b9cc;
}

.notification-time {
    font-size: 0.75rem;
    color: #6c757d;
}

.notification-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
}

.notification-filter.active {
    background-color: #4e73df;
    border-color: #4e73df;
    color: white;
}

.badge-counter {
    position: absolute;
    top: -2px;
    right: -6px;
    min-width: 18px;
    height: 18px;
    border-radius: 10px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dropdown-list {
    min-width: 350px;
}

.dropdown-footer {
    border-top: 1px solid #e3e6f0;
    padding: 0.5rem;
}

@keyframes notificationPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.notification-pulse {
    animation: notificationPulse 2s infinite;
}
</style>

<!-- Script JavaScript pour les notifications -->
<script>
// Variables globales
let notificationSettings = {
    seuilStockFaible: 5,
    seuilStockCritique: 2,
    objectifVentesJour: 5000,
    frequenceVerification: 60,
    alerteStockActive: true,
    alerteVentesActive: true,
    notificationsAudio: true
};

let notifications = [];
let notificationInterval = null;
let currentFilter = 'all';

// Initialisation
$(document).ready(function() {
    loadNotificationSettings();
    startNotificationService();
    generateSampleNotifications();
});

// Service de notifications
function startNotificationService() {
    // Vérification initiale
    checkForNotifications();
    
    // Démarrage de l'intervalle
    const freq = notificationSettings.frequenceVerification * 1000;
    notificationInterval = setInterval(checkForNotifications, freq);
}

function stopNotificationService() {
    if (notificationInterval) {
        clearInterval(notificationInterval);
    }
}

// Vérification des notifications
function checkForNotifications() {
    // Simulation de vérification des données
    checkStockAlerts();
    checkVentesAlerts();
    checkCommandesAlerts();
    
    updateNotificationDisplay();
}

function checkStockAlerts() {
    // Simulation d'alertes de stock
    const stockFaible = Math.floor(Math.random() * 10);
    const stockCritique = Math.floor(Math.random() * 5);
    
    if (stockFaible > 0 && notificationSettings.alerteStockActive) {
        addNotification({
            id: 'stock_' + Date.now(),
            type: 'urgent',
            title: 'Stock Faible',
            message: `${stockFaible} articles en stock faible`,
            icon: 'fas fa-exclamation-triangle',
            iconColor: 'warning',
            time: new Date(),
            unread: true,
            action: () => openAdvancedModal('stock', 'Alertes Stock')
        });
    }
    
    if (stockCritique > 0 && notificationSettings.alerteStockActive) {
        addNotification({
            id: 'stock_critique_' + Date.now(),
            type: 'critique',
            title: 'Stock Critique',
            message: `${stockCritique} articles en rupture`,
            icon: 'fas fa-exclamation-circle',
            iconColor: 'danger',
            time: new Date(),
            unread: true,
            action: () => openAdvancedModal('stock', 'Stock Critique')
        });
    }
}

function checkVentesAlerts() {
    if (!notificationSettings.alerteVentesActive) return;
    
    // Simulation d'alerte de ventes
    const ventesJour = Math.floor(Math.random() * 8000);
    const pourcentageObjectif = (ventesJour / notificationSettings.objectifVentesJour) * 100;
    
    if (pourcentageObjectif >= 100) {
        addNotification({
            id: 'ventes_objectif_' + Date.now(),
            type: 'important',
            title: 'Objectif Atteint!',
            message: `Objectif de ventes dépassé: ${ventesJour} DH`,
            icon: 'fas fa-trophy',
            iconColor: 'success',
            time: new Date(),
            unread: true
        });
    }
}

function checkCommandesAlerts() {
    // Simulation de nouvelles commandes
    if (Math.random() > 0.7) {
        addNotification({
            id: 'commande_' + Date.now(),
            type: 'important',
            title: 'Nouvelle Commande',
            message: 'Commande #' + Math.floor(Math.random() * 1000) + ' reçue',
            icon: 'fas fa-shopping-cart',
            iconColor: 'info',
            time: new Date(),
            unread: true
        });
    }
}

function addNotification(notification) {
    // Éviter les doublons
    if (notifications.find(n => n.id === notification.id)) {
        return;
    }
    
    notifications.unshift(notification);
    
    // Limiter le nombre de notifications
    if (notifications.length > 50) {
        notifications = notifications.slice(0, 50);
    }
    
    // Son de notification
    if (notificationSettings.notificationsAudio && notification.unread) {
        playNotificationSound();
    }
}

function updateNotificationDisplay() {
    const content = document.getElementById('notificationContent');
    const counter = document.getElementById('notificationCount');
    
    // Comptage des notifications
    const counts = {
        all: notifications.length,
        critique: notifications.filter(n => n.type === 'critique').length,
        urgent: notifications.filter(n => n.type === 'urgent').length,
        important: notifications.filter(n => n.type === 'important').length,
        unread: notifications.filter(n => n.unread).length
    };
    
    // Mise à jour des compteurs
    Object.keys(counts).forEach(key => {
        const element = document.getElementById('count' + key.charAt(0).toUpperCase() + key.slice(1));
        if (element) element.textContent = counts[key];
    });
    
    // Badge de notification
    if (counts.unread > 0) {
        counter.textContent = counts.unread;
        counter.style.display = 'flex';
    } else {
        counter.style.display = 'none';
    }
    
    // Génération du HTML des notifications
    let html = '';
    const filteredNotifications = filterNotifications();
    
    if (filteredNotifications.length === 0) {
        html = `
            <div class="dropdown-item text-center text-muted py-3">
                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                Aucune notification
            </div>
        `;
    } else {
        filteredNotifications.forEach(notification => {
            const timeAgo = getTimeAgo(notification.time);
            html += `
                <a class="dropdown-item notification-item ${notification.unread ? 'unread' : ''} ${notification.type}" 
                   href="#" onclick="handleNotificationClick('${notification.id}')">
                    <div class="d-flex align-items-center">
                        <div class="notification-icon bg-${notification.iconColor} text-white">
                            <i class="${notification.icon}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold">${notification.title}</div>
                            <div class="text-gray-600 small">${notification.message}</div>
                            <div class="notification-time">${timeAgo}</div>
                        </div>
                        ${notification.unread ? '<div class="text-primary"><i class="fas fa-circle" style="font-size: 0.5rem;"></i></div>' : ''}
                    </div>
                </a>
            `;
        });
    }
    
    content.innerHTML = html;
    document.getElementById('loadingNotifications').style.display = 'none';
}

function filterNotifications() {
    if (currentFilter === 'all') {
        return notifications;
    }
    return notifications.filter(n => n.type === currentFilter);
}

function handleNotificationClick(notificationId) {
    const notification = notifications.find(n => n.id === notificationId);
    if (notification) {
        // Marquer comme lu
        notification.unread = false;
        
        // Exécuter l'action si elle existe
        if (notification.action) {
            notification.action();
        }
        
        updateNotificationDisplay();
    }
}

// Fonctions d'actions
function refreshNotifications() {
    document.getElementById('loadingNotifications').style.display = 'block';
    setTimeout(() => {
        checkForNotifications();
    }, 1000);
}

function markAllAsRead() {
    notifications.forEach(n => n.unread = false);
    updateNotificationDisplay();
}

function openNotificationSettings() {
    $('#notificationSettingsModal').modal('show');
    loadSettingsToModal();
}

function saveNotificationSettings() {
    // Récupération des valeurs du modal
    notificationSettings = {
        seuilStockFaible: parseInt(document.getElementById('seuilStockFaible').value),
        seuilStockCritique: parseInt(document.getElementById('seuilStockCritique').value),
        objectifVentesJour: parseInt(document.getElementById('objectifVentesJour').value),
        frequenceVerification: parseInt(document.getElementById('frequenceVerification').value),
        alerteStockActive: document.getElementById('alerteStockActive').checked,
        alerteVentesActive: document.getElementById('alerteVentesActive').checked,
        notificationsAudio: document.getElementById('notificationsAudio').checked
    };
    
    // Sauvegarde locale
    localStorage.setItem('notificationSettings', JSON.stringify(notificationSettings));
    
    // Redémarrage du service avec nouveaux paramètres
    stopNotificationService();
    startNotificationService();
    
    $('#notificationSettingsModal').modal('hide');
    
    // Notification de confirmation
    addNotification({
        id: 'settings_saved_' + Date.now(),
        type: 'important',
        title: 'Paramètres Sauvegardés',
        message: 'Les paramètres de notification ont été mis à jour',
        icon: 'fas fa-check',
        iconColor: 'success',
        time: new Date(),
        unread: true
    });
}

function loadNotificationSettings() {
    const saved = localStorage.getItem('notificationSettings');
    if (saved) {
        notificationSettings = { ...notificationSettings, ...JSON.parse(saved) };
    }
}

function loadSettingsToModal() {
    document.getElementById('seuilStockFaible').value = notificationSettings.seuilStockFaible;
    document.getElementById('seuilStockCritique').value = notificationSettings.seuilStockCritique;
    document.getElementById('objectifVentesJour').value = notificationSettings.objectifVentesJour;
    document.getElementById('frequenceVerification').value = notificationSettings.frequenceVerification;
    document.getElementById('alerteStockActive').checked = notificationSettings.alerteStockActive;
    document.getElementById('alerteVentesActive').checked = notificationSettings.alerteVentesActive;
    document.getElementById('notificationsAudio').checked = notificationSettings.notificationsAudio;
}

// Gestion des filtres
$(document).on('click', '.notification-filter', function() {
    $('.notification-filter').removeClass('active');
    $(this).addClass('active');
    currentFilter = $(this).data('filter');
    updateNotificationDisplay();
});

// Fonctions utilitaires
function getTimeAgo(date) {
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return 'À l\'instant';
    if (diff < 3600) return Math.floor(diff / 60) + ' min';
    if (diff < 86400) return Math.floor(diff / 3600) + ' h';
    return Math.floor(diff / 86400) + ' j';
}

function playNotificationSound() {
    // Son de notification (optionnel)
    if ('speechSynthesis' in window && notificationSettings.notificationsAudio) {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmUhBR1+0/LOfCsGJH3L8N2TSA0PU6fl8KZjHA4zltr8xYYkBxZxwO/gm1AQP1W+5OCmbRsKRKXh8bBiIAcdgNn8xYUlBhF8zPPbijEAVbtXr1kzTU+qLNQj');
        audio.play().catch(() => {});
    }
}

function generateSampleNotifications() {
    // Génération de notifications d'exemple
    const sampleNotifs = [
        {
            id: 'sample_1',
            type: 'critique',
            title: 'Stock Épuisé',
            message: 'Couscous grain fin en rupture de stock',
            icon: 'fas fa-exclamation-triangle',
            iconColor: 'danger',
            time: new Date(Date.now() - 300000), // Il y a 5 minutes
            unread: true
        },
        {
            id: 'sample_2',
            type: 'important',
            title: 'Nouvelle Commande',
            message: 'Commande #1234 de 245 DH reçue',
            icon: 'fas fa-shopping-cart',
            iconColor: 'info',
            time: new Date(Date.now() - 600000), // Il y a 10 minutes
            unread: true
        }
    ];
    
    sampleNotifs.forEach(notif => notifications.push(notif));
    updateNotificationDisplay();
}
</script>
