# Pet Store

A pet store implementation.

## Requirements
------------
 - PHP == 8.2

Installation
------------
1. Clone project
2. Copy `.env.example` to `.env`
3. Run `docker-compose up` NB: Ensure no other docker containers are running
4. Run `docker-compose ps` to see running containers, and copy the hash/id of the app container.
5. Run `docker-compose exec -it [container_id] bash` to ssh into the running app container
6. Run `php artisan key:generate`
7. Run `php artisan migrate --seed`
8. Run `php artisan test` for tests
9. Done!

API Description
---------------
Navigate to `your-host/api/docs` to load the swagger API documentation.
