<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../security/session.php';

// Destruir cualquier sesión existente antes de registrar un nuevo usuario.
// Esto evita que se mantenga la sesión de un usuario anterior.
session_unset();
session_destroy();

// Recibir datos
$nombre   = trim($_POST['nombre'] ?? "");
$apellido = trim($_POST['apellido'] ?? "");
$email    = trim($_POST['email'] ?? "");
$telefono = trim($_POST['telefono'] ?? "");
$password = trim($_POST['password'] ?? "");

// Validación básica
if ($nombre === "" || $apellido === "" || $email === "" || $telefono === "" || $password === "") {
    header("Location: ../../public/register.php?error=empty");
    exit;
}

// ¿Email repetido?
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    header("Location: ../../public/register.php?error=email_exists");
    exit;
}

// Hash de contraseña
$hash = password_hash($password, PASSWORD_DEFAULT);

// Crear token
$token = bin2hex(random_bytes(32));

// Insertar usuario como NO verificado y con token
$stmt = $pdo->prepare("
    INSERT INTO users (nombre, apellido, email, telefono, password_hash, role, is_verified, verification_token)
    VALUES (?, ?, ?, ?, ?, 'user', 0, ?) 
");

$stmt->execute([$nombre, $apellido, $email, $telefono, $hash, $token]);

// Enviar email de verificación (si está configurado)
require_once __DIR__ . '/send_verification_email.php';
$link = "http://localhost/turnos/src/actions/verify.php?token={$token}&email=" . urlencode($email);
sendVerificationEmail($email, $nombre, $apellido, $link, $config);

// Redirigir al login y pedir que revise su email
header("Location: ../../public/login.php?registered=1");
exit;
