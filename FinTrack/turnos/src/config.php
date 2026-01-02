<?php

// Configuración centralizada
$config = (object)[];

// Datos de conexión
$config->db = (object)[
    'host' => '127.0.0.1',
    'name' => 'turnos_db',
    'user' => 'root',
    'pass' => ''
];

// Conexión PDO (disponible en $pdo)
try {
    $pdo = new PDO(
        "mysql:host={$config->db->host};dbname={$config->db->name};charset=utf8mb4",
        $config->db->user,
        $config->db->pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// SMTP CONFIG
$config->smtp = (object)[
    'host'       => 'smtp.gmail.com',
    'port'       => 587,
    'username'   => 'lautaro.roche123@gmail.com',
    'password'   => 'dfoq fldg qsee rdtg',
    'from_email' => 'lautaro.roche123@gmail.com',
    'from_name'  => 'FinTrack'
];

// Devolver configuración si el archivo se incluye como `require __DIR__ . '/config.php'`
return $config;
