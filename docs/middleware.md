# Middleware

## Purpose

Middleware runs before the controller. It can:

- Authenticate the user (e.g. check session).
- Log the request, add headers, or validate input.
- Short-circuit the request by sending a response and calling `exit`.

If middleware does not exit, the request continues to the next middleware and then to the controller.

## Interface

All middleware must implement `Core\Contracts\MiddlewareInterface`:

```php
namespace Core\Contracts;

interface MiddlewareInterface
{
    public function handle(): void;
}
```

Place your classes in `app/Middleware/` and name them `{Name}Middleware` (e.g. `WebAuthMiddleware`, `AuthMiddleware`). They are resolved as `App\Middleware\{Name}Middleware` when you use the string `webAuth` or `auth` in routes or attributes.

## Route-level middleware

Attach one or more middlewares to a group of routes:

```php
Router::middleware(['webAuth'], function () {
    Router::get('/home', [LoginController::class, 'home']);
});
```

You can pass multiple names: `['webAuth', 'log']`.

## Attribute-based middleware

Use PHP 8 attributes to attach middleware to a controller class or to a specific method.

**On a method:**

```php
use Core\Attributes\Middleware;

class HelloController
{
    #[Middleware('auth')]
    public function askAgent()
    {
        // only runs if AuthMiddleware passes
    }
}
```

**On the class (applies to every action):**

```php
#[Middleware('auth')]
class AdminController
{
    public function index() { ... }
    public function store() { ... }
}
```

**Multiple middlewares:**

```php
#[Middleware(['auth', 'admin'])]
public function destroy() { ... }
```

Execution order: route group middlewares first, then class attributes, then method attributes, then the controller.

## Example: auth middleware

```php
namespace App\Middleware;

use Core\Contracts\MiddlewareInterface;

class WebAuthMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        if (empty($_SESSION['user_id'] ?? null)) {
            header('Location: /login');
            exit;
        }
    }
}
```

If the user is not logged in, redirect and exit. Otherwise do nothing and let the request continue.
