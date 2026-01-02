<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="icon" href="images/fintrack_logo.jpg" type="image/jpeg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<header>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="login.php">
        <img src="images/fintrack_logo.jpg" alt="FinTrack Logo" width="30" height="30" class="d-inline-block align-text-top rounded-circle me-2">
        FinTrack
      </a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="login.php">Iniciar Sesión</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="register.php">Registrarse</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>