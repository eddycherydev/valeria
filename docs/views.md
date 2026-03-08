# Views

## Rendering

Views are PHP files under `app/Views/`. Use `Core\View` to render them.

**Without layout:**

```php
View::render('home/home', ['name' => 'Valeria']);
```

This includes `app/Views/home/home.php` and passes `$name` into the template. The result is output directly.

**With layout:**

```php
View::render('home/home', ['name' => 'Valeria'], 'layouts/layout');
```

The view `home/home` is rendered first; its output is captured. Then the layout `layouts/layout` is rendered. The layout can inject the view content using sections (see below).

## Sections and layout

Layouts typically define placeholders (sections) that child views fill.

**In the layout** (`app/Views/layouts/layout.php`):

```php
<!DOCTYPE html>
<html>
<head>
    <title><?php yieldSection('title') ?></title>
</head>
<body>
    <main><?php yieldSection('content') ?></main>
</body>
</html>
```

**In the view** (`app/Views/home/home.php`):

```php
<?php section('title') ?>Home<?php endsection() ?>
<?php section('content') ?>
<h1>Hello, <?= e($name) ?></h1>
<?php endsection() ?>
```

Helper functions (from `core/helpers.php`):

- `section($name)` — start a section.
- `endsection()` — end the current section and store it.
- `yieldSection($name)` — output the content of a section (used in the layout).
- `includeView($view, $data)` — include another view file.
- `e($value)` — escape for HTML (uses `View::e()`).

Sections are stored per render; when you use a layout, the view is rendered first and its sections are then available when the layout is rendered.

## Escaping

Always escape user-facing data in HTML to avoid XSS:

```php
<?= e($user->name) ?>
```

`e()` is `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.

## Paths

View names are relative to `app/Views/` and use forward slashes without the `.php` extension:

- `home/home` → `app/Views/home/home.php`
- `layouts/layout` → `app/Views/layouts/layout.php`
- `Auth/login` → `app/Views/Auth/login.php`

If the file does not exist, `View::render()` throws an exception.
