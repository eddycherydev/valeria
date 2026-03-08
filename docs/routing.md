# Routing

## Overview

Routes are defined in PHP files under `routes/web/` and `routes/api/`. `public/index.php` loads all files in those directories via `RouteLoader::loadDirectory()`, so every `*/*.php` file is included (e.g. `routes/web/web.php`, `routes/api/api.php`).

Define routes with `Core\Router`:

- `Router::get($path, $callback)`
- `Router::post($path, $callback)`

The callback can be a closure or an array `[ControllerClass::class, 'methodName']`. Controller actions receive route parameters as method arguments in order (or as named arguments if you use the same names as the placeholders).

## Route parameters

Use placeholders in the path; they are passed to the controller method in order:

```php
Router::get('/post/{id}', [PostController::class, 'show']);
Router::get('/user/{id}/post/{slug}', [PostController::class, 'showPost']);
```

In `show($id)` and `showPost($id, $slug)` the values are strings; cast to `int` if needed.

## Middleware groups

Wrap routes in a middleware group so all of them run the same middlewares:

```php
Router::middleware(['webAuth'], function () {
    Router::get('/home', [LoginController::class, 'home']);
    Router::get('/profile', [ProfileController::class, 'index']);
});
```

Middleware classes are resolved as `App\Middleware\{Name}Middleware` (e.g. `webAuth` → `App\Middleware\WebAuthMiddleware`). They must implement `Core\Contracts\MiddlewareInterface` and define `handle()`. If validation fails, send the response and call `exit`. If it passes, the request continues to the controller.

You can also attach middlewares to a controller or method with the `#[Middleware('name')]` attribute (see [Middleware](middleware.md)).

## Macros

The Router uses the `Macroable` trait. You can add custom static methods that register routes:

```php
Router::macro('admin', function ($uri, $action) {
    Router::get("/admin$uri", $action);
});

Router::admin('/users', [AdminController::class, 'users']);
Router::admin('/home/{id}', [HelloController::class, 'home']);
```

Another common macro is an `api` prefix:

```php
Router::macro('api', function ($uri, $action) {
    Router::post("/api$uri", $action);
});
Router::api('/ask', [HelloController::class, 'askAgent']);
```

## Dispatch and 404

The front controller calls `Router::dispatch()`. It:

1. Normalizes the request URI (strips the script base path, ensures a leading `/`).
2. Matches the request method and path against registered routes.
3. Runs route and attribute middlewares, then invokes the controller action.
4. If no route matches, responds with `404` and a JSON body `{"error": "Route not found"}`.

Only one route is dispatched per request; after the controller runs, execution stops.
