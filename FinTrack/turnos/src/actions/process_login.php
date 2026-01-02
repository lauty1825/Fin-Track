<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../security/session.php';

$email    = trim($_POST['email'] ?? "");
$password = trim($_POST['password'] ?? "");

// Buscar usuario
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../../public/login.php?error=invalid");
    exit;
}

// Verificar contraseña
if (!password_verify($password, $user['password_hash'])) {
    header("Location: ../../public/login.php?error=invalid");
    exit;
}

// Verificar si está confirmado (si usás verificación)
if ($user['is_verified'] == 0) {
    header("Location: ../../public/login.php?error=not_verified");
    exit;
}

// Login
loginUser($user);

// Redirigir según el rol
if ($user['role'] === 'admin') {
    header("Location: ../../public/admin.php");
} else {
    header("Location: ../../public/home.php");
}

exit;
