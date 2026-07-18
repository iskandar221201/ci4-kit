# CI4 Production Grade Kit (ci4-pgk)

A CodeIgniter 4 starter kit with production-ready patterns:
structured API responses, Shield authentication, filter stack, service layer,
and structured JSON logging — ready to clone and extend.

---

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+ or MariaDB 10.5+
- Web server (Apache / Nginx) or `php spark serve`

---

## Quick Start

```bash
# 1. Clone and enter the project
git clone <repo-url> my-project && cd my-project

# 2. Copy environment template and fill in DB credentials
cp .env.example .env

# 3. Install dependencies
composer install

# 4. Run all migrations (CI4 + Shield tables)
php spark migrate --all

# 5. Start the development server
php spark serve

# 6. Verify the setup
curl http://localhost:8080/api/ping
# Expected: {"status":true,"code":200,"message":"pong","data":null}
```

---

## Architecture Overview

```
Request → Filter Stack → Controller → Service → Model → Database
```

- **Controller** — receives the request, delegates to the Service, returns a JSON response.
  Never accesses a Model directly.
- **Service** — holds business logic, validates input, orchestrates Model calls.
- **Model** — extends `BaseModel`, handles DB queries and soft deletes.

All API responses use the standard envelope:

```json
{"status": true, "code": 200, "message": "...", "data": {...}}
{"status": false, "code": 422, "message": "...", "errors": {...}}
```

---

## How to Add a New Resource

Example: adding a `Post` resource.

1. Create migration:
   ```bash
   php spark make:migration CreatePostsTable
   php spark migrate
   ```

2. Create Model — `app/Models/PostModel.php`:
   ```php
   class PostModel extends BaseModel { ... }
   ```

3. Create Service — `app/Services/PostService.php`:
   ```php
   class PostService extends BaseService {
       protected string $modelClass = PostModel::class;
   }
   ```

4. Create Controller — `app/Controllers/Api/PostController.php`:
   ```php
   class PostController extends BaseApiController { ... }
   ```

5. Register routes in `app/Config/Routes.php`:
   ```php
   $routes->group('api', ['filter' => 'apiKeyFilter'], static function ($routes) {
       $routes->get('posts', 'Api\PostController::index');
       $routes->post('posts', 'Api\PostController::create');
       $routes->get('posts/(:num)', 'Api\PostController::show/$1');
       $routes->put('posts/(:num)', 'Api\PostController::update/$1');
       $routes->delete('posts/(:num)', 'Api\PostController::delete/$1');
   });
   ```

See `app/Services/UserService.php` and `app/Controllers/Api/UserController.php` as a full reference.

---

## Filter Stack

```
Request → CorsFilter → ApiKeyFilter / AuthFilter → Controller
```

| Filter | Path | Purpose |
|---|---|---|
| `CorsFilter` | `api/*` | Injects CORS headers; handles OPTIONS preflight (204) |
| `ApiKeyFilter` | `api/*` (protected group) | Validates Bearer token via Shield AccessTokens |
| `AuthFilter` | web routes | Checks session login; redirects to `/login` if missing |

Filter registration: `app/Config/Filters.php`

---

## Logging

Use `AppLogger` anywhere:

```php
use App\Libraries\AppLogger;

AppLogger::info('payment.success', ['amount' => 50000, 'user_id' => 12]);
AppLogger::error('webhook.failed', ['payload' => $raw], $exception);
```

Inside a Controller (via `LoggableTrait`):

```php
$this->logInfo('user.created', ['id' => $userId]);
$this->logError('user.create.failed', [], $e);
```

Logs are written to `writable/logs/` as structured JSON strings.

---

## Server Requirements

PHP 8.2 or higher with the following extensions:

- `intl`
- `mbstring`
- `json` (enabled by default)
- `mysqlnd` (for MySQL)
- `libcurl` (if using `HTTP\CURLRequest`)
