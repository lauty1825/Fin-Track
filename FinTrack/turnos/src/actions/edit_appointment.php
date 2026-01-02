<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../security/session.php';
requireAdmin();

$id = $_POST['id'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$hora = $_POST['hora'] ?? null;
$servicio = $_POST['servicio'] ?? null;

if (!$id || !$fecha || !$hora || !$servicio) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

// Validación fecha futura
$hoy = date('Y-m-d');
if ($fecha < $hoy) {
    echo json_encode(['error' => 'No podés editar a días pasados']);
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
    echo json_encode(['error' => 'Los turnos solo deben estar entre 8:00 AM y 7:00 PM']);
    exit;
}

// Verificar si ya existe un turno en esa fecha+hora (excepto el actual)
$check = $pdo->prepare("SELECT id FROM appointments WHERE fecha = ? AND hora = ? AND id != ?");
$check->execute([$fecha, $hora, $id]);
if ($check->rowCount() > 0) {
    echo json_encode(['error' => 'Ese horario ya está reservado']);
    exit;
}

// Actualizar turno
$stmt = $pdo->prepare("
    UPDATE appointments 
    SET fecha = ?, hora = ?, servicio = ?
    WHERE id = ?
");

$stmt->execute([$fecha, $hora, $servicio, $id]);

echo json_encode(['ok' => true]);
