.PHONY: help init build up start down restart migrate fresh-seed

help:
	@echo "Использование:"
	@echo "  make init      - Первоначальная настройка проекта (сборка, миграции, сиды)"
	@echo "  make build     - Собрать/пересобрать контейнеры"
	@echo "  make up        - Запустить контейнеры"
	@echo "  make down      - Остановить контейнеры"
	@echo "  make restart   - Перезапустить контейнеры"
	@echo "  make migrate   - Выполнить миграции"
	@echo "  make fresh-seed - Пересоздать БД с сидами"

init: build up
	@echo "\033[32mПроект успешно инициализирован!\033[0m"

build:
	@docker-compose down --remove-orphans
	@docker-compose build --no-cache --pull
	@echo "\033[32mКонтейнеры успешно собраны\033[0m"

up:
	@docker-compose up -d
	@echo "\033[32mКонтейнеры запущены\033[0m"

down:
	@docker-compose down --remove-orphans
	@echo "\033[32mКонтейнеры остановлены\033[0m"

restart: down up
	@echo "\033[32mКонтейнеры перезапущены\033[0m"

keygen:
	@docker-compose exec app php artisan key:generate
	@echo "\033[32mКлюч приложения сгенерирован\033[0m"

migrate:
	@docker-compose exec app php artisan migrate
	@echo "\033[32mМиграции выполнены\033[0m"

fresh-seed:
	@docker-compose exec app php artisan migrate:fresh --seed
	@echo "\033[32mБД пересоздана с сидами\033[0m"
