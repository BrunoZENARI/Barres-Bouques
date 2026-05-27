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

> Node.js, Composer et PHP sont embarqués dans l'image Docker du projet — aucune installation locale n'est requise.

## Installation

Toutes les commandes PHP/Node se lancent **à l'intérieur du conteneur `WEB`** via `docker exec -it WEB bash`.

### 1. Cloner le dépôt

```bash
git clone <url-du-repo>
cd Barres-Bouques
```

### 2. Créer le fichier d'environnement

```bash
cp .env.example .env
```

### 3. Créer le réseau Docker

Le réseau est déclaré comme externe dans `docker-compose.yml` — il doit exister avant de démarrer les conteneurs.

```bash
docker network create app-network
```

> Si le réseau existe déjà, Docker affiche une erreur ignorable.

### 4. Démarrer les conteneurs

```bash
docker compose up -d
```

Cela démarre quatre conteneurs : `WEB` (PHP 8.2 + Apache + Node), `DB` (MariaDB), `MONGO` (MongoDB) et `REDIS`.

### 5. Installer les dépendances (dans le conteneur WEB)

```bash
docker exec -it WEB bash

# Dans le conteneur :
composer install
npm install
exit
```

### 6. Créer la base de données MySQL

Le conteneur `DB` crée automatiquement la base `laravel`, mais l'application utilise la base `bibliotheque`. Il faut la créer manuellement :

```bash
docker exec DB mariadb -u root -proot -e "
  CREATE DATABASE IF NOT EXISTS bibliotheque CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  GRANT ALL PRIVILEGES ON bibliotheque.* TO 'laravel'@'%';
  FLUSH PRIVILEGES;
"
```

### 7. Générer la clé applicative

```bash
docker exec WEB php artisan key:generate
```

### 8. Corriger les permissions sur le dossier storage

Apache tourne en tant qu'utilisateur `www-data` dans le conteneur. Le montage du volume depuis l'hôte peut créer des fichiers avec un propriétaire différent.

```bash
docker exec WEB chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker exec WEB chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
```

### 9. Exécuter les migrations et les seeders

```bash
docker exec WEB php artisan migrate
docker exec WEB php artisan db:seed
```

### 10. Lancer le serveur de développement Vite

```bash
docker exec -it WEB npm run dev
```

L'application est accessible sur [http://127.0.0.1](http://127.0.0.1).

## Structure des branches (Git Flow)

- `main` — version stable et livrable, aucune modification directe
- `dev` — branche d'intégration
- `feature/*` — nouvelles fonctionnalités (créées depuis `dev`)
- `hotfix/*` — correctifs critiques (créés depuis `main`)

## Conventions

Ce projet respecte la [charte de développement](charte-developpement.md) du projet.

## Licence

Projet fictif à but pédagogique.
