# Bibliothèque Municipale

Application web de gestion d'une bibliothèque municipale, développée dans un cadre pédagogique.

## Fonctionnalités

- Gestion des ouvrages (consultation, ajout, modification, suppression)
- Gestion des usagers
- Suivi des emprunts et des retours
- Interface d'administration sécurisée avec gestion des rôles et des permissions

## Stack technique

- **Backend** : PHP 8.x – Laravel 10
- **Frontend** : Vue 3, Vite, PrimeVue, Tailwind CSS
- **Base de données** : MySQL / MariaDB
- **Authentification** : Laravel Sanctum
- **Conteneurisation** : Docker

## Prérequis

- Docker et Docker Compose
- Node.js 20+
- Composer

## Installation

```bash
# Cloner le dépôt
git clone <url-du-repo>
cd bibliotheque-municipale

# Copier le fichier d'environnement
cp .env.example .env

# Démarrer les conteneurs Docker
docker-compose up -d

# Installer les dépendances PHP
composer install

# Générer la clé applicative
php artisan key:generate

# Exécuter les migrations
php artisan migrate

# Installer les dépendances JS et compiler les assets
npm install
npm run dev
```

## Structure des branches (Git Flow)

- `main` — version stable et livrable, aucune modification directe
- `dev` — branche d'intégration
- `feature/*` — nouvelles fonctionnalités (créées depuis `dev`)
- `hotfix/*` — correctifs critiques (créés depuis `main`)

## Conventions

Ce projet respecte la [charte de développement](charte-developpement.md) du projet.

## Licence

Projet fictif à but pédagogique.
