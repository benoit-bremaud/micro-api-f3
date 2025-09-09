# Makefile pour projet micro-api-f3

# Variables
PROJECT = micro-api-f3
PHP = docker compose exec php

## help : Affiche la liste des commandes disponibles
help:
	@echo "Commandes disponibles :"
	@echo "  make up        - Démarre les services Docker"
	@echo "  make up-build  - Reconstruit et démarre les services"
	@echo "  make down      - Stoppe et supprime les conteneurs"
	@echo "  make restart   - Redémarre les services"
	@echo "  make ps        - Liste les conteneurs actifs"
	@echo "  make logs      - Affiche les logs des services"
	@echo "  make shell     - Ouvre un shell dans le conteneur PHP"
	@echo "  make install   - Installe les dépendances Composer"
	@echo "  make migrate   - Lance le script de migration SQLite"
	@echo "  make perms     - Fixe les permissions sur data/ et tmp/"

## up : Démarre les services
up:
	@docker compose up -d

## up-build : Reconstruit et démarre les services
up-build:
	@docker compose up -d --build

## down : Stoppe et supprime les conteneurs
down:
	@docker compose down

## restart : Redémarre la stack Docker
restart: down up

## ps : Liste les conteneurs actifs
ps:
	@docker compose ps

## logs : Affiche les logs des services
logs:
	@docker compose logs -f --tail=100

## shell : Ouvre un shell bash dans le conteneur PHP
shell:
	@$(PHP) bash

## install : Installe les dépendances Composer
install:
	@$(PHP) composer install

## migrate : Exécute le script de migration SQLite
migrate:
	@$(PHP) php cli/migrate.php

## perms : Fixe les permissions locales sur data/ et tmp/
perms:
	@mkdir -p data tmp
	@chmod -R 0777 data tmp
