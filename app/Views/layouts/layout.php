<!DOCTYPE html>
<html>
<head>
    <title><?php yieldSection('title') ?></title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
<body>
    <header><h1>Mi sitio</h1></header>
    <main><?php yieldSection('content') ?></main>
</body>
</html>