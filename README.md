# IGSCORE Tipster Admin Panel

This is the admin panel intended to manage data related to Tipster module on IGSCORE.

## Tech Stack

- PHP 8.1
- Laravel Framework 10.48
- MySQL 8.0
- Redis
- NodeJS 16

## Requirements

- Docker Desktop
- Docker Compose

## Development

- Please use `develop` branch for local development.
- The `release` branch will be used for deployment.
- Please use pull request to merge updates into `release` branch.
- The `main` branch will be updated to sync with `release` branch.

## Local Deployment

Execute the following commands on a terminal or command shell.

- Build and start the Docker stacks:
```
docker-compose up -d
```
- After the containers are all up and running, install the dependencies:
```
docker-compose exec app composer install
```
- Produce the `.env` file based on `.env.example`:
```
cp .env.example .env
```
- Generate the app key:
```
docker-compose exec app php artisan key:generate
```
- Modify the `.env` file and replace the `DB` part as follows:
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=igscore_tipster
DB_USERNAME=root
DB_PASSWORD=Password1234@@
```
- Resolve some permission issues:
```
docker-compose exec app chmod -R 777 storage
docker-compose exec app chmod -R 777 bootstrap/cache
```
- Open your browser and enter `localhost:8000` to see whether the web app has been running properly.