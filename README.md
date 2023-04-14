# Pet Shop

A pet shop implementation.

## Requirements
 - PHP == 8.2
 - Docker Desktop

## Installation

Install using any of the options below

A) Using bash script

  1. Clone project and cd into project e.g `git clone https://github.com/mazeeblanke/pet-shop-api.git && cd pet-shop-api`

  2. RUN ` bash install.bash ` at the root of the project
  3. Application now running on `http://localhost:3000/`
  4. Swagger documentation running on `http://localhost:3000/api/documentation`
  5. Done!

B) Follow the steps below

1. Clone project and cd into project e.g `git clone https://github.com/mazeeblanke/pet-shop-api.git && cd pet-shop-api`
2. Copy `.env.example` to `.env` i.e `cp .env.example .env` and `.env.testing.example` to `.env.testing`
3. RUN `composer install`
4. RUN `docker-compose up` NB: Ensure no other docker containers are running

5. To gain access into the app container environment, use either method A or B
``` 
 A) RUN `docker-compose exec pet-shop-api bash`
```
```
 B) RUN `docker ps` to see running containers, and copy the hash/id of the app container. The image name should look like this `pet-shop-api_pet-shop-api`

    RUN `docker exec -it [container_id] bash` to ssh into the running app container
```

7. RUN `php artisan key:generate`
8. RUN `php artisan migrate --seed`
9. Application now running on `http://localhost:3000/`
10. Swagger documentation running on `http://localhost:3000/api/documentation`
11. Done!



## Tests

Tests are found in the test directory. The project contains Feature tests.
A sample configuration file has been included `.env.testing`, it is currently set to use mysql database, so therefore

Also ensure to create a `storage/app/private-key.pem` for the asymmetric key before running tests. A sample .pem file has been include in the publoc dir. 

Steps to run tests:

1. cd into project dir
2. RUN `php artisan test`

## Currency Converter

The project also includes a currency converter package at `packages/mazi/currency-converter`

This is the full readme file `packages/mazi/currency-converter/README.md`

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
