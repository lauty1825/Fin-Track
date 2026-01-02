<?php
require_once __DIR__ . '/../security/session.php';
requireAdmin();

// Cargar configuración (esto deja disponible $config y $pdo)
$config = require __DIR__ . '/../config.php';

// Total de turnos
$total = $pdo->query("SELECT COUNT(*) AS c FROM appointments")->fetchColumn();

// Total pendientes
$total_pendientes = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pendiente'")->fetchColumn();

// Pendientes por servicio
$pending_by_service = $pdo->query("SELECT servicio, COUNT(*) AS total FROM appointments WHERE status = 'pendiente' GROUP BY servicio ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);

// Totales por servicio (para el gráfico)
$totals_by_service = $pdo->query("SELECT servicio, COUNT(*) AS total FROM appointments GROUP BY servicio ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);
$labels = array_column($totals_by_service, 'servicio');
$values = array_map(function($r){ return (int)$r['total']; }, $totals_by_service);

// Porcentaje asistidos
$attended = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'asistido'")->fetchColumn();
$pct_attended = $total > 0 ? round(($attended / $total) * 100, 2) : 0;

// Hora promedio (en segundos -> tiempo)
$avg_seconds = $pdo->query("SELECT AVG(TIME_TO_SEC(hora)) AS avg_sec FROM appointments WHERE hora IS NOT NULL")->fetchColumn();
$avg_time = $avg_seconds ? gmdate('H:i:s', (int)$avg_seconds) : null;

// Hora pico (HOUR con más pendientes)
$peak = $pdo->query("SELECT HOUR(hora) AS hora, COUNT(*) AS c FROM appointments WHERE status = 'pendiente' GROUP BY HOUR(hora) ORDER BY c DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Últimos 7 días - conteo diario (solo días pasados hasta hoy)
$daily = $pdo->query("SELECT fecha AS day, COUNT(*) AS total FROM appointments WHERE fecha <= CURDATE() AND fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY fecha ORDER BY fecha ASC")->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode([
	'total' => (int)$total,
	'total_pendientes' => (int)$total_pendientes,
	'pending_by_service' => $pending_by_service,
	'labels' => $labels,
	'values' => $values,
	'pct_attended' => $pct_attended,
	'avg_time' => $avg_time,
	'peak_hour' => $peak,
	'daily_last_7' => $daily
]);
