# Configuration

## Environment variables

Valeria loads configuration from a `.env` file at the project root. The loader is invoked in `public/index.php` and in the Lucid CLI (`lucid`).

- Do not commit `.env` to version control. Use `.env.txt` or `.env.example` as a template.
- Lines starting with `#` are comments.
- Format: `KEY=value`. Quotes are optional and stripped.

## Database (Lucid)

The following keys are used by `Core\Lucid\Connection`:

| Variable | Default | Description |
|----------|---------|-------------|
| `DB_CONNECTION` | `mysql` | PDO driver (e.g. `mysql`) |
| `DB_HOST` | `127.0.0.1` | Database host |
| `DB_PORT` | `3306` | Port |
| `DB_DATABASE` | `valeria` | Database name |
| `DB_USERNAME` | `root` | Username |
| `DB_PASSWORD` | (empty) | Password |
| `DB_CHARSET` | `utf8mb4` | Charset for the connection |

Example `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=valeria
DB_USERNAME=root
DB_PASSWORD=secret
DB_CHARSET=utf8mb4
```

## Using config in code

Load the env once (done in `public/index.php` and in `lucid`):

```php
\Core\Env::load(__DIR__ . '/..');  // path to project root or to .env file
```

Read values:

```php
$db = \Core\Env::get('DB_DATABASE', 'valeria');
$debug = \Core\Env::get('APP_DEBUG', false);
```

The second argument is the default when the key is missing.
