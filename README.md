# Reviews Service (FlexShop)

Service Symfony de gestion des avis clients.

Les clients peuvent noter (1 à 5) et commenter les produits du Catalogue.
Avant d'accepter un avis, le service vérifie auprès du service Catalogue
(Django) que le produit existe.

## Stack

- Symfony 7 + API Platform (API REST + doc OpenAPI automatique)
- Doctrine ORM + migrations
- MariaDB 10.11 (conteneur Docker en dev)
- Symfony HttpClient (appel au Catalogue, timeout 3 s)
- PHPUnit (WebTestCase)

## Démarrer

```bash
composer install

# Base de données MariaDB (Docker)
docker run -d --name reviews-db \
  -e MARIADB_DATABASE=reviews -e MARIADB_USER=app \
  -e MARIADB_PASSWORD=password -e MARIADB_ROOT_PASSWORD=root \
  -p 3307:3306 mariadb:10.11

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate -n
symfony serve -d --port=8001
```

Le service Catalogue (Django) doit tourner sur http://localhost:8000
(modifiable via la variable d'environnement `CATALOG_URL`).

## Endpoints

- Swagger : http://localhost:8001/api
- API : http://localhost:8001/api/reviews

| Méthode | URI | Description | Code |
|---|---|---|---|
| GET | /api/reviews | Liste paginée (10/page) | 200 |
| GET | /api/reviews/{id} | Détail d'un avis | 200/404 |
| POST | /api/reviews | Création (vérifie le produit via Catalogue) | 201/422 |
| PATCH | /api/reviews/{id} | Modification partielle | 200 |
| DELETE | /api/reviews/{id} | Suppression | 204 |

Filtrage par produit : `GET /api/reviews?productId=2`

## Validation

- `productId` : obligatoire, doit exister dans le Catalogue (validator
  custom `ProductExists`, retourne 422 sinon ; si le Catalogue est
  indisponible, l'avis est refusé avec « Catalogue indisponible »)
- `authorName` : 1 à 100 caractères
- `rating` : entier de 1 à 5
- `comment` : optionnel

## Tests

```bash
docker exec reviews-db mariadb -uroot -proot \
  -e "CREATE DATABASE IF NOT EXISTS reviews_test; GRANT ALL ON reviews_test.* TO 'app'@'%';"
php bin/console doctrine:migrations:migrate -n --env=test
php bin/phpunit
```
