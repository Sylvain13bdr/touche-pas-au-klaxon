-- =====================================================================
--  Touche pas au klaxon — Jeu d'essai (alimentation de la base)
--  À exécuter APRÈS schema.sql.
--
--  Comptes de démonstration (mot de passe commun) : Klaxon2024!
--    - Administrateur : admin@touchepasauklaxon.fr
--    - Employé        : alexandre.martin@email.fr
--
--  Les dates des trajets sont relatives à NOW() afin que le jeu
--  d'essai reste pertinent quelle que soit la date d'exécution.
-- =====================================================================

USE touche_pas_au_klaxon;
SET NAMES utf8mb4;

-- Nettoyage (permet de rejouer ce script)
DELETE FROM trajet;
DELETE FROM utilisateur;
DELETE FROM agence;
ALTER TABLE trajet      AUTO_INCREMENT = 1;
ALTER TABLE utilisateur AUTO_INCREMENT = 1;
ALTER TABLE agence      AUTO_INCREMENT = 1;

-- --- Agences (villes) ---
INSERT INTO agence (id, nom) VALUES
    (1, 'Paris'),
    (2, 'Lyon'),
    (3, 'Marseille'),
    (4, 'Toulouse'),
    (5, 'Nice'),
    (6, 'Nantes'),
    (7, 'Strasbourg'),
    (8, 'Montpellier'),
    (9, 'Bordeaux'),
    (10, 'Lille'),
    (11, 'Rennes'),
    (12, 'Reims');

-- --- Utilisateurs (20 employés importés du SI RH + 1 administrateur) ---
INSERT INTO utilisateur (id, nom, prenom, telephone, email, mot_de_passe, role) VALUES
    (1, 'Martin', 'Alexandre', '0612345678', 'alexandre.martin@email.fr', '$2y$10$R9ei/UgLzJMHioIlaBH4xuk7TnHG2tarHnbIOBgSPI9t1bmxQ3kNK', 'employe'),
    (2, 'Dubois', 'Sophie', '0698765432', 'sophie.dubois@email.fr', '$2y$10$4URo2cgbTdo54QvGHpcLger75Gs1i8myZJs78rgK1xtJij/2zkolG', 'employe'),
    (3, 'Bernard', 'Julien', '0622446688', 'julien.bernard@email.fr', '$2y$10$tJxcIpZzTKYhsqpDmyNHWuXQkv9Xyb0Z80qh02cTcwY7Ez4FGcKWi', 'employe'),
    (4, 'Moreau', 'Camille', '0611223344', 'camille.moreau@email.fr', '$2y$10$MbOrCuoO5kPSctJID9JNyemrgeyutZK4xXy1ueiUdB9unVXiV4ASi', 'employe'),
    (5, 'Lefèvre', 'Lucie', '0777889900', 'lucie.lefevre@email.fr', '$2y$10$irAyHQHyFgtxv2fuxFSxWOodt1xQ1EiVEZCe4GipPRPcW8oUXmcR.', 'employe'),
    (6, 'Leroy', 'Thomas', '0655443322', 'thomas.leroy@email.fr', '$2y$10$hxmepfmIbu/Un73FBGNd2uw9DSsX1rv/WACubwOS0WXU3YfYlyyPS', 'employe'),
    (7, 'Roux', 'Chloé', '0633221199', 'chloe.roux@email.fr', '$2y$10$T/mrvttrds138ZSjPBgQMuSNCX1hJaxLMGDCFoYDXvU76eq3.g3I.', 'employe'),
    (8, 'Petit', 'Maxime', '0766778899', 'maxime.petit@email.fr', '$2y$10$bugj6rwI0Qi0wsSMcTAEcOOBr5ybDhBe7wRrk35oF4ht1ZhJz2yLi', 'employe'),
    (9, 'Garnier', 'Laura', '0688776655', 'laura.garnier@email.fr', '$2y$10$4uJlw3ih5U4PpsmjAD2I7OXk8ZLhaA4yZWUwA1M0RMiVFE085DUFu', 'employe'),
    (10, 'Dupuis', 'Antoine', '0744556677', 'antoine.dupuis@email.fr', '$2y$10$ayPsIS3z6X4dRNv7vMpdTuRyVNPfeUMEMl47qETm48ix2BaiHeYRO', 'employe'),
    (11, 'Lefebvre', 'Emma', '0699887766', 'emma.lefebvre@email.fr', '$2y$10$UquNt0Ck5fdzlUiqnrEtXeG9AVcPV34DZqb.DnZGSLW6bMt8W7Nyu', 'employe'),
    (12, 'Fontaine', 'Louis', '0655667788', 'louis.fontaine@email.fr', '$2y$10$XubbP49oXC1YVzgTitvgvOvo/ixh0yissdqV/NtVIytu6eMML3Wxa', 'employe'),
    (13, 'Chevalier', 'Clara', '0788990011', 'clara.chevalier@email.fr', '$2y$10$cJWNdchRDwJCkM8zyRFDnuPe589KwZ45hs9aKUbYFRG8Z1mpzPXdO', 'employe'),
    (14, 'Robin', 'Nicolas', '0644332211', 'nicolas.robin@email.fr', '$2y$10$UtlSaEoxOp1GqY8Bfgt39OmYrqI9OKRG4OAh1Z.4xsDFpqYOR5c3.', 'employe'),
    (15, 'Gauthier', 'Marine', '0677889922', 'marine.gauthier@email.fr', '$2y$10$1bP0Zq0nWFY6MBjENNCA5e3OqqJHpAMfBESnA0TSkqdR1qBLaIY3u', 'employe'),
    (16, 'Fournier', 'Pierre', '0722334455', 'pierre.fournier@email.fr', '$2y$10$VjGTNL6mB/qTc4xbkg0pUeSx8UxSxoMIggCPiFYzAW1alzn/A6piy', 'employe'),
    (17, 'Girard', 'Sarah', '0688665544', 'sarah.girard@email.fr', '$2y$10$MkPO1Y2FgeQiSHFkHLs72eHaz5L/S5Uth05jQZJFyRST7RrfKnfh6', 'employe'),
    (18, 'Lambert', 'Hugo', '0611223366', 'hugo.lambert@email.fr', '$2y$10$kPdWL2Ttvu536KWfIbwk/eEg8lEOXjDvglMLGiW7x1ilK0mXmFOqi', 'employe'),
    (19, 'Masson', 'Julie', '0733445566', 'julie.masson@email.fr', '$2y$10$4UPcmfOCPLo3Mu9PzACMfegbtV3r7rvCIP8CJDWKIYGQlu0Q4M/Q.', 'employe'),
    (20, 'Henry', 'Arthur', '0666554433', 'arthur.henry@email.fr', '$2y$10$5wn4N3VvtXUJbsGp00QtuOR4uXyy4QAebAstGyJ6dorE61d8apHfS', 'employe'),
    (21, 'Klaxon', 'Admin', '0600000000', 'admin@touchepasauklaxon.fr', '$2y$10$oNgRC7JQ2afjFM8IrDicY.j1rN320cOG28ApNSESrYYM7l530fmcO', 'admin');

-- --- Trajets de démonstration ---
INSERT INTO trajet (agence_depart_id, agence_arrivee_id, date_heure_depart, date_heure_arrivee, places_totales, places_disponibles, utilisateur_id) VALUES
    (1, 2, NOW() + INTERVAL 50 HOUR, NOW() + INTERVAL 54 HOUR, 4, 3, 1),
    (3, 5, NOW() + INTERVAL 74 HOUR, NOW() + INTERVAL 77 HOUR, 3, 2, 2),
    (9, 1, NOW() + INTERVAL 26 HOUR, NOW() + INTERVAL 32 HOUR, 4, 4, 3),
    (2, 8, NOW() + INTERVAL 98 HOUR, NOW() + INTERVAL 101 HOUR, 2, 1, 1),
    (10, 7, NOW() + INTERVAL 30 HOUR, NOW() + INTERVAL 35 HOUR, 5, 2, 4),
    (4, 9, NOW() + INTERVAL 55 HOUR, NOW() + INTERVAL 57 HOUR, 3, 3, 5),
    (6, 11, NOW() + INTERVAL 80 HOUR, NOW() + INTERVAL 84 HOUR, 4, 1, 6),
    (12, 1, NOW() + INTERVAL 120 HOUR, NOW() + INTERVAL 122 HOUR, 3, 2, 7),
    (1, 3, NOW() + INTERVAL 60 HOUR, NOW() + INTERVAL 68 HOUR, 4, 0, 8),
    (2, 1, NOW() + INTERVAL -120 HOUR, NOW() + INTERVAL -116 HOUR, 4, 2, 1),
    (5, 4, NOW() + INTERVAL -48 HOUR, NOW() + INTERVAL -45 HOUR, 3, 1, 9);
