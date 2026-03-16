# Charte de développement

**Projet : Application web pour une bibliothèque municipale**

## 1. Objectifs de la charte

Cette charte de développement définit les règles, méthodes et bonnes pratiques à respecter tout au long du cycle de vie du projet.

Elle a pour objectifs de :
- Garantir un code lisible, cohérent et maintenable
- Faciliter le travail collaboratif
- Assurer la traçabilité des évolutions
- Réduire les risques d'erreurs lors des fusions et livraisons
- Appliquer des standards proches du monde professionnel

## 2. Présentation du projet

Le projet consiste à développer une application web destinée à une bibliothèque municipale.

L'application permettra notamment :
- La gestion des ouvrages (consultation, ajout, modification, suppression)
- La gestion des usagers
- Le suivi des emprunts et des retours
- Une interface d'administration sécurisée

Le projet est fictif et réalisé dans un cadre pédagogique.

## 3. Technologies utilisées

- **Backend** : PHP 8.x – Framework Laravel
- **Frontend** : VueJS, HTML, CSS, JavaScript
- **Base de données** : MySQL ou MariaDB
- **Gestion de versions** : Git
- **Plateforme de versioning** : GitHub
- **Environnement de développement** : Local (Laragon, XAMPP, Docker ou équivalent)
- **Accès et sécurité** : KeePass (Mot de passe disponible auprès du Lead Developer)
- **Linter** : Laravel Pint

## 4. Normes et standards

Le projet respecte les standards suivants :
- PSR-12 pour le style de code PHP
- Architecture MVC imposée par Laravel
- Bonnes pratiques officielles Laravel

Tout le code PHP doit être conforme à PSR-12 :
- Indentation en 4 espaces
- Accolades sur leur propre ligne
- Une classe par fichier
- Nommage cohérent des espaces de noms

## 5. Organisation du dépôt GitHub

Le dépôt GitHub contient :
- Le code source de l'application
- Un fichier `README.md`
- Un fichier `.env.example`
- La présente charte de développement
- Éventuellement des documents de conception (diagrammes, documentation)

> Le fichier `.env` ne doit jamais être versionné.

## 6. Stratégie de branches (Git Flow)

### 6.1 Branche `main`
- Contient la version stable et livrable
- Aucune modification directe n'est autorisée

### 6.2 Branche `dev`
- Branche d'intégration
- Regroupe toutes les fonctionnalités validées

### 6.3 Branches de fonctionnalités
- Créées depuis `dev`
- Une branche par fonctionnalité
- Nommage : `feature/nom-fonctionnalite`

### 6.4 Branches de correctifs
- Créées depuis `main`
- Réservées aux corrections critiques
- Nommage : `hotfix/description-courte`

## 7. Conventions de nommage (alignées PSR-12)

### 7.1 Conventions générales
- Langue : anglais
- Noms explicites et sans abréviation ambiguë
- Respect strict de la casse selon le type d'élément

### 7.2 Branches Git
- Minuscules, séparateur tiret (`-`)
- Exemples : `feature/book-management`, `hotfix/fix-return-date`

### 7.3 Commits
Format : `type: description courte à l'infinitif`

Types autorisés : `feat`, `fix`, `refactor`, `docs`, `style`, `test`

### 7.4 Classes PHP
- PascalCase, une responsabilité par classe
- Exemples : `BookController`, `LoanService`, `User`

### 7.5 Contrôleurs
- Suffixe `Controller`, nom au singulier
- Exemples : `BookController`, `LoanController`

### 7.6 Modèles Eloquent
- Singulier, PascalCase
- Exemples : `Book` → table `books`, `Loan` → table `loans`

### 7.7 Tables de base de données
- Pluriel, minuscules, `snake_case`
- Exemples : `books`, `book_loans`

### 7.8 Colonnes de base de données
- `snake_case`, clés étrangères : `model_id`
- Exemples : `published_at`, `user_id`

### 7.9 Migrations
- Générées via Artisan, nom non modifié
- Exemple : `2026_01_15_143210_create_books_table.php`

### 7.10 Routes
- URL en kebab-case, routes nommées au format Laravel standard
- Exemples : `/books/{id}`, `books.index`

### 7.11 Variables
- camelCase
- Exemples : `$loanDate`, `$returnDate`, `$currentUser`

### 7.12 Méthodes et fonctions
- camelCase, verbe + complément
- Exemples : `createLoan()`, `calculateReturnDate()`

### 7.13 Vues VueJS
- kebab-case, organisation par ressource
- Exemple : `resources/js/components/home.vue`

### 7.14 Fichiers CSS et JavaScript
- kebab-case
- Exemples : `book-list.css`, `loan-form.js`

## 8. Qualité du code

- Respect strict de PSR-12
- Code clair, lisible et commenté si nécessaire
- Suppression du code mort
- Séparation des responsabilités

## 9. Tests et validation

- Tests locaux obligatoires avant toute fusion
- Vérification fonctionnelle et technique
- Tests unitaires ou fonctionnels lorsque pertinent

## 10. Gestion des fusions

- Fusions via Pull Requests GitHub
- Description claire obligatoire
- Aucune fusion directe sur `main`

## 11. Sécurité

- `.env` non versionné
- Aucune donnée sensible dans le dépôt
- Accès administrateur protégé par authentification

## 12. Évolution de la charte

Cette charte peut évoluer selon les contraintes pédagogiques ou techniques. Toute modification doit être validée par l'équipe projet.
