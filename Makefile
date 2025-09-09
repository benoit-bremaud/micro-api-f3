up:
	@docker-compose up -d --build

perms:
	@mkdir -p data tmp
	@chmod -R 0777 data tmp

migrate:
	@docker-compose exec php php cli/migrate.php
