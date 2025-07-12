# Guide de Style AccessPos Pro
**Version SB Admin 2 Integration**  
**Date:** 12 juillet 2025

---

## 🎨 Palette de Couleurs

### Couleurs Principales
```css
/* Couleur primaire - Bleu SB Admin */
--primary: #4e73df;
--primary-dark: #224abe;
--primary-light: #6f8eea;

/* Couleurs système */
--success: #1cc88a;  /* Vert succès */
--info: #36b9cc;     /* Bleu info */
--warning: #f6c23e;  /* Jaune attention */
--danger: #e74a3b;   /* Rouge erreur */
```

### Couleurs Neutres
```css
/* Échelle de gris */
--gray-100: #f8f9fc;
--gray-200: #eaecf4;
--gray-300: #dddfeb;
--gray-400: #d1d3e2;
--gray-500: #b7b9cc;
--gray-600: #858796;
--gray-700: #6e707e;
--gray-800: #5a5c69;
--gray-900: #3a3b45;
```

### Usage des Couleurs
- **Primaire:** Actions principales, liens importants
- **Succès:** Confirmations, états positifs
- **Info:** Informations neutres, conseils
- **Attention:** Avertissements, états d'attente
- **Danger:** Erreurs, suppressions, actions critiques

---

## 📝 Typographie

### Police Principal
```css
font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
```

### Hiérarchie des Titres
```css
/* H1 - Titre de page principal */
.h1, h1 {
  font-size: 2.5rem;
  font-weight: 400;
  line-height: 1.2;
  color: #5a5c69;
}

/* H2 - Titre de section */
.h2, h2 {
  font-size: 2rem;
  font-weight: 400;
  line-height: 1.2;
  color: #5a5c69;
}

/* H3 - Titre de sous-section */
.h3, h3 {
  font-size: 1.75rem;
  font-weight: 400;
  line-height: 1.2;
  color: #5a5c69;
}

/* H4 - Titre de card/widget */
.h4, h4 {
  font-size: 1.5rem;
  font-weight: 400;
  line-height: 1.2;
  color: #5a5c69;
}

/* H5 - Titre de modal/composant */
.h5, h5 {
  font-size: 1.25rem;
  font-weight: 400;
  line-height: 1.2;
  color: #5a5c69;
}

/* H6 - Sous-titre/label */
.h6, h6 {
  font-size: 1rem;
  font-weight: 700;
  line-height: 1.2;
  color: #858796;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
```

### Tailles de Texte
```css
.text-xs { font-size: 0.75rem; }    /* 12px */
.text-sm { font-size: 0.875rem; }   /* 14px */
.text-base { font-size: 1rem; }     /* 16px */
.text-lg { font-size: 1.125rem; }   /* 18px */
.text-xl { font-size: 1.25rem; }    /* 20px */
```

### Weights de Police
```css
.font-weight-light { font-weight: 300; }
.font-weight-normal { font-weight: 400; }
.font-weight-bold { font-weight: 700; }
.font-weight-bolder { font-weight: 900; }
```

---

## 🔲 Espacement

### Système d'Espacement
```css
/* Bootstrap spacing scale (0.25rem = 4px base) */
.m-0 { margin: 0; }
.m-1 { margin: 0.25rem; }    /* 4px */
.m-2 { margin: 0.5rem; }     /* 8px */
.m-3 { margin: 1rem; }       /* 16px */
.m-4 { margin: 1.5rem; }     /* 24px */
.m-5 { margin: 3rem; }       /* 48px */

/* Padding identique avec .p-* */
```

### Espacement Spécifique AccessPos
```css
/* Espacement entre sections */
.section-spacing { margin-bottom: 2rem; }

/* Espacement pour cards */
.card-spacing { margin-bottom: 1.5rem; }

/* Espacement pour formulaires */
.form-group { margin-bottom: 1rem; }
.form-section { margin-bottom: 2rem; }
```

---

## 🎯 Composants UI

### Boutons

#### Boutons Primaires
```html
<!-- Bouton action principale -->
<button class="btn btn-primary">
  <i class="fas fa-save fa-sm text-white-50"></i> Enregistrer
</button>

<!-- Bouton secondaire -->
<button class="btn btn-secondary">Annuler</button>

<!-- Bouton succès -->
<button class="btn btn-success">
  <i class="fas fa-check fa-sm text-white-50"></i> Valider
</button>

<!-- Bouton danger -->
<button class="btn btn-danger">
  <i class="fas fa-trash fa-sm text-white-50"></i> Supprimer
</button>
```

#### Tailles de Boutons
```html
<button class="btn btn-primary btn-sm">Petit</button>
<button class="btn btn-primary">Normal</button>
<button class="btn btn-primary btn-lg">Grand</button>
```

#### Boutons avec Icônes
```html
<!-- Icône à gauche -->
<button class="btn btn-primary">
  <i class="fas fa-plus fa-sm text-white-50"></i> Ajouter
</button>

<!-- Icône seulement -->
<button class="btn btn-circle btn-primary">
  <i class="fas fa-edit"></i>
</button>
```

### Cards

#### Card Standard
```html
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Titre de la Card</h6>
  </div>
  <div class="card-body">
    Contenu de la card
  </div>
</div>
```

#### Card avec Bordure Colorée
```html
<div class="card border-left-primary shadow h-100 py-2">
  <div class="card-body">
    <div class="row no-gutters align-items-center">
      <div class="col mr-2">
        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
          Label
        </div>
        <div class="h5 mb-0 font-weight-bold text-gray-800">Valeur</div>
      </div>
      <div class="col-auto">
        <i class="fas fa-chart-area fa-2x text-gray-300"></i>
      </div>
    </div>
  </div>
</div>
```

### Formulaires

#### Structure de Formulaire
```html
<form class="user">
  <div class="form-group">
    <label for="nom" class="form-label">Nom de l'article</label>
    <input type="text" class="form-control form-control-user" 
           id="nom" name="nom" placeholder="Entrez le nom...">
    <div class="invalid-feedback">Ce champ est requis.</div>
  </div>
  
  <div class="form-group row">
    <div class="col-sm-6">
      <input type="text" class="form-control" placeholder="Champ 1">
    </div>
    <div class="col-sm-6">
      <input type="text" class="form-control" placeholder="Champ 2">
    </div>
  </div>
  
  <button type="submit" class="btn btn-primary btn-user btn-block">
    Enregistrer
  </button>
</form>
```

#### États de Validation
```html
<!-- Champ valide -->
<input type="text" class="form-control is-valid">
<div class="valid-feedback">Parfait!</div>

<!-- Champ invalide -->
<input type="text" class="form-control is-invalid">
<div class="invalid-feedback">Ce champ est requis.</div>
```

### Tables

#### DataTable Standard
```html
<div class="table-responsive">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Nom</th>
        <th>Prix</th>
        <th>Stock</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <!-- Données via AJAX -->
    </tbody>
  </table>
</div>
```

### Modals

#### Modal Standard
```html
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Titre du Modal</h5>
        <button class="close" type="button" data-dismiss="modal">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        Contenu du modal
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">
          Annuler
        </button>
        <button class="btn btn-primary" type="button">
          Confirmer
        </button>
      </div>
    </div>
  </div>
</div>
```

---

## 📱 Design Responsive

### Breakpoints
```css
/* Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) { }

/* Small devices (landscape phones, 576px and up) */
@media (min-width: 576px) and (max-width: 767.98px) { }

/* Medium devices (tablets, 768px and up) */
@media (min-width: 768px) and (max-width: 991.98px) { }

/* Large devices (desktops, 992px and up) */
@media (min-width: 992px) and (max-width: 1199.98px) { }

/* Extra large devices (large desktops, 1200px and up) */
@media (min-width: 1200px) { }
```

### Classes Responsive
```html
<!-- Visibilité responsive -->
<div class="d-none d-md-block">Visible uniquement sur desktop</div>
<div class="d-block d-md-none">Visible uniquement sur mobile</div>

<!-- Colonnes responsive -->
<div class="col-12 col-md-6 col-lg-4">
  Responsive column
</div>
```

---

## 🎭 États et Interactions

### États de Hover
```css
/* Boutons */
.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 7px 14px rgba(50, 50, 93, .1), 0 3px 6px rgba(0, 0, 0, .08);
}

/* Cards */
.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 7px 14px rgba(50, 50, 93, .1), 0 3px 6px rgba(0, 0, 0, .08);
}

/* Links */
.nav-link:hover {
  color: #4e73df !important;
}
```

### États de Focus
```css
.form-control:focus {
  border-color: #4e73df;
  box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn:focus {
  box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}
```

### Animations
```css
/* Animations d'entrée */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideInUp {
  from {
    transform: translate3d(0, 100%, 0);
    visibility: visible;
  }
  to {
    transform: translate3d(0, 0, 0);
  }
}

/* Classes utilitaires */
.fade-in { animation: fadeIn 0.5s ease-in; }
.slide-in-up { animation: slideInUp 0.3s ease-out; }
```

---

## 🚀 Loading States

### Spinners
```html
<!-- Spinner simple -->
<div class="spinner-border text-primary" role="status">
  <span class="sr-only">Chargement...</span>
</div>

<!-- Spinner dans bouton -->
<button class="btn btn-primary" type="button" disabled>
  <span class="spinner-border spinner-border-sm" role="status"></span>
  Chargement...
</button>
```

### Skeleton Loading
```css
.skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
```

---

## 🎯 Iconographie

### Icônes FontAwesome
```html
<!-- Icônes courantes -->
<i class="fas fa-home"></i>          <!-- Accueil -->
<i class="fas fa-tachometer-alt"></i> <!-- Dashboard -->
<i class="fas fa-box"></i>           <!-- Articles -->
<i class="fas fa-users"></i>         <!-- Utilisateurs -->
<i class="fas fa-chart-area"></i>    <!-- Graphiques -->
<i class="fas fa-cog"></i>           <!-- Paramètres -->
<i class="fas fa-sign-out-alt"></i>  <!-- Déconnexion -->

<!-- Icônes d'actions -->
<i class="fas fa-plus"></i>          <!-- Ajouter -->
<i class="fas fa-edit"></i>          <!-- Modifier -->
<i class="fas fa-trash"></i>         <!-- Supprimer -->
<i class="fas fa-eye"></i>           <!-- Voir -->
<i class="fas fa-download"></i>      <!-- Télécharger -->
<i class="fas fa-upload"></i>        <!-- Uploader -->

<!-- Icônes d'état -->
<i class="fas fa-check"></i>         <!-- Succès -->
<i class="fas fa-times"></i>         <!-- Erreur -->
<i class="fas fa-exclamation-triangle"></i> <!-- Attention -->
<i class="fas fa-info-circle"></i>   <!-- Information -->
```

### Tailles d'Icônes
```html
<i class="fas fa-home fa-xs"></i>    <!-- Extra small -->
<i class="fas fa-home fa-sm"></i>    <!-- Small -->
<i class="fas fa-home"></i>          <!-- Normal -->
<i class="fas fa-home fa-lg"></i>    <!-- Large -->
<i class="fas fa-home fa-2x"></i>    <!-- 2x -->
<i class="fas fa-home fa-3x"></i>    <!-- 3x -->
```

---

## 🎨 Thème Sombre (Dark Mode)

### Variables Dark Mode
```css
[data-theme="dark"] {
  --primary: #4e73df;
  --secondary: #858796;
  --success: #1cc88a;
  --info: #36b9cc;
  --warning: #f6c23e;
  --danger: #e74a3b;
  
  /* Arrière-plans */
  --body-bg: #1a1a1a;
  --card-bg: #2d2d2d;
  --sidebar-bg: #212529;
  --topbar-bg: #343a40;
  
  /* Textes */
  --text-primary: #ffffff;
  --text-secondary: #adb5bd;
  --text-muted: #6c757d;
}
```

### Toggle Dark Mode
```javascript
function toggleDarkMode() {
  const body = document.body;
  const isDark = body.getAttribute('data-theme') === 'dark';
  
  body.setAttribute('data-theme', isDark ? 'light' : 'dark');
  localStorage.setItem('theme', isDark ? 'light' : 'dark');
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
  const savedTheme = localStorage.getItem('theme') || 'light';
  document.body.setAttribute('data-theme', savedTheme);
});
```

---

## 📏 Grille et Layout

### Container
```html
<!-- Container fluide (pleine largeur) -->
<div class="container-fluid">
  <!-- Contenu -->
</div>

<!-- Container fixe -->
<div class="container">
  <!-- Contenu -->
</div>
```

### Système de Grille
```html
<div class="row">
  <div class="col-12 col-md-8">Contenu principal</div>
  <div class="col-12 col-md-4">Sidebar</div>
</div>

<div class="row">
  <div class="col-6 col-md-3">1/4</div>
  <div class="col-6 col-md-3">1/4</div>
  <div class="col-6 col-md-3">1/4</div>
  <div class="col-6 col-md-3">1/4</div>
</div>
```

---

## ✅ Bonnes Pratiques

### Performance
- Utiliser les classes utilitaires Bootstrap plutôt que du CSS custom
- Minimiser les fichiers CSS/JS en production
- Optimiser les images (WebP, lazy loading)
- Utiliser le cache browser

### Accessibilité
- Attributs `aria-*` sur les éléments interactifs
- Contraste suffisant (4.5:1 minimum)
- Navigation au clavier
- Labels descriptifs

### SEO
- Hiérarchie de titres logique (H1 → H6)
- Meta descriptions
- Alt text sur les images
- URLs descriptives

### Code
- Nommage consistant des classes
- Commentaires dans le CSS/JS
- Validation HTML/CSS
- Tests sur différents navigateurs

---

## 🔍 Exemples d'Utilisation

### Page de Liste
```html
@extends('layouts.sb-admin')

@section('content')
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Articles</h1>
    <a href="#" class="btn btn-primary">
      <i class="fas fa-plus fa-sm text-white-50"></i> Nouvel Article
    </a>
  </div>

  <!-- DataTable -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Liste des Articles</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable">
          <!-- Table content -->
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
```

### Formulaire
```html
@extends('layouts.sb-admin')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-xl-6 col-lg-8 col-md-9">
      <div class="card o-hidden border-0 shadow-lg">
        <div class="card-body p-0">
          <div class="p-5">
            <div class="text-center">
              <h1 class="h4 text-gray-900 mb-4">Créer un Article</h1>
            </div>
            <form class="user">
              <div class="form-group">
                <input type="text" class="form-control form-control-user" 
                       placeholder="Nom de l'article">
              </div>
              <div class="form-group row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                  <input type="number" class="form-control form-control-user" 
                         placeholder="Prix">
                </div>
                <div class="col-sm-6">
                  <input type="number" class="form-control form-control-user" 
                         placeholder="Stock">
                </div>
              </div>
              <button type="submit" class="btn btn-primary btn-user btn-block">
                Créer l'Article
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
```

---

**Version:** 1.0.0  
**Dernière mise à jour:** 12 juillet 2025  
**Équipe:** AccessPos Pro Development Team
