<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../security/session.php';
requireLogin();

$user_id = $_SESSION['usuario']['id'];

$stmt = $pdo->prepare("
    SELECT id, servicio, fecha, DATE_FORMAT(hora, '%H:%i') AS hora, status
    FROM appointments
    WHERE user_id = ?
    ORDER BY fecha ASC, hora ASC
");
$stmt->execute([$user_id]);

echo json_encode($stmt->fetchAll());
