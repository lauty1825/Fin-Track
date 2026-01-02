<?php
require_once __DIR__ . '/../config.php';

$stmt = $pdo->query("
    SELECT 
        id, 
        fecha AS start, 
        CONCAT(servicio, ' - ', DATE_FORMAT(hora, '%H:%i')) AS title
    FROM appointments
    WHERE status != 'cancelado'
");

$eventos = $stmt->fetchAll();
echo json_encode($eventos);
