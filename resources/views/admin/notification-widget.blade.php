<!-- =============================================================== -->
<!-- WIDGET DE NOTIFICATIONS EN TEMPS RÉEL - ACCESSPOS PRO -->
<!-- Système d'alertes et notifications intelligentes -->
<!-- =============================================================== -->

<div id="notificationWidget" class="notification-widget">
    <!-- Bouton de notification avec compteur -->
    <button id="notificationToggle" class="notification-toggle" onclick="toggleNotificationPanel()">
        <i class="fas fa-bell"></i>
        <span id="notificationCount" class="notification-count" style="display: none;">0</span>
        <span class="notification-pulse"></span>
    </button>
    
    <!-- Panel des notifications -->
    <div id="notificationPanel" class="notification-panel" style="display: none;">
        <!-- Header du panel -->
        <div class="notification-header">
            <h4>
                <i class="fas fa-bell me-2"></i>
                Notifications
                <span id="totalNotifications" class="badge bg-primary ms-2">0</span>
            </h4>
            <div class="notification-actions">
                <button onclick="refreshNotifications()" class="btn-action" title="Actualiser">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button onclick="markAllAsRead()" class="btn-action" title="Tout marquer comme lu">
                    <i class="fas fa-check-double"></i>
                </button>
                <button onclick="openNotificationSettings()" class="btn-action" title="Paramètres">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
        </div>
        
        <!-- Filtres de notification -->
        <div class="notification-filters">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-list"></i>
                Toutes
                <span class="filter-count" id="countAll">0</span>
            </button>
            <button class="filter-btn" data-filter="critique">
                <i class="fas fa-exclamation-triangle"></i>
                Critiques
                <span class="filter-count" id="countCritique">0</span>
            </button>
            <button class="filter-btn" data-filter="urgent">
                <i class="fas fa-clock"></i>
                Urgentes
                <span class="filter-count" id="countUrgent">0</span>
            </button>
            <button class="filter-btn" data-filter="important">
                <i class="fas fa-info-circle"></i>
                Importantes
                <span class="filter-count" id="countImportant">0</span>
            </button>
        </div>
        
        <!-- Contenu des notifications -->
        <div id="notificationContent" class="notification-content">
            <!-- Les notifications seront chargées ici via JavaScript -->
            <div class="loading-notifications">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <span class="ms-2">Chargement des notifications...</span>
            </div>
        </div>
        
        <!-- Footer du panel -->
        <div class="notification-footer">
            <button onclick="viewAllNotifications()" class="btn btn-outline-primary btn-sm w-100">
                <i class="fas fa-external-link-alt me-2"></i>
                Voir toutes les notifications
            </button>
        </div>
    </div>
</div>

<!-- Modal de paramètres des notifications -->
<div id="notificationSettingsModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-cog me-2"></i>
                    Paramètres des Notifications
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Configuration des alertes -->
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-boxes me-2 text-warning"></i>Alertes de Stock</h6>
                        <div class="mb-3">
                            <label class="form-label">Seuil d'alerte stock faible</label>
                            <input type="number" class="form-control" id="seuilStockFaible" value="5">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notifEmailStock" checked>
                            <label class="form-check-label" for="notifEmailStock">
                                Notifications par email
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-line me-2 text-primary"></i>Alertes de Performance</h6>
                        <div class="mb-3">
                            <label class="form-label">Objectif journalier (€)</label>
                            <input type="number" class="form-control" id="objectifJournalier" value="2000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Seuil d'alerte (%)</label>
                            <input type="range" class="form-range" id="seuilPerformance" min="50" max="100" value="70">
                            <small class="text-muted">70% de l'objectif</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-euro-sign me-2 text-success"></i>Alertes Financières</h6>
                        <div class="mb-3">
                            <label class="form-label">Seuil grosse transaction (€)</label>
                            <input type="number" class="form-control" id="seuilTransaction" value="500">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">% max espèces</label>
                            <input type="range" class="form-range" id="seuilEspeces" min="50" max="100" value="80">
                            <small class="text-muted">80% maximum en espèces</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6><i class="fas fa-utensils me-2 text-info"></i>Alertes Opérationnelles</h6>
                        <div class="mb-3">
                            <label class="form-label">Durée max occupation table (min)</label>
                            <input type="number" class="form-control" id="dureeMaxTable" value="120">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Délai alerte réservation (h)</label>
                            <input type="number" class="form-control" id="delaiReservation" value="2">
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Fréquence de mise à jour -->
                <div class="row">
                    <div class="col-12">
                        <h6><i class="fas fa-sync-alt me-2 text-secondary"></i>Fréquence de Mise à Jour</h6>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="frequence" id="freq30s" value="30">
                            <label class="form-check-label" for="freq30s">30 secondes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="frequence" id="freq1m" value="60" checked>
                            <label class="form-check-label" for="freq1m">1 minute</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="frequence" id="freq5m" value="300">
                            <label class="form-check-label" for="freq5m">5 minutes</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveNotificationSettings()">
                    <i class="fas fa-save me-2"></i>Sauvegarder
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* =============================================================== */
/* STYLES POUR LE WIDGET DE NOTIFICATIONS */
/* =============================================================== */

.notification-widget {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.notification-toggle {
    position: relative;
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(79, 70, 229, 0.4);
}

.notification-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ef4444;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: bounce 2s infinite;
}

.notification-pulse {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: rgba(79, 70, 229, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-3px);
    }
    60% {
        transform: translateY(-2px);
    }
}

.notification-panel {
    position: absolute;
    top: 70px;
    right: 0;
    width: 420px;
    max-height: 600px;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    animation: slideInDown 0.3s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-header {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.notification-header h4 {
    margin: 0;
    color: #1f2937;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    color: #6b7280;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-action:hover {
    background: rgba(79, 70, 229, 0.1);
    color: #4f46e5;
}

.notification-filters {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
}

.filter-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    background: white;
    color: #6b7280;
    border-radius: 2rem;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-btn:hover {
    border-color: #4f46e5;
    color: #4f46e5;
}

.filter-btn.active {
    background: #4f46e5;
    border-color: #4f46e5;
    color: white;
}

.filter-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.1rem 0.4rem;
    border-radius: 1rem;
    font-size: 0.7rem;
    font-weight: 600;
}

.filter-btn.active .filter-count {
    background: rgba(255, 255, 255, 0.3);
}

.notification-content {
    max-height: 360px;
    overflow-y: auto;
    padding: 0;
}

.loading-notifications {
    padding: 2rem;
    text-align: center;
    color: #6b7280;
    font-size: 0.875rem;
}

.notification-item {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s ease;
    cursor: pointer;
    position: relative;
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-item:hover {
    background: #f8fafc;
}

.notification-item.unread {
    background: rgba(79, 70, 229, 0.02);
    border-left: 4px solid #4f46e5;
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    top: 1rem;
    left: 0.5rem;
    width: 8px;
    height: 8px;
    background: #4f46e5;
    border-radius: 50%;
}

.notification-content-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.notification-icon.critique {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.notification-icon.urgent {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.notification-icon.important {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.notification-icon.info {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.notification-details {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.notification-message {
    color: #6b7280;
    font-size: 0.8rem;
    line-height: 1.4;
    margin-bottom: 0.5rem;
}

.notification-time {
    color: #9ca3af;
    font-size: 0.75rem;
}

.notification-actions-item {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.notification-action-btn {
    padding: 0.25rem 0.75rem;
    border: 1px solid #d1d5db;
    background: white;
    color: #374151;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.notification-action-btn:hover {
    border-color: #4f46e5;
    color: #4f46e5;
}

.notification-action-btn.primary {
    background: #4f46e5;
    border-color: #4f46e5;
    color: white;
}

.notification-action-btn.primary:hover {
    background: #3730a3;
}

.notification-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
}

.empty-notifications {
    padding: 3rem 2rem;
    text-align: center;
    color: #6b7280;
}

.empty-notifications i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

.empty-notifications h6 {
    color: #374151;
    margin-bottom: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .notification-panel {
        width: calc(100vw - 40px);
        right: -180px;
        left: 20px;
    }
    
    .notification-widget {
        right: 15px;
        top: 15px;
    }
    
    .notification-toggle {
        width: 48px;
        height: 48px;
        font-size: 1.1rem;
    }
    
    .notification-filters {
        padding: 0.75rem 1rem;
    }
    
    .filter-btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
    }
}

/* Animation de notification entrante */
.notification-item.new {
    animation: newNotification 0.6s ease-out;
}

@keyframes newNotification {
    0% {
        background: rgba(79, 70, 229, 0.2);
        transform: scale(1.02);
    }
    100% {
        background: rgba(79, 70, 229, 0.02);
        transform: scale(1);
    }
}

/* Scrollbar personnalisée */
.notification-content::-webkit-scrollbar {
    width: 4px;
}

.notification-content::-webkit-scrollbar-track {
    background: transparent;
}

.notification-content::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 2px;
}

.notification-content::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>

<script>
/* =============================================================== */
/* JAVASCRIPT POUR LE WIDGET DE NOTIFICATIONS */
/* =============================================================== */

// Variables globales
let notificationPanel = null;
let notificationUpdateInterval = null;
let currentFilter = 'all';
let notifications = [];
let notificationConfig = {
    updateFrequency: 60000, // 1 minute par défaut
    soundEnabled: true,
    autoMarkRead: false
};

// Initialisation du widget
document.addEventListener('DOMContentLoaded', function() {
    initializeNotificationWidget();
    loadNotificationSettings();
    startNotificationUpdates();
});

function initializeNotificationWidget() {
    notificationPanel = document.getElementById('notificationPanel');
    
    // Gestionnaire pour fermer le panel en cliquant à l'extérieur
    document.addEventListener('click', function(event) {
        const widget = document.getElementById('notificationWidget');
        if (!widget.contains(event.target)) {
            closeNotificationPanel();
        }
    });
    
    // Gestionnaire pour les filtres
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            setNotificationFilter(filter);
        });
    });
    
    // Première charge des notifications
    loadNotifications();
}

function toggleNotificationPanel() {
    const panel = document.getElementById('notificationPanel');
    const isVisible = panel.style.display !== 'none';
    
    if (isVisible) {
        closeNotificationPanel();
    } else {
        openNotificationPanel();
    }
}

function openNotificationPanel() {
    const panel = document.getElementById('notificationPanel');
    panel.style.display = 'block';
    
    // Marquer les notifications visibles comme lues automatiquement
    if (notificationConfig.autoMarkRead) {
        setTimeout(() => {
            markVisibleAsRead();
        }, 2000);
    }
}

function closeNotificationPanel() {
    const panel = document.getElementById('notificationPanel');
    panel.style.display = 'none';
}

async function loadNotifications() {
    try {
        showNotificationLoading();
        
        const response = await fetch('/admin/notifications/live', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success) {
            notifications = data.notifications;
            updateNotificationDisplay();
            updateNotificationCounts();
            
            // Jouer un son pour les nouvelles notifications critiques
            if (notificationConfig.soundEnabled) {
                checkForCriticalNotifications(data.notifications);
            }
        } else {
            showNotificationError('Erreur lors du chargement des notifications');
        }
        
    } catch (error) {
        console.error('Erreur lors du chargement des notifications:', error);
        showNotificationError('Impossible de charger les notifications');
    }
}

function showNotificationLoading() {
    const content = document.getElementById('notificationContent');
    content.innerHTML = `
        <div class="loading-notifications">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <span class="ms-2">Chargement des notifications...</span>
        </div>
    `;
}

function showNotificationError(message) {
    const content = document.getElementById('notificationContent');
    content.innerHTML = `
        <div class="empty-notifications">
            <i class="fas fa-exclamation-triangle text-warning"></i>
            <h6>Erreur de chargement</h6>
            <p>${message}</p>
            <button onclick="loadNotifications()" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-retry me-1"></i>Réessayer
            </button>
        </div>
    `;
}

function updateNotificationDisplay() {
    const content = document.getElementById('notificationContent');
    
    if (notifications.length === 0) {
        content.innerHTML = `
            <div class="empty-notifications">
                <i class="fas fa-bell-slash"></i>
                <h6>Aucune notification</h6>
                <p>Tout va bien ! Aucune alerte en cours.</p>
            </div>
        `;
        return;
    }
    
    const filteredNotifications = filterNotifications(notifications, currentFilter);
    
    if (filteredNotifications.length === 0) {
        content.innerHTML = `
            <div class="empty-notifications">
                <i class="fas fa-filter"></i>
                <h6>Aucune notification</h6>
                <p>Aucune notification ne correspond au filtre sélectionné.</p>
            </div>
        `;
        return;
    }
    
    const notificationsHtml = filteredNotifications.map(notification => 
        createNotificationHTML(notification)
    ).join('');
    
    content.innerHTML = notificationsHtml;
}

function createNotificationHTML(notification) {
    const timeAgo = formatTimeAgo(notification.timestamp);
    const actionsHtml = notification.actions ? 
        notification.actions.map(action => 
            `<button class="notification-action-btn ${action.primary ? 'primary' : ''}" 
                     onclick="executeNotificationAction('${action.action}', ${JSON.stringify(action.params || {}).replace(/"/g, '&quot;')}, '${notification.id}')">
                ${action.label}
             </button>`
        ).join('') : '';
    
    return `
        <div class="notification-item unread" data-id="${notification.id}" data-type="${notification.type}" data-priority="${notification.priorite}">
            <div class="notification-content-item">
                <div class="notification-icon ${notification.priorite}">
                    <i class="${notification.icone}"></i>
                </div>
                <div class="notification-details">
                    <div class="notification-title">${notification.titre}</div>
                    <div class="notification-message">${notification.message}</div>
                    <div class="notification-time">${timeAgo}</div>
                    ${actionsHtml ? `<div class="notification-actions-item">${actionsHtml}</div>` : ''}
                </div>
            </div>
        </div>
    `;
}

function filterNotifications(notifications, filter) {
    if (filter === 'all') {
        return notifications;
    }
    return notifications.filter(n => n.priorite === filter);
}

function updateNotificationCounts() {
    const total = notifications.length;
    const critiques = notifications.filter(n => n.priorite === 'critique').length;
    const urgentes = notifications.filter(n => n.priorite === 'urgent').length;
    const importantes = notifications.filter(n => n.priorite === 'important').length;
    
    // Mise à jour des compteurs
    document.getElementById('totalNotifications').textContent = total;
    document.getElementById('countAll').textContent = total;
    document.getElementById('countCritique').textContent = critiques;
    document.getElementById('countUrgent').textContent = urgentes;
    document.getElementById('countImportant').textContent = importantes;
    
    // Mise à jour du badge sur le bouton
    const countBadge = document.getElementById('notificationCount');
    if (total > 0) {
        countBadge.textContent = total > 99 ? '99+' : total;
        countBadge.style.display = 'flex';
    } else {
        countBadge.style.display = 'none';
    }
}

function setNotificationFilter(filter) {
    currentFilter = filter;
    
    // Mise à jour des boutons de filtre
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
    
    // Mise à jour de l'affichage
    updateNotificationDisplay();
}

function executeNotificationAction(action, params, notificationId) {
    switch(action) {
        case 'openAdvancedModal':
            if (typeof openAdvancedModal === 'function') {
                openAdvancedModal(params[0], params[1], params[2]);
                closeNotificationPanel();
            }
            break;
        case 'redirect':
            window.location.href = params.url;
            break;
        case 'launchBackup':
            launchBackup();
            break;
        default:
            console.log('Action non reconnue:', action);
    }
    
    // Marquer la notification comme lue
    markNotificationAsRead(notificationId);
}

async function markNotificationAsRead(notificationId) {
    try {
        const response = await fetch('/admin/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ notification_id: notificationId })
        });
        
        if (response.ok) {
            // Retirer visuellement la notification
            const notificationElement = document.querySelector(`[data-id="${notificationId}"]`);
            if (notificationElement) {
                notificationElement.classList.remove('unread');
                setTimeout(() => {
                    notificationElement.remove();
                    // Recharger si plus de notifications
                    if (document.querySelectorAll('.notification-item').length === 0) {
                        loadNotifications();
                    }
                }, 300);
            }
        }
    } catch (error) {
        console.error('Erreur lors du marquage comme lu:', error);
    }
}

function markAllAsRead() {
    notifications.forEach(notification => {
        markNotificationAsRead(notification.id);
    });
}

function refreshNotifications() {
    loadNotifications();
}

function openNotificationSettings() {
    const modal = new bootstrap.Modal(document.getElementById('notificationSettingsModal'));
    modal.show();
    loadCurrentSettings();
}

function loadCurrentSettings() {
    // Charger les paramètres actuels dans le modal
    fetch('/admin/notifications/config')
        .then(response => response.json())
        .then(config => {
            document.getElementById('seuilStockFaible').value = config.stock.seuil_stock_faible;
            document.getElementById('objectifJournalier').value = config.performance.objectif_journalier;
            document.getElementById('seuilPerformance').value = config.performance.seuil_alerte_performance;
            document.getElementById('seuilTransaction').value = config.financier.seuil_grosse_transaction;
            document.getElementById('seuilEspeces').value = config.financier.seuil_especes_elevees;
            document.getElementById('dureeMaxTable').value = config.operationnel.duree_max_occupation_table;
            document.getElementById('delaiReservation').value = config.operationnel.delai_alerte_reservation;
            
            // Sélectionner la fréquence
            document.querySelector(`input[name="frequence"][value="${notificationConfig.updateFrequency / 1000}"]`).checked = true;
        })
        .catch(error => console.error('Erreur lors du chargement de la config:', error));
}

function saveNotificationSettings() {
    const config = {
        stock: {
            seuil_stock_faible: parseInt(document.getElementById('seuilStockFaible').value),
            notification_email: document.getElementById('notifEmailStock').checked
        },
        performance: {
            objectif_journalier: parseInt(document.getElementById('objectifJournalier').value),
            seuil_alerte_performance: parseInt(document.getElementById('seuilPerformance').value)
        },
        financier: {
            seuil_grosse_transaction: parseInt(document.getElementById('seuilTransaction').value),
            seuil_especes_elevees: parseInt(document.getElementById('seuilEspeces').value)
        },
        operationnel: {
            duree_max_occupation_table: parseInt(document.getElementById('dureeMaxTable').value),
            delai_alerte_reservation: parseInt(document.getElementById('delaiReservation').value)
        }
    };
    
    const frequence = document.querySelector('input[name="frequence"]:checked').value;
    notificationConfig.updateFrequency = parseInt(frequence) * 1000;
    
    // Sauvegarder la configuration
    fetch('/admin/notifications/config', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(config)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Fermer le modal
            bootstrap.Modal.getInstance(document.getElementById('notificationSettingsModal')).hide();
            
            // Redémarrer les mises à jour avec la nouvelle fréquence
            startNotificationUpdates();
            
            // Afficher un message de succès
            showToast('Paramètres sauvegardés avec succès', 'success');
        }
    })
    .catch(error => {
        console.error('Erreur lors de la sauvegarde:', error);
        showToast('Erreur lors de la sauvegarde', 'error');
    });
}

function startNotificationUpdates() {
    // Arrêter l'ancien intervalle s'il existe
    if (notificationUpdateInterval) {
        clearInterval(notificationUpdateInterval);
    }
    
    // Démarrer le nouveau avec la fréquence configurée
    notificationUpdateInterval = setInterval(() => {
        loadNotifications();
    }, notificationConfig.updateFrequency);
}

function loadNotificationSettings() {
    // Charger les paramètres depuis le stockage local ou l'API
    const savedConfig = localStorage.getItem('accesspos_notification_config');
    if (savedConfig) {
        notificationConfig = { ...notificationConfig, ...JSON.parse(savedConfig) };
    }
}

function checkForCriticalNotifications(newNotifications) {
    const criticalNotifications = newNotifications.filter(n => n.priorite === 'critique');
    if (criticalNotifications.length > 0) {
        // Jouer un son d'alerte (si supporté par le navigateur)
        playNotificationSound();
        
        // Faire clignoter le bouton
        animateNotificationButton();
    }
}

function playNotificationSound() {
    try {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmEaAze...');
        audio.volume = 0.3;
        audio.play().catch(e => console.log('Son non supporté:', e));
    } catch (e) {
        console.log('Son non supporté:', e);
    }
}

function animateNotificationButton() {
    const button = document.getElementById('notificationToggle');
    button.style.animation = 'shake 0.5s ease-in-out 0s 3';
    setTimeout(() => {
        button.style.animation = '';
    }, 1500);
}

function formatTimeAgo(timestamp) {
    const now = new Date();
    const time = new Date(timestamp);
    const diffInSeconds = Math.floor((now - time) / 1000);
    
    if (diffInSeconds < 60) {
        return 'À l\'instant';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `Il y a ${minutes} minute${minutes > 1 ? 's' : ''}`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `Il y a ${hours} heure${hours > 1 ? 's' : ''}`;
    } else {
        return time.toLocaleDateString('fr-FR');
    }
}

function viewAllNotifications() {
    // Rediriger vers une page dédiée aux notifications
    window.location.href = '/admin/notifications';
}

function showToast(message, type = 'info') {
    // Créer un toast Bootstrap ou un système de notification simple
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.cssText = 'position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 10000;';
    document.body.appendChild(container);
    return container;
}

// Animation de tremblement pour les alertes critiques
const shakeKeyframes = `
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}
`;

// Ajouter les keyframes au document
const style = document.createElement('style');
style.textContent = shakeKeyframes;
document.head.appendChild(style);
</script>
