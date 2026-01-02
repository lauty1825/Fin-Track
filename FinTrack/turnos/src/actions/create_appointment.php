<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../security/session.php';
requireLogin();

$user_id = $_SESSION['usuario']['id'] ?? null;

if (!$user_id) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

$fecha = $_POST['fecha'] ?? null;
$hora = $_POST['hora'] ?? null;
$servicio = $_POST['servicio'] ?? null;

if (!$fecha || !$hora || !$servicio) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

// Validación fecha futura
$hoy = date('Y-m-d');
if ($fecha < $hoy) {
    echo json_encode(['error' => 'No podés sacar turnos para días pasados']);
    exit;
}

// Validación de horario (8am a 7pm)
$hora_obj = DateTime::createFromFormat('H:i', $hora);
if (!$hora_obj) {
    echo json_encode(['error' => 'Formato de hora inválido']);
    exit;
}

$hora_int = intval($hora_obj->format('H'));
if ($hora_int < 8 || $hora_int >= 19) {
    echo json_encode(['error' => 'Los turnos solo se pueden sacar entre 8:00 AM y 7:00 PM']);
    exit;
}

// Verificar si ya existe un turno en esa fecha+hora
$check = $pdo->prepare("SELECT id FROM appointments WHERE fecha = ? AND hora = ?");
$check->execute([$fecha, $hora]);
if ($check->rowCount() > 0) {
    echo json_encode(['error' => 'Ese horario ya está reservado']);
    exit;
}

// Insertar turno
$stmt = $pdo->prepare("
    INSERT INTO appointments (user_id, servicio, fecha, hora)
    VALUES (?, ?, ?, ?)
");

$stmt->execute([$user_id, $servicio, $fecha, $hora]);

echo json_encode(['ok' => true]);
