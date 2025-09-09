# Micro API F3
Mini API REST construite avec le framework [Fat-Free (F3)](https://fatfreeframework.com/), utilisant SQLite et conteneurisée avec Docker. Le projet inclut une authentification JWT et un CRUD de notes.

## Prérequis

* Docker & Docker Compose
* `make` (pour utiliser le Makefile fourni)
* `curl` et `jq` pour tester facilement l’API

## Installation & initialisation

1. **Cloner le dépôt** :

```bash
git clone https://github.com/benoit-bremaud/micro-api-f3.git
cd micro-api-f3
```

2. **Construire et démarrer les conteneurs** :

```bash
make up-build
```

3. **Installer les dépendances PHP et exécuter la migration** :

```bash
make install
make migrate
```

👉 Après ces étapes, l’API est disponible sur [http://localhost:8000](http://localhost:8000).

## Endpoints disponibles

* `GET /` → `{ok:true}`
* `POST /auth/register` → inscription (email, password)
* `POST /auth/login` → connexion, renvoie un token JWT
* `GET /api/v1/notes` (auth)
* `POST /api/v1/notes` (title, content)
* `GET /api/v1/notes/{id}`
* `PUT /api/v1/notes/{id}`
* `DELETE /api/v1/notes/{id}`

## Batteries de tests (copier/coller)

```bash
# Register
curl -s -X POST http://localhost:8000/auth/register \
  -H 'Content-Type: application/json' \
  -d '{"email":"alice@example.com","password":"secret123"}' | jq

# Login
TOKEN=$(curl -s -X POST http://localhost:8000/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"alice@example.com","password":"secret123"}' | jq -r .token)
echo $TOKEN

# Create note
curl -s -X POST http://localhost:8000/api/v1/notes \
  -H "Authorization: Bearer $TOKEN" -H 'Content-Type: application/json' \
  -d '{"title":"Todo","content":"Acheter du café"}' | jq

# List notes
curl -s -H "Authorization: Bearer $TOKEN" http://localhost:8000/api/v1/notes | jq
```

## Commandes Make disponibles

* `make up` → Démarrer les conteneurs
* `make up-build` → Reconstruire les images et démarrer les conteneurs
* `make down` → Stopper et supprimer les conteneurs
* `make restart` → Redémarrer les conteneurs
* `make ps` → Afficher l’état des conteneurs
* `make logs` → Afficher les logs
* `make shell` → Ouvrir un shell dans le conteneur PHP
* `make install` → Installer les dépendances Composer
* `make migrate` → Exécuter les migrations SQLite
* `make perms` → Créer les dossiers `data/` et `tmp/` avec les bonnes permissions

## Dépannage

* `Class 'Base' not found` → `make dump-autoload` (ou relancer `make install`)
* `no such table: users` → `make migrate`
* `fatfree-core not found` → `make install`
