# Development tools

## Database

On local environment we're using MySQL database on Docker container. When environment is
online, you can connect your favorite database client to local database with following configuration:

* Host: `localhost`
* Port: `3306`
* User: `adventure_vue`
* Password: `secret`
* Database: `adventure_vue`

## Code quality tools

We're using ECS, PhpStan, PhpUnit and PhpCs on backend side and Eslint with Jest on frontend side. You can run
tests with following commands:

```bash
# ECS
$ docker-compose run php php ./vendor/bin/ecs check ./src
# PhpStan
$ docker-compose run php php -d memory_limit=4G ./vendor/bin/phpstan analyse src --level 5
# PhpUnit
$ docker-compose run php php ./vendor/bin/phpunit -c phpunit.xml.dist --do-not-cache-result --no-coverage
# PhpCs
$ docker-compose run php php ./vendor/bin/phpcs --extensions=php --standard=PSR2 ./src
# Eslint
$ docker-compose run node npm run lint
# Jest
$ docker-compose run node npm run test
```

Or, if you have `make`:
```bash
$ make tests
```

It will call all test quality commands + additional Symfony commands for linting Twig templates and Yaml configuration
files. All of these commands are used during testing phase in CI tool.