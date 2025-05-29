<?php section('title') ?>
    Iniciar Sesión
<?php endsection() ?>

<?php section('content') ?>
    <h2>Acceder a tu cuenta</h2>

    <?php if (!empty($error)): ?>
        <div style="color: red;">
            <?= e($error) ?>
        </div>
    <?php endif ?>

    <form method="post" action="/login">
        <div>
            <label for="email">Correo electrónico:</label>
            <input type="email" name="email" id="email" value="<?= e($old['email'] ?? '') ?>" required>
        </div>

        <div>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>
        </div>

        <div>
            <button type="submit">Iniciar sesión</button>
        </div>
    </form>
<?php endsection() ?>