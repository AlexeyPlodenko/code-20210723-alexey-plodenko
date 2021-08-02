# code-20210723-alexey-plodenko
Laravel 8, PHP 8, MySQL 5.7, Docker, REST API

## Prerequisites
Docker, Composer.

Free ports - 80.

## To start the project
1. Navigate to the project's directory in CLI.
2. Navigate to `/backend` and execute `composer install`.
4. Navigate to the project's root directory and execute `docker-compose up`.
5. Navigate to `/backend` and execute `php artisan migrate:fresh --seed`.

## Implementation
The implementation provides a CRUD interface to the users.

API authorization is ensured by using the user API tokens. Access to the update and delete user is restricted to the user himself or admin.

## API Endpoints
* POST /api/v1/user - Register a user.
* GET /api/v1/user/1 - Get the user data.
* PUT /api/v1/user/1 - Update the user.
* DELETE /api/v1/user/1 - Delete the user.
* POST /api/v1/user/login - Login with credentials.

Postman collection https://www.getpostman.com/collections/66ccc9718b91bd71ade4

## Possible improvements
* Execute `composer install` and `artisan migrate` inside the Docker image. Less manual work.
* Throughout testing of the endpoints to prevent the edge cases and framework hidden behavior.
* Fix the user enumeration vulnerability.
* Brute-force protection.
* Throttle protection.
* Nginx instead of Apache to save resources and mitigate the slow connections.

## TODO
* JWT
* Tests
* Websockets
* List users endpoint
* Disable exposed Redis ports in Docker
