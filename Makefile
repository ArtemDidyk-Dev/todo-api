# Makefile

# The default 'help' target
.PHONY: help
help:
	@echo "Available commands:"
	@echo "  up       		- Starts containers"
	@echo "  down     		- Shuts down docker containers"
	@echo "  stop     		- Stops docker containers"
	@echo "  ecs      		- Executes ECS (Easy Coding Standard) for PHP code correction"
	@echo "  stan     		- Runs PHPStan for static analysis of PHP code"
	@echo "  init     		- Initializes the project"
	@echo "  test     		- Runs unit tests"
	@echo "  clean    		- symfony cache clear"
	@echo "  migrate  		- migrate database"
	@echo "  schema-update  - update schema"

# UP command
.PHONY: up
up:
	@USER_ID=$$(id -u) GROUP_ID=$$(id -g) docker compose up -d --build

# DOWN command
.PHONY: down
down:
	@docker compose down -v --remove-orphans

# STOP command
.PHONY: stop
stop:
	@docker compose stop

# ECS command
.PHONY: ecs
ecs:
	@docker exec todo-php vendor/bin/ecs check --fix

# PHPStan command
.PHONY: stan
stan:
	@docker exec todo-php vendor/bin/phpstan analyse src tests

# Init command
.PHONY: init
init:
	@docker compose down -v --remove-orphans
	@USER_ID=$$(id -u) GROUP_ID=$$(id -g) docker compose up -d --build
	@cp hooks/pre-commit .git/hooks/pre-commit
	@chmod +x .git/hooks/pre-commit
	@echo "Git hooks installed."

# Tests command
.PHONY: test
test:
	@docker exec todo-php vendor/bin/phpunit

# Clean command
.PHONY: clean
clean:
	@docker exec todo-php php bin/console cache:clear

# Database migration command
.PHONY: migrate
migrate:
	@docker exec todo-php php bin/console doctrine:migrations:migrate

# Database migration command
.PHONY: schema-update
schema-update:
	@docker exec todo-php php bin/console doctrine:schema:update --force --complete

# Setting 'help' as the default target
.DEFAULT_GOAL := help
