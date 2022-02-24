# Project setup for development

Requirements:
* Docker
* Docker-Compose
* `openssl` command available

Setup was tested on Linux machines. All commands are called from repository's root directory.
Remember to change example.com domain with domain set during docker configuration.

After installation with one of the following instructions you will be able to access website on
`adventure-local.example.com URL. 

## With Make

If you have `make` installed, running `make dev` should handle whole set up project. After the setup is finished, run:

```shell
$ docker network inspect adventure_default | grep proxy -A 4 | grep v4
```

Result should look for example like this:

```
"IPv4Address": "172.18.0.11/16"
```

Copy address (in sample above it's "172.18.0.11") and create entry in `/etc/hosts` file:

```shell
172.18.0.11 adventure-local.example.com
```

You should now have working environment on `adventure-local.example.com` URL. If something went wrong, try following
setup instruction without `make`.

## Without Make

Start with generating SSL keys for correct JWT signing:

```shell
$ mkdir -p backend/var/jwt
$ openssl genrsa -out backend/var/jwt/private.pem -aes256 -passout pass:secret 4096
$ openssl rsa -pubout -in backend/var/jwt/private.pem -out backend/var/jwt/public.pem -passin pass:secret
```

In `app/config` directory copy either empty `parameters.yml.dist` or sample `parameters-sample.yml` to `parameters.yml`
file and edit that file if you want to.

```shell
$ cp backend/app/config/parameters-sample.yml backend/app/config/parameters.yml
```

Now run docker environment:

```shell
$ docker-compose up -d
```

Install backend and frontend dependencies:

```shell
$ docker-compose exec php composer install
$ docker-compose exec node npm ci
```

Build frontend:

```shell
$ docker-compose exec node npm run build
```

After the setup is finished, run:

```shell
$ docker network inspect adventure_default | grep proxy -A 4 | grep v4
```

Result should look for example like this:

```
"IPv4Address": "172.18.0.11/16"
```

Copy address (in sample above it's "172.18.0.11") and create entry in `/etc/hosts` file:

```shell
172.18.0.11 adventure-local.example.com
```

You should now have working environment on `adventure-local.example.com` URL.