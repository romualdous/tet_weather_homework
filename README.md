# Symfony Docker

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up` To start docker containers
4. Run `docker compose down` to stop the Docker containers.

## Endpoints
*To start working with TET Homework Weather test.*
Endpoint : https://localhost/weather <br>
 || Method:[POST] ||
  <br> 
  optional parametr : `refresh` -> type:`bool` 
  <br>
  (If `refresh` provided to request,controller will force to update clients weather ,even if its cashed or not. Force update.)

## Database
*When building up docker container,it will create database in postgressql,called WeatherTest*
1) Configure `docker-compose.yml` file to modify database login/password/db_name in field `DATABASE_URL`. (By default : `Login : root | Password : root | db_name : WeatherTest`.)
2) To modify connection datas,please open `.env` and mofidy -> `DATABASE_URL` , before executing console command to create docker container.

## 3rd API SERVICES
*To make this homework,i created accounts on website : https://openweathermap.org and https://ipstack.com to get API keys for request*
1) For test purpose you still can use my API Keys to make testing.
2) If you wish to modify them,please open `.env` file , and mofidy : 
    2.1) `IPSTACK_API_KEY` -> https://ipstack.com
    2.2) `WEATHER_API_KEY` -> https://openweathermap.org
If any of this 3rd party API will not work,controller will drop exception.

## Used tools
1) Phpstorm as IDE
2) HeidiSQL as Database Connection tool,or PhpStorm included databsae connection.
3) Visual Code
4) Docker
5) Composer
6) GitHub

## P.S 
In `DockerFile`,before creating containers,i inserted version `5.4` of symfony. But he still upgraded my symfony to `6.2`,for no reason.I recognized it later,when homework was almost done.
Php version configured correct to `8.1.16`. 

**Enjoy!**

## Homework made : Romans Rjabcevs

## License

Symfony Docker is available under the MIT License.

## Credits

Created by [Kévin Dunglas](https://dunglas.fr), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
