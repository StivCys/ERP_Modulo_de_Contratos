.PHONY: octane fpm down build logs

octane:
	@docker compose --profile fpm stop nginx 2>/dev/null || true
	PHP_SERVER=octane APP_PORT=8000 docker compose up -d

fpm:
	PHP_SERVER=fpm APP_PORT=127.0.0.1:0 docker compose --profile fpm up -d

down:
	docker compose --profile fpm down

build:
	docker compose --profile fpm build

logs:
	docker compose --profile fpm logs -f
