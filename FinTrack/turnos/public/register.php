<?php
require_once __DIR__ . '/components/layout_public_start.php';
?>

<title>FinTrack - Registro</title>

<main class="container mt-5 mb-5">
<div class="col-md-5 mx-auto">
<div class="card shadow p-4 ">

<h3 class="text-center mb-4">Crear Cuenta</h3>

<form id="registerForm" action="../src/actions/process_register.php" method="POST">

<div id="formAlert" class="alert d-none"></div>

<label>Nombre</label>
<input type="text" name="nombre" class="form-control mb-3" required>

<label>Apellido</label>
<input type="text" name="apellido" class="form-control mb-3" required>

<label>Email</label>
<input type="email" name="email" class="form-control mb-3" required>

<label>Teléfono</label>
<input type="text" name="telefono" class="form-control mb-3" required>

<label>Contraseña</label>
<input type="password" id="pass" name="password" class="form-control mb-3" required>

<label>Confirmar Contraseña</label>
<input type="password" id="pass2" name="password2" class="form-control mb-3" required>

<button class="btn btn-primary w-100">Registrarme</button>

</form>

<a href="login.php" class="d-block text-center mt-3">Ya tengo cuenta</a>

</div>
</div>
</main>

<script src="assets/js/register.js"></script>

<?php
require_once __DIR__ . '/components/layout_public_end.php';
?>
