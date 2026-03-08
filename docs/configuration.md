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

## AI / Agent (optional)

**Secrets** — Put only the API key in `.env`:

| Variable | Description |
|----------|-------------|
| `OPENAI_API_KEY` | API key for OpenAI (or compatible) |

**Models and providers** — Edit `config/ai.php` to change the default provider, model, base URL, and temperature. That file is the single place to configure:

- `default` — Which provider to use (e.g. `openai`, `openai_gpt4`)
- `temperature` — Default 0.7 (0 = deterministic, 1 = more creative)
- `providers` — List of providers, each with `class`, `model`, `base_url`, and `env_key` (the .env variable for the API key)

Example: to use a different model, add or edit a provider in `config/ai.php`:

```php
'providers' => [
    'openai' => [
        'class' => \Core\AI\OpenAILLM::class,
        'model' => 'gpt-4o-mini',
        'base_url' => 'https://api.openai.com',
        'env_key' => 'OPENAI_API_KEY',
    ],
    'openai_gpt4' => [
        'class' => \Core\AI\OpenAILLM::class,
        'model' => 'gpt-4o',
        'base_url' => 'https://api.openai.com',
        'env_key' => 'OPENAI_API_KEY',
    ],
],
```

Then set `'default' => 'openai_gpt4'` to use GPT-4 by default. The agent uses `Config::createLLM()` so it always reads from `config/ai.php`; the API key is still read from `.env` via `env_key`.

**HTTP** — `GET /api/ai/config` returns the current AI config (default provider, temperature, and provider list with model/base_url only; no API keys).

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
