# Models and ORM (Lucid)

## Model class

Models live in `app/Models/` and extend `Core\Lucid\Model`. Each model must define the table name:

```php
namespace App\Models;

use Core\Lucid\Model;

class User extends Model
{
    protected static string $table = 'users';
}
```

You can create models manually or (if the command is wired into the CLI) use a generator; the class is just a table name and optional custom logic.

## Creating and saving

```php
$user = new User([
    'email' => 'user@example.com',
    'name'  => 'Jane',
    'password' => password_hash('secret', PASSWORD_DEFAULT),
]);
$user->save();
```

After `save()` on a new model, the instance gets the auto-increment `id` and is marked as existing. Subsequent `save()` updates the row.

## Finding records

```php
$user = User::find(1);  // returns ?User
```

`find($id)` returns a single model instance or `null`.

## Query Builder (from Model)

Static methods on the model return a `Core\Lucid\QueryBuilder` for the model table:

```php
User::where('email', 'admin@example.com')
User::orderBy('created_at', 'DESC')
User::limit(10)
User::groupBy('status')
```

Chain and execute:

```php
$user = User::where('email', $email)->first();   // ?object
$users = User::where('status', 'active')->get(); // array of objects
```

For model instances instead of plain objects, use:

```php
$users = User::getFromQueryBuilder(User::where('status', 'active'));
```

Or use `User::all()` to get all rows as model instances.

## Relations

- **belongsTo** — this model has a foreign key pointing to the related table.

  ```php
  $user = $post->belongsTo(User::class);           // Post has user_id
  $user = $post->belongsTo(User::class, 'author_id');
  ```

- **hasOne** — one related record (related table has a foreign key to this model).

  ```php
  $profile = $user->hasOne(Profile::class);
  ```

- **hasMany** — many related records.

  ```php
  $posts = $user->hasMany(Post::class);
  ```

By default, the foreign key is inferred from the related (or current) model name (e.g. `user_id`, `post_id`). Pass the second argument to override.

## Query Builder directly

You can use the Query Builder without a model for raw table access:

```php
use Core\Lucid\QueryBuilder;

$rows = QueryBuilder::table('users')
    ->where('active', 1)
    ->orderBy('name', 'ASC')
    ->limit(10)
    ->get();
```

Available methods:

- `where($column, $value)`
- `orderBy($column, $direction)`
- `limit($limit)`
- `groupBy(...$columns)`
- `join($table, $first, $operator, $second)`
- `leftJoin(...)` / `rightJoin(...)`
- `first()` — single row or null
- `get()` — array of rows
- `insert($data)` / `update($data)` (update uses current `where` clauses)

The database connection and configuration are provided by `Core\Lucid\Connection` and the `.env` settings (see [Configuration](configuration.md)).
