<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../security/session.php';
requireAdmin();

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['error' => 'ID de turno no proporcionado']);
    exit;
}

// Actualizar estado a cancelado
$update = $pdo->prepare("UPDATE appointments SET status='cancelado' WHERE id=?");
$update->execute([$id]);

echo json_encode(['ok' => true]);
