# Pet Shop

A pet shop implementation.

## Requirements
 - PHP == 8.2

Installation
------------
1. Clone project
2. Copy `.env.example` to `.env` i.e `cp .env.example .env`
3. RUN `composer install`
4. RUN `docker-compose up` NB: Ensure no other docker containers are running
5. RUN `docker ps` to see running containers, and copy the hash/id of the app container. The image name should look like this `pet-shop-api_pet-shop-api`
6. RUN `docker exec -it [container_id] bash` to ssh into the running app container
7. RUN `php artisan key:generate`
8. RUN `php artisan migrate --seed`
10. Done!

## Tests
-------------
Tests are found in the test directory. The project contains Feature tests.
A sample configuration file has been included `.env.testing`, it is currently set to use mysql database, so therefore

Also ensure to create a `storage/app/private-key.pem` for the asymmetric key before running tests. A sample .pem file has been include in the publoc dir. 

Steps to run tests:

1. cd into project dir
2. RUN `php artisan test`

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
