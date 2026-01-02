<?php
require_once __DIR__ . '/../security/session.php';
requireAdmin();

$config = require __DIR__ . '/../config.php';
$dbcfg = $config->db;
$pdo = new PDO("mysql:host={$dbcfg->host};dbname={$dbcfg->name};charset=utf8mb4", $dbcfg->user, $dbcfg->pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

$stmt = $pdo->query("SELECT a.id,u.nombre,u.apellido,a.servicio,a.fecha,DATE_FORMAT(a.hora, '%H:%i') AS hora,a.status FROM appointments a JOIN users u ON u.id=a.user_id ORDER BY a.fecha,a.hora");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
