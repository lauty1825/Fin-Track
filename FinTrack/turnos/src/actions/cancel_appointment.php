<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../security/session.php';
requireLogin();

$id = $_POST['id'] ?? null;

$update = $pdo->prepare("UPDATE appointments SET status='cancelado' WHERE id=?");
$update->execute([$id]);

echo json_encode(["ok" => true]);
