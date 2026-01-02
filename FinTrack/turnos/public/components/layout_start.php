<?php 
require_once __DIR__ . '/../../src/security/session.php';
requireLogin();
$usuario = $_SESSION['usuario'];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FinTrack</title>
  <link rel="icon" href="images/fintrack_logo.jpg" type="image/jpeg"> <!-- Favicon -->

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/home.css">
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="d-flex">
  <aside class="bg-dark text-white sidebar p-3">
    <h4 class="text-center mb-3">FinTrack</h4>

    <div class="mb-3 small px-2">
      Hola, <?= htmlspecialchars($usuario['nombre']) ?>
    </div>

    <?php if ($usuario['role'] !== 'admin'): ?>

      <!-- SOLO USUARIO NORMAL -->
      <a href="home.php" class="d-block text-white py-1">📅 Calendario</a>
      <a href="my_appointments.php" class="d-block text-white py-1">📝 Mis turnos</a>

    <?php endif; ?>

    <?php if ($usuario['role'] === 'admin'): ?>

      <!-- SOLO ADMIN -->
      <a href="admin.php" class="d-block text-white py-1">📊 Panel Admin</a>
      
    <?php endif; ?>

    <hr class="border-light">
    <a href="logout.php" class="d-block text-white py-1 mt-2">🚪 Cerrar sesión</a>
  </aside>

  <main class="content-area flex-grow-1 p-4">
