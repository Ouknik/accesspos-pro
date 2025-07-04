<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# AccessPOS Pro - Système de Point de Vente Avancé

## 📋 Description

AccessPOS Pro est une solution complète de point de vente (POS) développée avec Laravel, conçue pour les commerces de détail, restaurants et établissements de service. Le système offre une interface moderne et intuitive avec des fonctionnalités avancées de gestion et de reporting.

## ✨ Fonctionnalités Principales

### 🎯 Tableau de Bord Moderne
- Interface responsive et interactive
- Statistiques en temps réel
- Indicateurs de performance clés (KPI)
- Alertes automatiques pour la gestion proactive

### 📊 Système de Rapports Avancé
- **Rapports des Ventes** : Analyse du chiffre d'affaires, tickets moyens, tendances
- **Rapports de Stock** : État des inventaires, articles en rupture, valorisation
- **Rapports Clients** : Analyse de la clientèle, programme de fidélité
- **Rapports Financiers** : Analyse financière complète et rentabilité
- **Rapports Restaurant** : Gestion des tables, réservations et services

### 📈 Formats d'Export Multiples
- Affichage web interactif
- Export PDF formaté et imprimable
- Export Excel pour analyses approfondies
- Export CSV pour intégration de données

### 🔧 Gestion Adaptative des Données
- Détection automatique des structures de base de données
- Adaptation dynamique aux noms de colonnes existants
- Support de différents schémas de données
- Gestion intelligente des erreurs

### 🎨 Interface Utilisateur
- Design moderne et professionnel
- Interface responsive (mobile, tablette, desktop)
- Thème sombre/clair
- Navigation intuitive

## 🛠️ Technologies Utilisées

- **Backend** : Laravel 10.x
- **Frontend** : Bootstrap 5.3, JavaScript ES6+
- **Base de Données** : MySQL/MariaDB
- **Exports** : Laravel Excel, DomPDF
- **Styles** : CSS3 avec variables personnalisées
- **Icons** : Font Awesome 6.4

## 📦 Installation

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- MySQL 5.7+ ou MariaDB 10.3+
- Node.js et NPM (optionnel)

### Étapes d'Installation

1. **Cloner le projet**
```bash
git clone https://github.com/votre-username/accesspos-pro.git
cd accesspos-pro
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configuration de l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configuration de la base de données**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=accesspos_pro
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Migration et données de test**
```bash
php artisan migrate
php artisan db:seed
```

6. **Installation des packages de reporting**
```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

7. **Publier les configurations**
```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

8. **Démarrer le serveur**
```bash
php artisan serve
```

## 🗂️ Structure du Projet

```
accesspos-pro/
├── app/
│   ├── Http/Controllers/Admin/
│   │   ├── ReportController.php      # Contrôleur des rapports
│   │   └── TableauDeBordController.php
│   └── Exports/
│       └── ReportExport.php          # Classes d'export
├── resources/views/admin/
│   ├── reports/
│   │   ├── index.blade.php           # Interface de génération
│   │   ├── rapport-ventes.blade.php  # Vue des ventes
│   │   ├── rapport-stock.blade.php   # Vue du stock
│   │   ├── rapport-clients.blade.php # Vue des clients
│   │   └── pdf/                      # Templates PDF
│   └── tableau-de-bord-moderne.blade.php
├── routes/
│   └── web.php                       # Routes système
└── public/
    ├── css/
    └── js/
```

## 🔧 Configuration

### Configuration des Rapports

Le système s'adapte automatiquement à votre structure de base de données existante. Les tables principales supportées :

- `FACTURE_VNT` : Factures de vente
- `ARTICLE` : Articles/Produits
- `CLIENT` : Base clients
- `TABLE_RESTAURANT` : Tables restaurant (optionnel)

### Personnalisation des Colonnes

Le système recherche automatiquement les colonnes dans cet ordre de priorité :

**Pour les ventes :**
- Date : `fctv_date`, `FCTV_DATE`, `DATE_FACTURE`, `created_at`
- Montant TTC : `fctv_mnt_ttc`, `FCTV_MNT_TTC`, `montant_ttc`, `total_ttc`
- Référence : `FCTV_REF`, `fctv_ref`, `REF_FACTURE`, `numero_facture`

**Pour le stock :**
- Code article : `ART_CODE`, `art_code`, `code_article`, `code`
- Désignation : `ART_DESIGNATION`, `art_designation`, `designation`, `nom`
- Stock : `ART_QTE_STOCK`, `art_qte_stock`, `quantite_stock`, `stock`

## 🚀 Utilisation

### Génération de Rapports

1. Accédez au tableau de bord administrateur
2. Cliquez sur "Rapports Détaillés"
3. Sélectionnez le type de rapport souhaité
4. Configurez la période d'analyse
5. Appliquez les filtres optionnels
6. Choisissez le format d'export
7. Générez le rapport

### Raccourcis Rapides

Depuis le tableau de bord, utilisez les raccourcis pour :
- Rapport des ventes du jour
- État du stock actuel
- Analyse de la clientèle
- Synthèse financière

## 📈 Fonctionnalités Avancées

### Alertes Automatiques
- Détection des ruptures de stock
- Alertes de performance des ventes
- Notifications en temps réel

### Suggestions Intelligentes
- Recommandations de rapports basées sur les données
- Détection des anomalies
- Conseils d'optimisation

### Historique des Rapports
- Accès aux rapports récemment générés
- Sauvegarde des configurations
- Réutilisation rapide des paramètres

## 🔒 Sécurité

- Authentification requise pour tous les rapports
- Contrôle d'accès par rôles
- Validation des données d'entrée
- Protection CSRF
- Logs d'audit des actions

## 🐛 Dépannage

### Problèmes Courants

**Erreur "Column not found"**
- Vérifiez que vos tables contiennent les données nécessaires
- Le système s'adapte automatiquement aux noms de colonnes

**Rapport vide**
- Assurez-vous que des données existent pour la période sélectionnée
- Vérifiez la configuration de la base de données

**Erreur d'export PDF/Excel**
- Vérifiez que les packages sont correctement installés
- Contrôlez les permissions d'écriture

### Logs
```bash
tail -f storage/logs/laravel.log
```

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet
2. Créez une branche feature (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Committez vos changements (`git commit -am 'Ajout de nouvelle fonctionnalité'`)
4. Pushez vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Ouvrez une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 📞 Support

Pour toute question ou support :

- **Email** : support@accesspos-pro.com
- **Documentation** : [Wiki du projet](https://github.com/votre-username/accesspos-pro/wiki)
- **Issues** : [GitHub Issues](https://github.com/votre-username/accesspos-pro/issues)

## 📊 Statistiques du Projet

![GitHub stars](https://img.shields.io/github/stars/votre-username/accesspos-pro)
![GitHub forks](https://img.shields.io/github/forks/votre-username/accesspos-pro)
![GitHub issues](https://img.shields.io/github/issues/votre-username/accesspos-pro)
![GitHub license](https://img.shields.io/github/license/votre-username/accesspos-pro)

## 🔮 Roadmap

- [ ] Module de comptabilité avancée
- [ ] Intégration e-commerce
- [ ] Application mobile native
- [ ] API REST complète
- [ ] Module de Business Intelligence
- [ ] Support multi-magasins
- [ ] Intégration avec systèmes de caisse

---

**AccessPOS Pro** - Solution POS moderne pour entreprises ambitieuses.

Développé avec ❤️ par l'équipe AccessPOS.
