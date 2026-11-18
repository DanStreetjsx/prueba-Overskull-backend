# Backend – API de productos y categorías

API REST en Laravel 12 para gestionar productos y categorías, con auditoría de operaciones y documentación Swagger.

## Tecnologías

- Laravel 12 (PHP ^8.2) – API REST
- MySQL – base de datos
- Docker Compose (Laravel Sail) – contenedores
- L5-Swagger – documentación OpenAPI
- Spatie Activitylog – auditoría de create/update/delete
- Vue 3 – frontend (carpeta `../frontend-prueba`)

## Requisitos funcionales cubiertos

- Auditoría de toda acción create/update/delete.
- Endpoints documentados con Swagger (atributos `OpenApi\Attributes`).
- Validación de inputs y respuestas JSON estandarizadas (`success`, `message`, `errors`).
- Código separado por responsabilidades (controllers, modelos, documentación en `app/OpenApi`).

## Puesta en marcha

Requisitos: [Docker](https://www.docker.com/) con Docker Compose. Todos los comandos se ejecutan dentro de la carpeta `backend`.

### 1. Variables de entorno

```sh
cp .env.example .env
```

Para usar MySQL del contenedor, deja estos valores en el `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

### 2. Instalar dependencias de PHP

```sh
docker run --rm -v "${PWD}:/app" -w /app composer:2 composer install --no-scripts
```

### 3. Levantar los contenedores

```sh
docker compose up -d --build
docker compose ps
```

### 4. Inicializar la aplicación

```sh
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test php artisan db:seed
docker compose exec laravel.test php artisan l5-swagger:generate
```

### 5. Comprobar

| Recurso       | URL                                      |
| ------------- | ---------------------------------------- |
| API           | <http://localhost/api>                   |
| Swagger UI    | <http://localhost/api/documentation>     |

La app usa el puerto `80` por defecto. Para cambiarlo, define `APP_PORT` en el `.env` (y ajusta `VITE_API_URL` en el frontend).

## Endpoints

Base: `/api`

| Método       | Ruta                  | Descripción                          |
| ------------ | --------------------- | ------------------------------------ |
| GET          | `/categories`         | Listar categorías                    |
| POST         | `/categories`         | Crear categoría                      |
| GET          | `/categories/{id}`    | Ver categoría con sus productos      |
| PUT / PATCH  | `/categories/{id}`    | Actualizar categoría                 |
| DELETE       | `/categories/{id}`    | Eliminar (409 si tiene productos)    |
| GET          | `/products`           | Listar paginado (`category_id`, `search`, `page`) |
| POST         | `/products`           | Crear producto                       |
| GET          | `/products/{id}`      | Ver producto                         |
| PUT / PATCH  | `/products/{id}`      | Actualizar producto                  |
| DELETE       | `/products/{id}`      | Eliminar producto                    |

### Formato de error

Los errores de validación devuelven `422`:

```json
{
  "success": false,
  "message": "El campo 'name' es obligatorio",
  "errors": {
    "name": ["El campo nombre es requerido"]
  }
}
```

Un recurso inexistente devuelve `404` con `{ "success": false, "message": "Recurso no encontrado" }`.

## Estructura relevante

```
app/
├── Http/Controllers/   # CategoryController, ProductController
├── Models/             # Category, Product (con auditoría)
└── OpenApi/            # Documentation.php (info, servidor y esquema ErrorResponse)
bootstrap/app.php       # rutas API y respuestas de error personalizadas
routes/api.php          # endpoints CRUD
database/               # migraciones y seeders
```

## Comandos útiles

```sh
docker compose logs -f laravel.test                       # ver logs
docker compose exec laravel.test php artisan migrate:fresh --seed   # reiniciar la base de datos
docker compose exec laravel.test php artisan route:list   # listar rutas
docker compose down                                       # apagar contenedores
```

Después de cambiar anotaciones Swagger, vuelve a ejecutar `php artisan l5-swagger:generate`.

## Frontend

Ver [../frontend-prueba/README.md](../frontend-prueba/README.md).
