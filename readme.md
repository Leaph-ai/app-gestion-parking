``` markdown
# 🚗 Projet Parking - Système de Gestion de Parking

Un système complet de gestion de parking développé en PHP avec une interface moderne et intuitive.

## 📋 Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Technologies utilisées](#-technologies-utilisées)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Structure du projet](#-structure-du-projet)
- [Utilisation](#-utilisation)
- [API](#-api)
- [Contribuer](#-contribuer)

## ✨ Fonctionnalités

### 👥 Gestion des utilisateurs
- ✅ Inscription et connexion sécurisées
- ✅ Profils utilisateur avec validation
- ✅ Système de rôles (Admin/Utilisateur)
- ✅ Gestion des permissions

### 🚙 Gestion des places de parking
- ✅ Types de places : Normale, Handicapée, Staff
- ✅ Statut en temps réel (Libre/Occupée)
- ✅ Interface d'administration intuitive

### 📅 Système de réservation
- ✅ Réservation en temps réel avec validation de disponibilité
- ✅ Durée flexible : 15 minutes à 30 jours
- ✅ Calcul automatique des prix
- ✅ Paiement intégré PayPal
- ✅ Annulation de réservation

### 💰 Tarification dynamique
- ✅ Règles de prix par type de place
- ✅ Tarification par plages horaires
- ✅ Jours de la semaine configurables
- ✅ Durée minimale paramétrable

### 📊 Statistiques et rapports
- ✅ Dashboard administrateur
- ✅ Statistiques de réservation
- ✅ Chiffre d'affaires
- ✅ Taux d'occupation

## 🛠️ Technologies utilisées

### Backend
- **PHP 8.2+** - Langage principal
- **PDO** - Accès base de données sécurisé
- **MySQL 8.3** - Base de données

### Frontend
- **HTML5/CSS3** - Structure et style
- **JavaScript ES6** - Interactions dynamiques
- **Font Awesome** - Icônes
- **PayPal SDK** - Paiements

### Outils
- **Composer** - Gestionnaire de dépendances
- **Git** - Contrôle de version

## 📋 Prérequis

- **PHP 8.2 ou supérieur**
- **MySQL 8.0 ou supérieur**
- **Composer** (gestionnaire de dépendances PHP)
- **Serveur web** (Apache/Nginx)
- **Compte PayPal Developer** (pour les paiements)

## 🚀 Installation

### 1. Cloner le projet
```
bash git clone [https://github.com/Leaph-ai/projet-parking.git](https://github.com/Leaph-ai/projet-parking.git) cd projet-parking
``` 

### 2. Installer les dépendances
```
bash composer install
``` 

### 3. Configuration de la base de données

#### Créer la base de données
```
sql CREATE DATABASE projet_parking CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
``` 

#### Importer la structure
```
bash mysql -u votre_user -p projet_parking < database.sql
``` 

### 4. Configuration de l'environnement

#### Copier le fichier de configuration
```
bash cp .env.dist .env
``` 

#### Éditer le fichier `.env`
```
bash nano .env
``` 

## ⚙️ Configuration

### Fichier `.env`
```
env
# Configuration de la base de données
DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=projet_parking DB_USER=votre_utilisateur DB_PASSWORD=votre_mot_de_passe
# Configuration PayPal
PAYPAL_CLIENT_ID=votre_client_id_paypal PAYPAL_CLIENT_SECRET=votre_client_secret_paypal PAYPAL_MODE=sandbox
# Configuration générale
APP_ENV=development APP_DEBUG=true
``` 

### Configuration du serveur web

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```
```
#### Nginx
``` nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```
## 📁 Structure du projet
``` 
projet-parking/
├── _partials/              # Templates partiels
│   ├── errors.php
│   ├── footer.php
│   └── navbar.php
├── assets/                 # Ressources statiques
│   ├── CSS/
│   │   └── style.css
│   ├── images/
│   └── javascript/
│       ├── components/     # Composants UI
│       └── services/       # Services API
├── Controller/             # Contrôleurs
├── Includes/              # Fichiers utilitaires
│   ├── database.php
│   └── functions.php
├── Model/                 # Modèles de données
├── View/                  # Vues/Templates
├── vendor/               # Dépendances Composer
├── .env                  # Configuration (non versionné)
├── .env.dist            # Template de configuration
├── composer.json        # Dépendances PHP
├── database.sql         # Structure de la base
├── index.php           # Point d'entrée
└── readme.md          # Documentation
```
## 🎯 Utilisation
### Première connexion
1. **Créer un compte administrateur** (directement en base ou via l'interface)
2. **Configurer les places de parking** dans le panel admin
3. **Définir les règles de tarification**
4. **Tester une réservation**

### Interface utilisateur
#### Tableau de bord
- Vue d'ensemble des réservations
- Statistiques personnelles
- Actions rapides

#### Réservation
1. Sélectionner les dates/heures
2. Choisir une place disponible
3. Confirmer le prix
4. Effectuer le paiement
5. Recevoir la confirmation

### Interface administrateur
#### Gestion des places
- Créer/modifier/supprimer des places
- Changer les statuts (libre/occupée)
- Configurer les types de places

#### Tarification
- Définir des règles de prix flexibles
- Gérer les plages horaires
- Configurer par jour de la semaine

#### Utilisateurs
- Voir tous les utilisateurs
- Gérer les rôles et permissions
- Statistiques d'utilisation

## 🔌 API
### Endpoints principaux
#### Réservations
``` javascript
// Vérifier disponibilité
GET /index.php?component=booking&action=check_availability&spot_id=1&start_time=...&end_time=...

// Calculer le prix
GET /index.php?component=booking&action=calculate&spot_id=1&start_time=...&end_time=...

// Créer une réservation
POST /index.php?component=booking
{
    "action": "create",
    "spot_id": 1,
    "start_time": "2025-06-21T09:00",
    "end_time": "2025-06-21T17:00",
    "payment_id": "paypal_payment_id"
}

// Annuler une réservation
POST /index.php?component=booking
{
    "action": "cancel",
    "id": 123
}
```
#### Places disponibles
``` javascript
// Rechercher places disponibles
GET /index.php?component=booking&action=get_available_spots_for_period&start_time=...&end_time=...
```
### Réponses API
``` json
{
    "success": true,
    "message": "Opération réussie",
    "data": {...}
}
```
## 📊 Base de données
### Tables principales
#### `users` - Utilisateurs
- Informations personnelles
- Authentification sécurisée
- Système de rôles

#### - Places de parking `parking_spots`
- Numérotation et types
- Statut d'occupation

#### - Réservations `bookings`
- Périodes de réservation
- Prix et statuts
- Liens utilisateur/place

#### `pricing` - Tarification
- Règles de prix flexibles
- Plages horaires
- Jours de la semaine

## 🤝 Contribuer
1. **Fork** le projet
2. Créer une **branche feature** (`git checkout -b feature/AmazingFeature`)
3. **Commit** vos changements (`git commit -m 'Add AmazingFeature'`)
4. **Push** vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une **Pull Request**

## 📝 Licence
Ce projet est sous licence MIT. Voir le fichier pour plus de détails. `LICENSE`
## 👨‍💻 Auteur
Boudegna Philippe
- GitHub: [@Leaph-ai](https://github.com/Leaph-ai)

## 🙏 Remerciements
- PayPal pour l'API de paiement
- Font Awesome pour les icônes
- La communauté PHP pour les bonnes pratiques

**Version:** 1.0.0
**Dernière mise à jour:** Juin 2025
``` 

<snippet-file>database.sql</snippet-file>
```sql
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 20 juin 2025 à 21:34
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projet_parking`
--

-- --------------------------------------------------------

--
-- Structure de la table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `spot_id` int DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_cancelled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parking_spots`
--

DROP TABLE IF EXISTS `parking_spots`;
CREATE TABLE IF NOT EXISTS `parking_spots` (
  `id` int NOT NULL AUTO_INCREMENT,
  `number` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  `is_occupied` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pricing`
--

DROP TABLE IF EXISTS `pricing`;
CREATE TABLE IF NOT EXISTS `pricing` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(100) DEFAULT NULL,
  `spot_type` int DEFAULT NULL,
  `start_hour` time DEFAULT NULL,
  `end_hour` time DEFAULT NULL,
  `days` set('mon','tue','wed','thu','fri','sat','sun') DEFAULT NULL,
  `price_per_hour` decimal(6,2) DEFAULT NULL,
  `min_duration_minutes` int DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(25) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` int DEFAULT '1',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
