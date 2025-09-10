# ============================================================
# Makefile for micro-api-f3
# ------------------------------------------------------------
# Purpose:
#   Provide a unified interface for managing the Docker-based
#   development environment of this project.
#
# Highlights:
#   - Uses the new `docker compose` syntax (no hyphen).
#   - DRY principle: commands are centralized, reused via $(MAKE).
#   - Two reset flows: safe (keep data) and hard (purge all).
#   - Shortcuts for faster daily usage.
#   - Each target includes clear explanations for readability.
# ============================================================

# ---------- Variables ----------
PROJECT     ?= $(notdir $(CURDIR))  # Current directory name used as Compose project name
COMPOSE     ?= docker compose       # Docker Compose command (new syntax)
PHP_SERVICE ?= php                  # Main PHP service name (from docker-compose.yml)
EXEC_PHP    = $(COMPOSE) exec $(PHP_SERVICE)

# ============================================================
# HELP SECTION
# ============================================================

## help: Display all available commands with explanations.
help:
	@echo "Available commands (with explanations):"
	@echo ""
	@echo "Core lifecycle:"
	@echo "  make up           - Start services in detached mode (create if needed)."
	@echo "  make up-build     - Rebuild images, then start services in detached mode."
	@echo "  make down         - Stop and remove containers and networks (volumes are kept)."
	@echo "  make restart      - Restart the full stack (equivalent to down + up)."
	@echo "  make ps           - Show running containers for this Compose project."
	@echo "  make logs         - Stream logs of all services (Ctrl+C to exit)."
	@echo ""
	@echo "Inside containers:"
	@echo "  make shell        - Open an interactive Bash shell inside the PHP container."
	@echo "  make install      - Run 'composer install' inside the PHP container."
	@echo "  make migrate      - Execute SQLite migration script inside the PHP container."
	@echo ""
	@echo "Utilities:"
	@echo "  make perms        - Ensure write permissions for local folders data/ and tmp/."
	@echo "  make ps-project   - List only containers belonging to this Compose project."
	@echo ""
	@echo "Reset flows:"
	@echo "  make reset-safe   - Soft reset: stop and remove containers (keep DB/data), then rebuild."
	@echo "  make reset-hard   - Full reset: remove containers, images, volumes, orphans, prune system, then rebuild (DANGEROUS)."
	@echo ""
	@echo "Low-level cleanup:"
	@echo "  make purge        - Purge everything for this project: containers, images, volumes, networks, orphans (DANGEROUS)."
	@echo ""
	@echo "Shortcuts (aliases for daily use):"
	@echo "  make sh   -> shell"
	@echo "  make i    -> install"
	@echo "  make m    -> migrate"
	@echo "  make r    -> restart"
	@echo "  make l    -> logs"
	@echo "  make rs   -> reset-safe"
	@echo "  make rh   -> reset-hard"

# ============================================================
# CORE LIFECYCLE COMMANDS
# ============================================================

## up: Start services in detached mode (create if needed).
up:
	@$(COMPOSE) up -d

## up-build: Rebuild images, then start services in detached mode.
up-build:
	@$(COMPOSE) up -d --build

## down: Stop and remove containers and networks (volumes are kept).
down:
	@$(COMPOSE) down

## restart: Restart the full stack (equivalent to down + up).
restart:
	@$(MAKE) down
	@$(MAKE) up

## ps: Show running containers for this Compose project.
ps:
	@$(COMPOSE) ps

## logs: Stream logs of all services (Ctrl+C to exit).
logs:
	@$(COMPOSE) logs -f --tail=200

# ============================================================
# CONTAINER OPERATIONS
# ============================================================

## shell: Open an interactive Bash shell inside the PHP container.
shell:
	@$(EXEC_PHP) bash

## install: Run 'composer install' inside the PHP container.
install:
	@$(EXEC_PHP) composer install

## migrate: Execute SQLite migration script inside the PHP container.
migrate:
	@$(EXEC_PHP) php cli/migrate.php

# ============================================================
# UTILITIES
# ============================================================

## perms: Ensure write permissions for local folders data/ and tmp/.
perms:
	@mkdir -p data tmp
	@chmod -R 0777 data tmp

## ps-project: List only containers belonging to this Compose project.
ps-project:
	@docker ps --filter "label=com.docker.compose.project=$(PROJECT)" \
		--format "table {{.Names}}\t{{.Status}}\t{{.Image}}\t{{.Ports}}"

# ============================================================
# RESET FLOWS
# ============================================================

## purge: Purge everything for this project (containers, images, volumes, networks, orphans).
## WARNING: This is destructive and should be used with caution.
purge:
	@echo ">>> Purging Compose project '$(PROJECT)' (containers, images, volumes, networks, orphans)..."
	@$(COMPOSE) down -v --rmi all --remove-orphans
	@echo ">>> Global prune for dangling data (safe but aggressive):"
	@docker system prune -af --volumes

## reset-safe: Soft reset (keep data volumes).
## Stops containers, removes them, then rebuilds images and restarts everything.
reset-safe:
	@echo ">>> SAFE reset (volumes kept, DB/data preserved)..."
	@$(MAKE) down
	@$(MAKE) up-build

## reset-hard: Hard reset (remove everything including volumes).
## Equivalent to purge + full rebuild. WARNING: DB/data will be lost.
reset-hard:
	@echo ">>> HARD reset (containers, images, volumes WILL BE REMOVED)..."
	@$(MAKE) purge
	@$(MAKE) up-build

# ============================================================
# SHORTCUTS (ALIASES)
# ============================================================

## Aliases for quicker daily usage
sh: shell
i: install
m: migrate
r: restart
l: logs
rs: reset-safe
rh: reset-hard
