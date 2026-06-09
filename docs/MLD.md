# MLD — Touche pas au klaxon

**Modèle Logique des Données** (notation textuelle).

Légende : <u>clé primaire</u> soulignée, `#` clé étrangère.

```
agence (id, nom)

utilisateur (id, nom, prenom, telephone, email, mot_de_passe, role)

trajet (id,
        #agence_depart_id,
        #agence_arrivee_id,
        date_heure_depart,
        date_heure_arrivee,
        places_totales,
        places_disponibles,
        #utilisateur_id)
```

## Clés primaires
- `agence` : **id**
- `utilisateur` : **id**
- `trajet` : **id**

## Clés étrangères de `trajet`
- `agence_depart_id` → `agence(id)`
- `agence_arrivee_id` → `agence(id)`
- `utilisateur_id` → `utilisateur(id)`

## Règles de gestion traduites en contraintes
- Un trajet relie **deux agences différentes** (`agence_depart_id <> agence_arrivee_id`).
- La date d'arrivée est **postérieure** à la date de départ (`date_heure_arrivee > date_heure_depart`).
- Le nombre de places disponibles ne dépasse pas le nombre total (`places_disponibles <= places_totales`).
- `role` ∈ {`employe`, `admin`}.
