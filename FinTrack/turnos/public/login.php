<?php
require_once __DIR__ . '/components/layout_public_start.php';
?>

<title>FinTrack - Iniciar Sesión</title>

<main class="container mt-5 mb-5">
    <div class="col-md-4 mx-auto">
        <div class="card shadow p-4">

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success">
                    ¡Registro exitoso! Revisa tu correo electrónico para verificar tu cuenta.
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error']) && $_GET['error'] === 'not_verified'): ?>
                <div class="alert alert-warning">
                    Tu cuenta no ha sido verificada. Por favor, revisa tu correo electrónico.
                </div>
            <?php endif; ?>

            <h3 class="text-center mb-4">Iniciar Sesión</h3>

            <form action="../src/actions/process_login.php" method="POST">
                <label>Email</label>
                <input type="email" name="email" class="form-control mb-3" required>

                <label>Contraseña</label>
                <input type="password" name="password" class="form-control mb-3" required>

                <button class="btn btn-primary w-100">Ingresar</button>
            </form>

            <a href="register.php" class="d-block text-center mt-3">Crear cuenta</a>

        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/components/layout_public_end.php';
?>
