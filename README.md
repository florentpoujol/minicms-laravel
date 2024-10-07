# Minicms Laravel

A demo application (a small CMS) with Laravel 11.

## Setup

- Build the docker image with `docker compose build`.
- Copy the `.env.local` file to `.env`.
- Once the docker container has finished building, run `docker compose up -d`.
- Install the composer depencies with `docker/composer install`
- Run the migrations with `docker/artisan migrate` and `docker/artisan migrate`, then both commands again in the testing env with the `--env=testing` option

The site is available via a browser at `localhost:8084`. if needed change the port in the `compose.yaml` file.

The tests will run with `docker/artisan test`.

The seeders contain 3 users that you can login with:
- `regular@example.com`, password `regular`
- `writer@example.com`, password `writer`
- `admin@example.com`, password `admin`
