# Touche pas au klaxon

Application web de **covoiturage inter-sites** destinée à l'intranet d'une
entreprise multi-implantations. Elle permet aux employés de diffuser et de
consulter les trajets prévus entre les différentes agences (villes) afin de
favoriser le covoiturage.

Projet réalisé dans le cadre du devoir **« Mise en place d'une application MVC en PHP »**.

---

## Fonctionnalités

- **Visiteur (non connecté)** : consultation de la liste des trajets à venir
  disposant de places, triés par date de départ croissante.
- **Employé (connecté)** :
  - détails d'un trajet dans une fenêtre modale (auteur, téléphone, email, places totales) ;
  - création de trajets (avec contrôles de cohérence) ;
  - modification et suppression de **ses propres** trajets uniquement.
- **Administrateur** :
  - consultation des utilisateurs ;
  - gestion complète des agences (création, modification, suppression) ;
  - consultation et suppression de tous les trajets.

Après chaque écriture en base, l'utilisateur est redirigé vers la liste
concernée avec un **message de confirmation** (message flash).

---

## Stack technique

| Composant | Choix |
|---|---|
| Langage | PHP 8.1+ (orienté objet, architecture **MVC**) |
| Base de données | MySQL 8 / **MariaDB 10.4+** |
| Routeur | [izniburak/router](https://packagist.org/packages/izniburak/router) |
| Front | **Bootstrap 5** compilé via **Sass** (variables surchargées par la palette imposée) |
| Tests | **PHPUnit** |
| Analyse statique | **PHPStan** (niveau 6) |
| Documentation du code | **DocBlock** |

---

## Prérequis

- PHP **8.1 ou supérieur** avec les extensions `pdo_mysql`, `mbstring`, `openssl`, `zip` ;
- [Composer](https://getcomposer.org/) ;
- un serveur **MySQL/MariaDB** (par exemple via [XAMPP](https://www.apachefriends.org/)) ;
- (facultatif) [Node.js](https://nodejs.org/) + [Dart Sass](https://sass-lang.com/) pour recompiler le CSS.

---

## Installation

```bash
# 1. Récupérer le projet
git clone https://github.com/Sylvain13bdr/touche-pas-au-klaxon.git
cd touche-pas-au-klaxon

# 2. Installer les dépendances PHP
composer install

# 3. Créer et alimenter la base de données
#    (via la console mysql ou phpMyAdmin)
mysql -u root < database/schema.sql
mysql -u root < database/seed.sql
```

### Configuration de la connexion

Par défaut, l'application se connecte à MariaDB sur `127.0.0.1:3306`, base
`touche_pas_au_klaxon`, utilisateur `root` **sans mot de passe** (réglages
XAMPP standard).

Pour adapter ces valeurs **sans modifier de fichier versionné**, créez un
fichier `config/config.local.php` qui retourne un tableau partiel ; il est
fusionné par-dessus la configuration par défaut :

```php
<?php
return [
    'db' => [
        'user' => 'mon_utilisateur',
        'pass' => 'mon_mot_de_passe',
    ],
];
```

---

## Lancement

```bash
php -S localhost:4000 -t public public/index.php
```

Puis ouvrir **http://localhost:4000** dans le navigateur.

> Sous XAMPP, si `php` n'est pas dans le PATH, utilisez le chemin complet,
> par exemple `C:\xampp\php\php.exe -S localhost:4000 -t public public/index.php`.

---

## Comptes de démonstration

Le jeu d'essai crée 20 employés et 1 administrateur. **Tous** partagent le même
mot de passe : `Klaxon2024!`

| Rôle | Email | Mot de passe |
|---|---|---|
| **Administrateur** | `admin@touchepasauklaxon.fr` | `Klaxon2024!` |
| **Employé** | `alexandre.martin@email.fr` | `Klaxon2024!` |

---

## Qualité et tests

```bash
composer test    # exécute la suite PHPUnit
composer stan    # lance l'analyse statique PHPStan (niveau 6)
```

## Recompilation du CSS (facultatif)

Le CSS compilé est versionné. Pour le régénérer après modification des sources
Sass (`scss/app.scss`) :

```bash
npm install
npm run css
```

---

## Architecture du projet

```
touche-pas-au-klaxon/
├── public/            # Racine web : front controller (index.php) + assets
├── src/
│   ├── Controllers/   # Contrôleurs (Home, Auth, Trajet, Admin)
│   ├── Core/          # Socle : Database, Session, Auth, View, Controller
│   ├── Models/        # Entités : Agence, Utilisateur, Trajet
│   ├── Repositories/  # Accès aux données (requêtes préparées)
│   ├── Services/      # Logique métier et validation
│   └── Views/         # Templates (layouts, home, auth, trajets, admin)
├── config/            # Configuration de l'application
├── database/          # schema.sql (création) + seed.sql (jeu d'essai)
├── docs/              # MCD (image) et MLD (texte)
├── scss/              # Sources Sass
└── tests/             # Tests unitaires PHPUnit
```

Le **MCD** est disponible dans [`docs/MCD.png`](docs/MCD.png) et le **MLD** dans
[`docs/MLD.md`](docs/MLD.md).

---

## Sécurité

- Accès aux données via **requêtes préparées** (protection contre les injections SQL) ;
- mots de passe **hachés** (`password_hash` / bcrypt) ;
- **échappement systématique** des sorties HTML (protection contre le XSS) ;
- contrôle des **permissions côté serveur** (un employé ne gère que ses trajets ;
  l'espace d'administration est réservé au rôle `admin`) ;
- **validation systématique** des entrées dans la couche service.
