# Micro API F3

Mini REST API built with the [Fat-Free (F3)](https://fatfreeframework.com/) framework, using SQLite and containerized with Docker.  
The project includes JWT authentication and a notes CRUD.

## Prerequisites

* Docker & Docker Compose
* `make` (to use the provided Makefile)
* `curl` and `jq` (for quick API testing)

## Installation & Setup

1. **Clone the repository**:

```bash
git clone https://github.com/benoit-bremaud/micro-api-f3.git
cd micro-api-f3
```

2. **Build and start containers**:

```bash
make up-build
```

3. **Install PHP dependencies and run the migration**:

```bash
make install
make migrate
```

👉 After these steps, the API will be available at [http://localhost:8000](http://localhost:8000).

## Available Endpoints

* `GET /` → `{ok:true}`
* `POST /auth/register` → user registration (email, password)
* `POST /auth/login` → login, returns a JWT token
* `GET /api/v1/notes` (auth required)
* `POST /api/v1/notes` (title, content)
* `GET /api/v1/notes/{id}`
* `PUT /api/v1/notes/{id}`
* `DELETE /api/v1/notes/{id}`

## Quick Test Commands (copy/paste)

```bash
# Register
curl -s -X POST http://localhost:8000/auth/register   -H 'Content-Type: application/json'   -d '{"email":"alice@example.com","password":"secret123"}' | jq

# Login
TOKEN=$(curl -s -X POST http://localhost:8000/auth/login   -H 'Content-Type: application/json'   -d '{"email":"alice@example.com","password":"secret123"}' | jq -r .token)
echo $TOKEN

# Create note
curl -s -X POST http://localhost:8000/api/v1/notes   -H "Authorization: Bearer $TOKEN" -H 'Content-Type: application/json'   -d '{"title":"Todo","content":"Buy coffee"}' | jq

# List notes
curl -s -H "Authorization: Bearer $TOKEN" http://localhost:8000/api/v1/notes | jq
```

## Make Commands

The project includes a **Makefile** to simplify Docker environment management.  

Most useful commands for daily work are:

* `make up` → Start services in detached mode  
* `make up-build` → Rebuild images and start services  
* `make down` → Stop and remove containers (volumes are kept)  
* `make restart` → Restart the whole stack (down + up)  
* `make shell` → Open an interactive shell inside the PHP container  
* `make install` → Run `composer install` inside the PHP container  
* `make migrate` → Run SQLite migration script  
* `make reset-safe` → Reset environment but keep volumes/data  
* `make reset-hard` → Full reset (containers, images, volumes removed – ⚠️ destructive)  

👉 For advanced usage (utilities, project-only container listing, shortcuts like `make sh`, `make i`, etc.), check the [Makefile](./Makefile) at the root of the repository.

## Troubleshooting

* `Class 'Base' not found` → run `make install` (or `composer dump-autoload`)  
* `no such table: users` → run `make migrate`  
* `fatfree-core not found` → run `make install`