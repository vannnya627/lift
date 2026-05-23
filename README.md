* Clone project:
```
git clone https://github.com/vannnya627/lift.git
```
---
* Start Docker containers:
```
docker compose up -d
```
---
* Install PHP dependencies:
```
docker compose exec -u www-data -w /var/www/project php-fpm composer install
```
---
* Create MongoDB indexes:
```
docker compose exec php-fpm php bin/console doctrine:mongodb:schema:update
```
---
* Check worker logs:
```
docker-compose logs -f event-worker
```
* or
```
docker-compose logs -f command-worker
```
---
* Check the documentation: http://localhost/api/doc
---

### Before running "Functional Tests" for the first time, you need to prepare a test database:
### MongoDB:
* Prepare test database indexes:
```
docker compose exec php-fpm php bin/console doctrine:mongodb:schema:update --env=test
```
---
### Run tests:
```
docker compose exec php-fpm php bin/phpunit 
```
### XDEBUG(one of command):

```
 docker compose exec php-fpm php bin/phpunit --coverage-html coverage-report
```
* or
```
docker compose exec php-fpm php bin/phpunit --coverage-text
```

