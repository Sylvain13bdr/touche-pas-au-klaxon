-- =====================================================================
--  Touche pas au klaxon — Script de création de la base de données
--  SGBD : MariaDB 10.4+ / MySQL 8+
--
--  Ce script crée la base et ses trois tables :
--    - agence       : les villes (agences) entre lesquelles on circule
--    - utilisateur  : les employés (importés du SI RH) et l'administrateur
--    - trajet       : les trajets de covoiturage proposés
--
--  Les contraintes d'intégrité (clés étrangères + CHECK) garantissent
--  la cohérence des données au plus près du stockage, en complément de
--  la validation applicative (PHP).
-- =====================================================================

CREATE DATABASE IF NOT EXISTS touche_pas_au_klaxon
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE touche_pas_au_klaxon;

-- On supprime les tables dans l'ordre inverse des dépendances pour
-- permettre de rejouer le script sans erreur.
DROP TABLE IF EXISTS trajet;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS agence;

-- ---------------------------------------------------------------------
--  Table : agence (villes)
--  Seul l'administrateur peut gérer cette liste.
-- ---------------------------------------------------------------------
CREATE TABLE agence (
    id  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_agence_nom (nom)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Table : utilisateur
--  role = 'employe' (par défaut) ou 'admin'.
--  Les employés proviennent du SI RH : l'application ne crée pas,
--  ne modifie pas et ne supprime pas d'employés.
-- ---------------------------------------------------------------------
CREATE TABLE utilisateur (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom          VARCHAR(100) NOT NULL,
    prenom       VARCHAR(100) NOT NULL,
    telephone    VARCHAR(20)  NOT NULL,
    email        VARCHAR(180) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,                 -- haché (password_hash / bcrypt)
    role         ENUM('employe', 'admin') NOT NULL DEFAULT 'employe',
    PRIMARY KEY (id),
    UNIQUE KEY uq_utilisateur_email (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Table : trajet
--  Un trajet relie deux agences distinctes, à des dates cohérentes,
--  et est proposé par un utilisateur (la personne à contacter).
-- ---------------------------------------------------------------------
CREATE TABLE trajet (
    id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    agence_depart_id   INT UNSIGNED NOT NULL,
    agence_arrivee_id  INT UNSIGNED NOT NULL,
    date_heure_depart  DATETIME     NOT NULL,
    date_heure_arrivee DATETIME     NOT NULL,
    places_totales     TINYINT UNSIGNED NOT NULL,
    places_disponibles TINYINT UNSIGNED NOT NULL,
    utilisateur_id     INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY idx_trajet_depart (date_heure_depart),
    KEY idx_trajet_auteur (utilisateur_id),
    CONSTRAINT fk_trajet_agence_depart
        FOREIGN KEY (agence_depart_id)  REFERENCES agence (id),
    CONSTRAINT fk_trajet_agence_arrivee
        FOREIGN KEY (agence_arrivee_id) REFERENCES agence (id),
    CONSTRAINT fk_trajet_auteur
        FOREIGN KEY (utilisateur_id)    REFERENCES utilisateur (id),
    -- Contrôles de cohérence demandés par le cahier des charges :
    CONSTRAINT chk_agences_differentes CHECK (agence_depart_id <> agence_arrivee_id),
    CONSTRAINT chk_dates_coherentes    CHECK (date_heure_arrivee > date_heure_depart),
    CONSTRAINT chk_places_coherentes   CHECK (places_disponibles <= places_totales)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
