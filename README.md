# 🎬 CRUD Movies avec Authentification

Ce projet est une application PHP simple permettant de gérer un CRUD (Create, Read, Update, Delete) sur une table de films, avec un système d’authentification basique.

---

## 🚀 Prérequis

- **PHP** 7.4 ou supérieur  
- **Composer**  
- **Serveur web** (Apache, Nginx, ou serveur PHP intégré)  
- **Base de données** MySQL  

---

## ⚙️ Installation

1. **Installer les dépendances avec Composer**
    ```bash
    composer install
    ```

2. **Configurer les variables d’environnement**  
    Créez un fichier `.env` à la racine du projet avec le contenu suivant :
    ```env
    HOST=localhost
    DBNAME=nom_de_la_base
    USERNAME=utilisateur
    PASSWORD=mot_de_passe
    ```

3. **Créer la table `movie` dans la base de données**  
    Exécutez la migration :
    ```bash
    php migrate_create_movie_table.php
    ```

4. **Démarrer un serveur local PHP (option rapide) :**
    ```bash
    php -S localhost:8000 -t public
    ```
    Puis accédez à [http://localhost:8000](http://localhost:8000)

    **Ou déployer sur un serveur Apache/Nginx :**  
    Copiez les fichiers du projet sur votre serveur web et configurez la racine sur le dossier approprié.

---

## 📚 API

L’API accessible via `/api` permet de gérer les films (`movie`) via des requêtes HTTP au format JSON.

### Endpoints

#### 1. `GET /api`

Retourne la liste de tous les films.

**Réponse :**
```json
[
  {
     "id": 1,
     "title": "Inception",
     "release_year": 2010,
     "genre": "Sci-Fi",
     "duration": 148
  },
  ...
]
```

---

#### 2. `GET /api.php?id={id}`

Retourne un film par son ID.

**Réponse :**
```json
{
  "id": 3,
  "title": "The Dark Knight",
  "release_year": 2008,
  "genre": "Action",
  "duration": 152
}
```

---

#### 3. `POST /api.php`

Crée un nouveau film.

**Requête :**
```http
POST /api.php
Content-Type: application/json

{
  "title": "Interstellar",
  "release_year": 2014,
  "genre": "Sci-Fi",
  "duration": 169
}
```

**Réponse :**
```json
{
  "success": true,
  "id": 5
}
```

---

#### 4. `PUT /api.php?id={id}`

Met à jour un film existant.

**Requête :**
```http
PUT /api.php?id=5
Content-Type: application/json

{
  "title": "Interstellar",
  "release_year": 2014,
  "genre": "Science Fiction",
  "duration": 170
}
```

**Réponse :**
```json
{
  "success": true
}
```

---

#### 5. `DELETE /api.php?id={id}`

Supprime un film.

**Requête :**
```http
DELETE /api.php?id=5
```

**Réponse :**
```json
{
  "success": true
}
```

---

## ✨ Fonctionnalités

- Authentification simple avec un utilisateur fixe (`admin` / `password123`)
- Liste des films existants
- Ajout, modification et suppression de films
- Protection des opérations CRUD (accessible uniquement après connexion)

---

## 💡 Remarques

- Pour modifier les identifiants de connexion, éditez les variables **`validUsername`** et **`validPassword`** dans le fichier principal.
- Le projet utilise **PDO** pour la gestion sécurisée des accès à la base de données.
- Le projet attend une base **MySQL** configurée avec les bonnes permissions.

