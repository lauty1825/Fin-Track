<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../security/session.php';
requireAdmin();

header('Content-Type: application/json');

// Cargar todos los turnos para el admin (excluyendo cancelados)
$stmt = $pdo->query("
    SELECT 
        a.id,
        CONCAT(u.nombre, ' ', u.apellido) AS usuario,
        a.servicio,
        a.fecha AS start,
        a.hora,
        a.status
    FROM appointments a
    INNER JOIN users u ON a.user_id = u.id
    WHERE a.status != 'cancelado'
");

$eventos = [];

foreach ($stmt as $row) {

    // Formato final del evento
    $hora_formateada = substr($row['hora'], 0, 5); // Tomar solo HH:MM
    $eventos[] = [
        "id"    => $row["id"],
        "title" => "{$row['servicio']} - {$hora_formateada} - {$row['usuario']}",
        "start" => $row["start"],
        "color" => colorEvento($row["status"])
    ];
}

echo json_encode($eventos);

// Colores por estado
function colorEvento($estado) {
    switch ($estado) {
        case "pendiente": return "#ffc107"; // amarillo
        case "asistido": return "#28a745";  // verde
        case "cancelado": return "#dc3545"; // rojo
        default: return "#6c757d";          // gris
    }
}
