# Valeria Framework

## Sponsored by [bigmantis.com](https://bigmantis.com)

**Valeria** is a lightweight, expressive PHP micro-framework built for speed, developer experience, and flexibility. Inspired by Laravel, it provides routing, middleware, views, an ORM, and migrations while keeping the core simple.

## Features

- **Routing** — GET/POST, route parameters `{id}`, automatic route loading from `routes/web` and `routes/api`
- **Middleware** — Route groups and PHP 8 attributes (`#[Middleware('auth')]`)
- **Views** — Layouts, sections, `yield`, `include`, and `e()` for escaping
- **Lucid ORM** — ActiveRecord-style models, fluent Query Builder, relations (`belongsTo`, `hasOne`, `hasMany`)
- **Migrations** — CLI: `make:migration`, `migrate`, `rollback`; Blueprint API for schema
- **Configuration** — `.env` loader via `Env::get()`
- **Macroable** — Extend the Router (e.g. `Router::admin()`, `Router::api()`) with custom macros
- **Agents & Skills** — Optional AI layer: skills in `app/Skills/`, agent CLI and HTTP API, optional LLM (OpenAI) and prompts
- PSR-4 autoloading and modular structure

## Installation

1. Clone the repository.
2. Copy or rename `.env.txt` to `.env`.
3. Configure `.env` with your database and app settings (see [Configuration](docs/configuration.md)).
4. Run `composer install` if needed.
5. Run migrations: `php lucid migrate`.

## Project structure

```
valeria/
├── app/
│   ├── Controllers/
│   ├── Middleware/
│   ├── Models/
│   ├── Agents/             # Agent profiles (optional)
│   ├── Skills/              # Agent skills (optional)
│   └── Views/
├── config/                 # App config, prompts
├── core/                   # Framework core (Valeria + Lucid)
│   ├── Attributes/
│   ├── Contracts/
│   ├── Lucid/              # ORM, migrations, commands
│   └── ...
├── database/
│   └── migrations/
├── docs/                   # Documentation
├── public/
│   ├── index.php
│   └── .htaccess
├── routes/
│   ├── api/
│   └── web/
├── vendor/
├── .env
├── composer.json
├── lucid                   # CLI: migrations and commands
└── readme.md
```

The web entry point is `public/index.php`. The `database/` folder is at the project root; migrations live in `database/migrations/`.

## Quick start

**Routes** — Edit `routes/web/web.php` or `routes/api/api.php`:

```php
use Core\Router;
use App\Controllers\HelloController;

Router::get('/', [HelloController::class, 'index']);
Router::get('/post/{id}', [HelloController::class, 'show']);
Router::post('/login', [LoginController::class, 'login']);
```

**Middleware group:**

```php
Router::middleware(['webAuth'], function () {
    Router::get('/home', [LoginController::class, 'home']);
});
```

**Render a view with layout:**

```php
View::render('home/home', ['name' => 'Valeria'], 'layouts/layout');
```

**Model and Query Builder:**

```php
$user = User::find(1);
$users = User::where('email', $email)->get();
$user->save();
```

## Lucid CLI

From the project root:

| Command | Description |
|--------|-------------|
| `php lucid make:migration create_posts_table` | Create a new migration file |
| `php lucid migrate` | Run pending migrations |
| `php lucid rollback` | Rollback the last batch of migrations |
| `php lucid make:skill <Name>` | Create a new skill in `app/Skills/` |
| `php lucid skill:list` | List registered skills |
| `php lucid make:agent <Name>` | Create a new agent profile in `app/Agents/` |
| `php lucid agent:list` | List registered agents |
| `php lucid agent:run --skill=echo --input='{"message":"hi"}'` | Run a skill |
| `php lucid agent:run --message="Question" [--agent=name]` | Chat with LLM (optional agent profile) |

After creating a migration, edit the file in `database/migrations/` and define the table and columns in `up()` and `down()` (see [Migrations](docs/migrations.md)).

## Documentation

| Topic | File |
|-------|------|
| [Configuration](docs/configuration.md) | Environment variables and database |
| [Routing](docs/routing.md) | Routes, parameters, macros, route loading |
| [Middleware](docs/middleware.md) | Route middleware and attributes |
| [Views](docs/views.md) | Layouts, sections, includes, escaping |
| [Models & ORM](docs/models.md) | Models, Query Builder, relations |
| [Migrations](docs/migrations.md) | Blueprint, Schema, CLI commands |
| [Agents & Skills](docs/agents-and-skills.md) | Skills, agent CLI/HTTP, optional LLM and prompts |

## Requirements

- PHP 8.0+
- Composer
- MySQL/MariaDB (or compatible) for Lucid

## License

See repository for license information.
