# Documentation des Classes CSS Personnalisées
**AccessPos Pro - SB Admin 2 Integration**  
**Date:** 12 juillet 2025

---

## 📋 Table des Matières

1. [Classes Layout](#classes-layout)
2. [Classes Composants](#classes-composants)
3. [Classes Utilitaires](#classes-utilitaires)
4. [Classes Responsive](#classes-responsive)
5. [Classes Animations](#classes-animations)
6. [Classes Thème](#classes-thème)
7. [Classes Performance](#classes-performance)
8. [Classes Accessibilité](#classes-accessibilité)

---

## 🏗️ Classes Layout

### Container Personnalisés
```css
/* Container avec padding personnalisé */
.container-accesspos {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Container pleine hauteur */
.container-full-height {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Container centré */
.container-centered {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
}
```

**Utilisation:**
```html
<div class="container-accesspos">Contenu avec padding personnalisé</div>
<div class="container-full-height">Page pleine hauteur</div>
<div class="container-centered">Contenu centré</div>
```

### Sidebar Personnalisations
```css
/* Sidebar collapse states */
.sidebar-collapsed {
  margin-left: 0 !important;
  transition: margin-left 0.3s ease;
}

.sidebar-expanded {
  margin-left: 224px !important;
  transition: margin-left 0.3s ease;
}

/* Sidebar custom colors */
.sidebar-dark {
  background: linear-gradient(180deg, #2d3748 10%, #1a202c 100%);
}

.sidebar-light {
  background: #ffffff;
  border-right: 1px solid #e3e6f0;
}

/* Sidebar brand styling */
.sidebar-brand-accesspos {
  display: flex;
  align-items: center;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.1);
}
```

### Topbar Enhancements
```css
/* Topbar fixed height */
.topbar-fixed {
  position: fixed;
  top: 0;
  right: 0;
  left: 224px;
  z-index: 1030;
  transition: left 0.3s ease;
}

/* Topbar search enhanced */
.topbar-search {
  position: relative;
  flex: 1;
  max-width: 400px;
}

.topbar-search-results {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #e3e6f0;
  border-radius: 0.35rem;
  max-height: 300px;
  overflow-y: auto;
  z-index: 1000;
}
```

---

## 🎨 Classes Composants

### Cards Améliorées
```css
/* Card avec gradient background */
.card-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.card-gradient-success {
  background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  color: white;
}

/* Card avec hover effect */
.card-hover {
  transition: all 0.3s ease;
  cursor: pointer;
}

.card-hover:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

/* Card stats */
.card-stat {
  border-left: 4px solid;
  border-radius: 0.35rem;
  overflow: hidden;
}

.card-stat-primary { border-left-color: #4e73df; }
.card-stat-success { border-left-color: #1cc88a; }
.card-stat-warning { border-left-color: #f6c23e; }
.card-stat-danger { border-left-color: #e74a3b; }
.card-stat-info { border-left-color: #36b9cc; }

/* Card compact */
.card-compact {
  padding: 0.5rem;
}

.card-compact .card-body {
  padding: 1rem;
}
```

### Boutons Personnalisés
```css
/* Bouton AccessPos */
.btn-accesspos {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  color: white;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 0.75rem 1.5rem;
  border-radius: 0.35rem;
  transition: all 0.3s ease;
}

.btn-accesspos:hover {
  background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
  transform: translateY(-1px);
  box-shadow: 0 7px 14px rgba(102, 126, 234, 0.4);
  color: white;
}

/* Bouton avec icône */
.btn-icon {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-icon i {
  font-size: 0.875em;
}

/* Bouton loading */
.btn-loading {
  position: relative;
  color: transparent !important;
}

.btn-loading::after {
  content: "";
  position: absolute;
  width: 16px;
  height: 16px;
  top: 50%;
  left: 50%;
  margin-left: -8px;
  margin-top: -8px;
  border: 2px solid transparent;
  border-top-color: currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

/* Bouton circle */
.btn-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
}

.btn-circle.btn-sm {
  width: 32px;
  height: 32px;
}

.btn-circle.btn-lg {
  width: 48px;
  height: 48px;
}
```

### Formulaires Améliorés
```css
/* Input groups enhanced */
.input-group-accesspos {
  border-radius: 0.35rem;
  overflow: hidden;
  box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.input-group-accesspos .form-control {
  border: none;
  background: #f8f9fc;
}

.input-group-accesspos .input-group-text {
  background: #4e73df;
  color: white;
  border: none;
}

/* Form floating labels */
.form-floating-accesspos {
  position: relative;
}

.form-floating-accesspos .form-control {
  padding: 1rem 0.75rem 0.25rem;
  background: transparent;
}

.form-floating-accesspos label {
  position: absolute;
  top: 0;
  left: 0.75rem;
  padding: 0.25rem 0;
  color: #6c757d;
  font-size: 0.875rem;
  transition: all 0.3s ease;
  pointer-events: none;
}

/* Form validation enhanced */
.form-control.is-valid {
  border-color: #1cc88a;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%231cc88a' d='m2.3 6.73.94-.94-1.2-1.2L3.6 3l1.8 1.8-.94.94zM.5 3.5l-.94-.94L1.1 1l.94.94zm2.4-2.4L1.7 0 3.2 1.5l1.5-1.5L6.2 1l-1.5 1.5L6.2 4l-1.5-1.5z'/%3e%3c/svg%3e");
}

.form-control.is-invalid {
  border-color: #e74a3b;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23e74a3b'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6.7.7.7-.7'/%3e%3c/svg%3e");
}
```

### DataTables Personnalisées
```css
/* DataTable wrapper */
.datatable-wrapper {
  background: white;
  border-radius: 0.35rem;
  box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
  overflow: hidden;
}

/* DataTable header */
.datatable-header {
  background: #f8f9fc;
  padding: 1rem;
  border-bottom: 1px solid #e3e6f0;
}

/* DataTable enhanced styling */
.table-accesspos {
  font-size: 0.875rem;
}

.table-accesspos thead th {
  background: #f8f9fc;
  border-color: #e3e6f0;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-size: 0.75rem;
  color: #858796;
}

.table-accesspos tbody tr:hover {
  background: rgba(78, 115, 223, 0.05);
}

/* DataTable actions */
.table-actions {
  white-space: nowrap;
}

.table-actions .btn {
  margin-right: 0.25rem;
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}
```

---

## 🔧 Classes Utilitaires

### Espacement Personnalisé
```css
/* Marges personnalisées */
.m-xs { margin: 0.125rem; }    /* 2px */
.m-xxl { margin: 4rem; }       /* 64px */

/* Padding personnalisé */
.p-xs { padding: 0.125rem; }   /* 2px */
.p-xxl { padding: 4rem; }      /* 64px */

/* Espacement spécifique */
.gap-xs { gap: 0.25rem; }      /* 4px */
.gap-sm { gap: 0.5rem; }       /* 8px */
.gap-md { gap: 1rem; }         /* 16px */
.gap-lg { gap: 1.5rem; }       /* 24px */
.gap-xl { gap: 2rem; }         /* 32px */
```

### Couleurs Personnalisées
```css
/* Couleurs de texte */
.text-accesspos { color: #667eea !important; }
.text-light-gray { color: #f8f9fc !important; }
.text-dark-gray { color: #3a3b45 !important; }

/* Couleurs de fond */
.bg-accesspos { background-color: #667eea !important; }
.bg-gradient-accesspos {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}
.bg-light-gray { background-color: #f8f9fc !important; }

/* Bordures colorées */
.border-accesspos { border-color: #667eea !important; }
.border-light-gray { border-color: #e3e6f0 !important; }
```

### Shadows et Effects
```css
/* Ombres personnalisées */
.shadow-xs { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important; }
.shadow-xl { box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important; }
.shadow-colored { box-shadow: 0 0.15rem 1.75rem 0 rgba(78, 115, 223, 0.15) !important; }

/* Effets hover */
.hover-lift {
  transition: transform 0.3s ease;
}

.hover-lift:hover {
  transform: translateY(-2px);
}

.hover-shadow {
  transition: box-shadow 0.3s ease;
}

.hover-shadow:hover {
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
```

### Positioning
```css
/* Position sticky avec offset */
.sticky-top-offset {
  position: sticky;
  top: 65px; /* Hauteur du topbar */
  z-index: 1020;
}

/* Position absolue centrée */
.absolute-center {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

/* Position overlay */
.overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
}
```

---

## 📱 Classes Responsive

### Visibilité Responsive
```css
/* Visibilité par taille d'écran */
.visible-xs { display: block !important; }
.visible-sm { display: none !important; }
.visible-md { display: none !important; }
.visible-lg { display: none !important; }
.visible-xl { display: none !important; }

@media (min-width: 576px) {
  .visible-xs { display: none !important; }
  .visible-sm { display: block !important; }
}

@media (min-width: 768px) {
  .visible-sm { display: none !important; }
  .visible-md { display: block !important; }
}

@media (min-width: 992px) {
  .visible-md { display: none !important; }
  .visible-lg { display: block !important; }
}

@media (min-width: 1200px) {
  .visible-lg { display: none !important; }
  .visible-xl { display: block !important; }
}

/* Classes pour mobile seulement */
.mobile-only {
  display: block;
}

@media (min-width: 768px) {
  .mobile-only {
    display: none !important;
  }
}

/* Classes pour desktop seulement */
.desktop-only {
  display: none;
}

@media (min-width: 768px) {
  .desktop-only {
    display: block !important;
  }
}
```

### Spacing Responsive
```css
/* Marges responsive */
.m-responsive {
  margin: 0.5rem;
}

@media (min-width: 768px) {
  .m-responsive {
    margin: 1rem;
  }
}

@media (min-width: 1200px) {
  .m-responsive {
    margin: 2rem;
  }
}

/* Padding responsive */
.p-responsive {
  padding: 0.5rem;
}

@media (min-width: 768px) {
  .p-responsive {
    padding: 1rem;
  }
}

@media (min-width: 1200px) {
  .p-responsive {
    padding: 2rem;
  }
}
```

### Flex Responsive
```css
/* Direction flex responsive */
.flex-column-mobile {
  display: flex;
  flex-direction: column;
}

@media (min-width: 768px) {
  .flex-column-mobile {
    flex-direction: row;
  }
}

/* Ordre responsive */
.order-first-mobile {
  order: -1;
}

@media (min-width: 768px) {
  .order-first-mobile {
    order: 0;
  }
}
```

---

## 🎬 Classes Animations

### Animations d'Entrée
```css
/* Fade in */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.fade-in {
  animation: fadeIn 0.5s ease-in;
}

.fade-in-slow {
  animation: fadeIn 1s ease-in;
}

.fade-in-fast {
  animation: fadeIn 0.3s ease-in;
}

/* Slide in */
@keyframes slideInUp {
  from {
    transform: translate3d(0, 100%, 0);
    visibility: visible;
  }
  to {
    transform: translate3d(0, 0, 0);
  }
}

@keyframes slideInDown {
  from {
    transform: translate3d(0, -100%, 0);
    visibility: visible;
  }
  to {
    transform: translate3d(0, 0, 0);
  }
}

@keyframes slideInLeft {
  from {
    transform: translate3d(-100%, 0, 0);
    visibility: visible;
  }
  to {
    transform: translate3d(0, 0, 0);
  }
}

@keyframes slideInRight {
  from {
    transform: translate3d(100%, 0, 0);
    visibility: visible;
  }
  to {
    transform: translate3d(0, 0, 0);
  }
}

.slide-in-up { animation: slideInUp 0.3s ease-out; }
.slide-in-down { animation: slideInDown 0.3s ease-out; }
.slide-in-left { animation: slideInLeft 0.3s ease-out; }
.slide-in-right { animation: slideInRight 0.3s ease-out; }
```

### Animations de Loading
```css
/* Spinner */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.spin {
  animation: spin 1s linear infinite;
}

/* Pulse */
@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

.pulse {
  animation: pulse 2s ease-in-out infinite;
}

/* Bounce */
@keyframes bounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translate3d(0, 0, 0);
  }
  40%, 43% {
    transform: translate3d(0, -30px, 0);
  }
  70% {
    transform: translate3d(0, -15px, 0);
  }
  90% {
    transform: translate3d(0, -4px, 0);
  }
}

.bounce {
  animation: bounce 1s ease-in-out infinite;
}

/* Loading dots */
@keyframes loadingDots {
  0%, 80%, 100% {
    opacity: 0;
  }
  40% {
    opacity: 1;
  }
}

.loading-dots {
  display: inline-block;
}

.loading-dots span {
  display: inline-block;
  width: 8px;
  height: 8px;
  margin: 0 2px;
  background-color: #4e73df;
  border-radius: 50%;
  animation: loadingDots 1.4s infinite ease-in-out both;
}

.loading-dots span:nth-child(1) { animation-delay: -0.32s; }
.loading-dots span:nth-child(2) { animation-delay: -0.16s; }
.loading-dots span:nth-child(3) { animation-delay: 0; }
```

### Hover Animations
```css
/* Scale hover */
.hover-scale {
  transition: transform 0.3s ease;
}

.hover-scale:hover {
  transform: scale(1.05);
}

/* Rotate hover */
.hover-rotate {
  transition: transform 0.3s ease;
}

.hover-rotate:hover {
  transform: rotate(5deg);
}

/* Glow hover */
.hover-glow {
  transition: box-shadow 0.3s ease;
}

.hover-glow:hover {
  box-shadow: 0 0 20px rgba(78, 115, 223, 0.6);
}
```

---

## 🎨 Classes Thème

### Dark Mode
```css
/* Variables dark mode */
[data-theme="dark"] {
  --body-bg: #1a1a1a;
  --card-bg: #2d2d2d;
  --text-color: #ffffff;
  --text-muted: #adb5bd;
  --border-color: #495057;
  --sidebar-bg: #212529;
  --topbar-bg: #343a40;
}

/* Applications dark mode */
[data-theme="dark"] .card {
  background-color: var(--card-bg);
  color: var(--text-color);
}

[data-theme="dark"] .table {
  color: var(--text-color);
}

[data-theme="dark"] .table th,
[data-theme="dark"] .table td {
  border-color: var(--border-color);
}

[data-theme="dark"] .form-control {
  background-color: var(--card-bg);
  border-color: var(--border-color);
  color: var(--text-color);
}

/* Toggle dark mode */
.theme-toggle {
  position: relative;
  width: 60px;
  height: 30px;
  background: #ddd;
  border-radius: 15px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.theme-toggle.dark {
  background: #4e73df;
}

.theme-toggle::after {
  content: '';
  position: absolute;
  top: 3px;
  left: 3px;
  width: 24px;
  height: 24px;
  background: white;
  border-radius: 50%;
  transition: transform 0.3s ease;
}

.theme-toggle.dark::after {
  transform: translateX(30px);
}
```

### Thème Couleurs Alternatives
```css
/* Thème bleu */
.theme-blue {
  --primary: #007bff;
  --primary-dark: #0056b3;
  --primary-light: #66b3ff;
}

/* Thème vert */
.theme-green {
  --primary: #28a745;
  --primary-dark: #1e7e34;
  --primary-light: #6ed576;
}

/* Thème rouge */
.theme-red {
  --primary: #dc3545;
  --primary-dark: #a71d2a;
  --primary-light: #e7616e;
}

/* Application des thèmes */
.theme-blue .btn-primary,
.theme-green .btn-primary,
.theme-red .btn-primary {
  background-color: var(--primary);
  border-color: var(--primary);
}

.theme-blue .text-primary,
.theme-green .text-primary,
.theme-red .text-primary {
  color: var(--primary) !important;
}
```

---

## ⚡ Classes Performance

### Optimisations CSS
```css
/* GPU acceleration */
.gpu-accelerated {
  transform: translateZ(0);
  will-change: transform;
}

/* Optimisation animations */
.optimized-animation {
  will-change: transform, opacity;
  backface-visibility: hidden;
  perspective: 1000px;
}

/* Lazy loading images */
.lazy-image {
  opacity: 0;
  transition: opacity 0.3s ease;
}

.lazy-image.loaded {
  opacity: 1;
}

/* Contenu critique */
.critical-content {
  display: block;
  visibility: visible;
}

.non-critical-content {
  display: none;
}

@media (min-width: 768px) {
  .non-critical-content {
    display: block;
  }
}
```

### Loading States
```css
/* Skeleton loading */
.skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  border-radius: 4px;
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

.skeleton-text {
  height: 1rem;
  margin-bottom: 0.5rem;
}

.skeleton-text:last-child {
  width: 60%;
}

.skeleton-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.skeleton-button {
  width: 100px;
  height: 36px;
}
```

---

## ♿ Classes Accessibilité

### Screen Reader
```css
/* Contenu pour lecteurs d'écran seulement */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

.sr-only-focusable:active,
.sr-only-focusable:focus {
  position: static;
  width: auto;
  height: auto;
  overflow: visible;
  clip: auto;
  white-space: normal;
}
```

### Focus States
```css
/* Focus visible amélioré */
.focus-visible {
  outline: 2px solid #4e73df;
  outline-offset: 2px;
}

/* Skip links */
.skip-link {
  position: absolute;
  top: -40px;
  left: 6px;
  background: #4e73df;
  color: white;
  padding: 8px;
  text-decoration: none;
  z-index: 9999;
}

.skip-link:focus {
  top: 6px;
}
```

### Contrast et Lisibilité
```css
/* High contrast mode */
@media (prefers-contrast: high) {
  .btn {
    border: 2px solid;
  }
  
  .card {
    border: 1px solid #000;
  }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}

/* Text size adaptation */
.large-text {
  font-size: 1.125rem;
  line-height: 1.6;
}

.extra-large-text {
  font-size: 1.25rem;
  line-height: 1.7;
}
```

---

## 🔍 Exemples d'Utilisation

### Card Statistique
```html
<div class="card card-stat card-stat-primary hover-lift">
  <div class="card-body">
    <div class="row no-gutters align-items-center">
      <div class="col mr-2">
        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
          Ventes du jour
        </div>
        <div class="h5 mb-0 font-weight-bold text-gray-800">1,234 €</div>
      </div>
      <div class="col-auto">
        <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
      </div>
    </div>
  </div>
</div>
```

### Bouton avec Animation
```html
<button class="btn btn-accesspos hover-lift btn-icon" onclick="saveData()">
  <i class="fas fa-save"></i>
  <span>Enregistrer</span>
</button>
```

### Table Responsive
```html
<div class="datatable-wrapper">
  <div class="datatable-header">
    <h6 class="m-0 font-weight-bold text-primary">Articles</h6>
  </div>
  <div class="table-responsive">
    <table class="table table-accesspos" id="articlesTable">
      <!-- Contenu table -->
    </table>
  </div>
</div>
```

### Layout Responsive
```html
<div class="container-fluid">
  <div class="row">
    <div class="col-12 col-lg-8 mobile-stack">
      <div class="card fade-in">
        <!-- Contenu principal -->
      </div>
    </div>
    <div class="col-12 col-lg-4 mobile-stack">
      <div class="card slide-in-right">
        <!-- Sidebar -->
      </div>
    </div>
  </div>
</div>
```

---

## 📚 Référence Rapide

### Classes les Plus Utilisées
```css
/* Layout */
.container-accesspos, .container-full-height, .container-centered

/* Composants */
.card-hover, .card-stat, .btn-accesspos, .btn-icon, .btn-loading

/* Utilitaires */
.fade-in, .slide-in-up, .hover-lift, .shadow-colored

/* Responsive */
.mobile-only, .desktop-only, .flex-column-mobile

/* Thème */
[data-theme="dark"], .theme-blue, .theme-green

/* Performance */
.skeleton, .lazy-image, .gpu-accelerated

/* Accessibilité */
.sr-only, .focus-visible, .skip-link
```

### Conventions de Nommage
- **Préfixes:** `.accesspos-`, `.sb-admin-`
- **Modificateurs:** `--primary`, `--large`, `--mobile`
- **États:** `.is-active`, `.is-loading`, `.is-visible`
- **Breakpoints:** `-xs`, `-sm`, `-md`, `-lg`, `-xl`

---

**Version:** 1.0.0  
**Dernière mise à jour:** 12 juillet 2025  
**Fichiers sources:**
- `resources/css/custom-sb-admin.css`
- `resources/css/performance-optimizations.css`
- `resources/css/mobile-responsive.css`
- `resources/css/accessibility.css`
