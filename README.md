# Minicms Laravel


## Setup

- Build the docker image with `docker compose build`.
- Copy the `.env.local` file to `.env`.
- Once the docker container has finished building, run `docker compose up -d`.
- Install the composer depencies with `docker/composer install`
- Run the migrations with `docker/artisan migrate` and `docker/artisan migrate --env=testing`

The site should be available via a browser at `localhost:8084`. if needed change the port in the `compose.yaml` file.

The tests should run with `docker/php vendor/bin/phpunit`.

For some reason `docker/artisan test` doesn't work properly, it starts PHPUnit but that complain that there isn't any file named "test".
Running `docker/artisan tests` properly ask us to correct the command to "test", which then works properly.

