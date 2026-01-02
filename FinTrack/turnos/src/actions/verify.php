<?php
$config = require __DIR__ . '/../config.php';
$dbcfg = $config->db;
$pdo = new PDO("mysql:host={$dbcfg->host};dbname={$dbcfg->name};charset=utf8mb4", $dbcfg->user, $dbcfg->pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
if (!$token || !$email) { echo "Parámetros inválidos."; exit; }

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND verification_token = ? AND is_verified = 0");
$stmt->execute([$email, $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) { echo "Token inválido o cuenta ya verificada."; exit; }

$upd = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
$upd->execute([$user['id']]);
echo "Cuenta verificada. Podés iniciar sesión.";
