<?php
require_once __DIR__ . '/../security/session.php';
requireAdmin();

// cargar config (y $pdo)
$config = require __DIR__ . '/../config.php';

// Consultas (mismas que en admin_load_stats)
$total = (int)$pdo->query("SELECT COUNT(*) AS c FROM appointments")->fetchColumn();
$total_pendientes = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pendiente'")->fetchColumn();
$pending_by_service = $pdo->query("SELECT servicio, COUNT(*) AS total FROM appointments WHERE status = 'pendiente' GROUP BY servicio ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);
$attended = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'asistido'")->fetchColumn();
$pct_attended = $total > 0 ? round(($attended / $total) * 100, 2) : 0;
$avg_seconds = $pdo->query("SELECT AVG(TIME_TO_SEC(hora)) AS avg_sec FROM appointments WHERE hora IS NOT NULL")->fetchColumn();
$avg_time = $avg_seconds ? gmdate('H:i:s', (int)$avg_seconds) : 'N/A';
$peak = $pdo->query("SELECT HOUR(hora) AS hora, COUNT(*) AS c FROM appointments WHERE status = 'pendiente' GROUP BY HOUR(hora) ORDER BY c DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$daily = $pdo->query("SELECT fecha AS day, COUNT(*) AS total FROM appointments WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY fecha ORDER BY fecha ASC")->fetchAll(PDO::FETCH_ASSOC);

// Preparar líneas de texto para el PDF
$lines = [];
$lines[] = "Reporte de Estadísticas - Turnos";
$lines[] = "Fecha: " . date('Y-m-d H:i:s');
$lines[] = "";
$lines[] = "Total de turnos: $total";
$lines[] = "Turnos pendientes: $total_pendientes";
$lines[] = "Porcentaje asistidos: $pct_attended%";
$lines[] = "Hora promedio (todos): $avg_time";
if ($peak && isset($peak['hora'])) {
    $lines[] = "Hora pico (pendientes): " . $peak['hora'] . ":00 - " . $peak['c'] . " turnos";
}
$lines[] = "";
$lines[] = "Pendientes por servicio:";
foreach ($pending_by_service as $p) {
    $lines[] = " - {$p['servicio']}: {$p['total']}";
}
$lines[] = "";
$lines[] = "Últimos 7 días (por fecha):";
foreach ($daily as $d) {
    $lines[] = " - {$d['day']}: {$d['total']}";
}

// Construir un PDF mínimo y válido con texto simple
function pdf_escape($s) {
    return str_replace(['\\','(' ,')'], ['\\\\','\\(','\\)'], $s);
}

$texts = [];
foreach ($lines as $ln) {
    $texts[] = "(" . pdf_escape($ln) . ") Tj";
}

$stream_lines = [];
$stream_lines[] = "BT";
$stream_lines[] = "/F1 12 Tf";
$stream_lines[] = "72 720 Td";
// write first line
if (count($texts) > 0) {
    $stream_lines[] = $texts[0];
}
for ($i = 1; $i < count($texts); $i++) {
    $stream_lines[] = "0 -14 Td";
    $stream_lines[] = $texts[$i];
}
$stream_lines[] = "ET";

$stream = implode("\n", $stream_lines) . "\n";

// Build PDF objects
$objects = [];
$objects[] = "%PDF-1.4\n"; // will be object 1 as header

$obj2 = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
$objects[] = $obj2;

$obj3 = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
$objects[] = $obj3;

$contents_obj = "4 0 obj\n<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream\nendobj\n";
$objects[] = $contents_obj;

$page_obj = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n";
$objects[] = $page_obj;

$font_obj = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
$objects[] = $font_obj;

// Combine and generate xref
$out = "%PDF-1.4\n";
$offsets = [];
$pos = strlen($out);
$out .= $obj2; $offsets[] = $pos; $pos += strlen($obj2);
$out .= $obj3; $offsets[] = $pos; $pos += strlen($obj3);
$out .= $contents_obj; $offsets[] = $pos; $pos += strlen($contents_obj);
$out .= $page_obj; $offsets[] = $pos; $pos += strlen($page_obj);
$out .= $font_obj; $offsets[] = $pos; $pos += strlen($font_obj);

$xref_pos = $pos;
$out .= "xref\n0 " . (count($offsets) + 1) . "\n";
$out .= sprintf("%010d %05d f \n", 0, 65535);
foreach ($offsets as $o) {
    $out .= sprintf("%010d %05d n \n", $o, 0);
}

$out .= "trailer\n<< /Size " . (count($offsets) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref_pos . "\n%%EOF";

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="estadisticas_turnos_' . date('Ymd') . '.pdf"');
echo $out;
exit;
