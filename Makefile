dev:
	mkdir -p backend/var/jwt
	openssl genrsa -out backend/var/jwt/private.pem -aes256 -passout pass:secret 4096
	openssl rsa -pubout -in backend/var/jwt/private.pem -out backend/var/jwt/public.pem -passin pass:secret
	cp backend/app/config/parameters-sample.yml backend/app/config/parameters.yml
	docker-compose up -d
	docker-compose exec php composer install
	docker-compose exec node npm ci
	docker-compose exec node npm run build

tests: eslint jest ecs phpstan yaml_lint twig_lint phpunit phpcs

eslint:
	docker-compose run node npm run lint

jest:
	docker-compose run node npm run test

ecs:
	docker-compose run php php ./vendor/bin/ecs check ./src

phpstan:
	docker-compose run php php -d memory_limit=4G ./vendor/bin/phpstan analyse src --level 5

yaml_lint:
	docker-compose run php php bin/console lint:yaml src
    docker-compose run php php bin/console lint:yaml app

twig_lint:
	docker-compose run php php bin/console lint:twig src

phpunit:
	docker-compose run php php ./vendor/bin/phpunit -c phpunit.xml.dist --do-not-cache-result --no-coverage

phpcs:
	docker-compose run php php ./vendor/bin/phpcs --extensions=php --standard=PSR2 ./src