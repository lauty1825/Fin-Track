<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
        header("Location: home.php");
        exit;
    }
}

function loginUser($usuario) {
    $_SESSION['usuario'] = [
        'id'        => $usuario['id'],
        'nombre'    => $usuario['nombre'],
        'apellido'  => $usuario['apellido'],
        'email'     => $usuario['email'],
        'telefono'  => $usuario['telefono'],
        'role'      => $usuario['role'],
        'verified'  => $usuario['is_verified']
    ];
}
