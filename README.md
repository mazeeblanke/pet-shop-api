# Pet Shop

A pet shop implementation.

## Requirements
 - PHP == 8.2

Installation
------------
1. Clone project
2. Copy `.env.example` to `.env` i.e `cp .env.example .env`
3. RUN `composer install`
4. Run `docker-compose up` NB: Ensure no other docker containers are running
5. Run `docker ps` to see running containers, and copy the hash/id of the app container. The image name should look like this `pet-shop-api_pet-shop-api`
6. Run `docker exec -it [container_id] bash` to ssh into the running app container
7. Run `php artisan key:generate`
8. Run `php artisan migrate --seed`
9. Run `php artisan test` for tests
10. Done!

API Description
---------------
Navigate to `your-host/api/docs` to load the swagger API documentation. e.g `http://localhost:3000/api/documentation`


Implemented Features
--------------------
Brands: Create, Read, Delete, List All

Products: Create, Read, Delete, List All

Category: Create, Read, Delete, List All

User: Login, Logout, Create

Admin: Login, Logout, User Listing
