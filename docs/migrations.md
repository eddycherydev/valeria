# Migrations (Lucid)

## Overview

Migrations are PHP files in `database/migrations/`. Each file returns an anonymous object with `up()` and `down()`. The framework tracks executed migrations in a `migrations` table and runs only pending files when you execute `php lucid migrate`.

## CLI commands

From the project root:

| Command | Description |
|---------|-------------|
| `php lucid make:migration create_posts_table` | Creates a new file like `20260308_032800_create_posts_table.php` |
| `php lucid migrate` | Runs all pending migrations (in filename order) |
| `php lucid rollback` | Rolls back the latest batch of migrations (calls `down()`) |

After `make:migration`, edit the new file in `database/migrations/` and define your table in `up()` and drop it (or reverse changes) in `down()`.

## Writing migrations

Use `Core\Lucid\Schema` and `Core\Lucid\Blueprint`:

```php
<?php
use Core\Lucid\Schema;
use Core\Lucid\Blueprint;

return new class {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->text('body');
            $table->integer('user_id');
            $table->timestamps();
            $table->foreign('user_id', 'id', 'users');
        });
    }

    public function down(): void
    {
        Blueprint::drop('posts');
    }
};
```

## Blueprint API

| Method | Example | Description |
|--------|---------|-------------|
| `increments($name)` | `$table->increments('id')` | Integer primary key, auto-increment |
| `string($name, $length)` | `$table->string('email', 150)` | VARCHAR |
| `integer($name)` | `$table->integer('user_id')` | INTEGER |
| `text($name)` | `$table->text('body')` | TEXT |
| `boolean($name)` | `$table->boolean('active')` | TINYINT(1) |
| `decimal($name, $p, $s)` | `$table->decimal('price', 8, 2)` | DECIMAL |
| `date($name)` | `$table->date('birthday')` | DATE |
| `datetime($name)` | `$table->datetime('published_at')` | DATETIME |
| `timestamps()` | `$table->timestamps()` | `created_at`, `updated_at` (DATETIME, default CURRENT_TIMESTAMP) |
| `foreign($column, $references, $on)` | `$table->foreign('user_id', 'id', 'users')` | Foreign key to another table |

**Dropping tables:**

- `Blueprint::drop('table_name')` — drops the table.
- `Blueprint::dropIfExists('table_name')` — drops only if it exists.

`Schema::create($tableName, callable)` creates the table using the Blueprint. Run migrations with `php lucid migrate` after editing.

## Database

Migrations use the same database connection as the rest of the app (see [Configuration](configuration.md)). Ensure `.env` is set correctly before running `migrate` or `rollback`.
