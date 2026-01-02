<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/send_appointment_notification.php';

// Obtener la hora actual
$ahora = new DateTime('now', new DateTimeZone('America/Argentina/Buenos_Aires'));

// Buscar turnos que serán en aproximadamente 3 horas (rango: 3h - 3.08h)
$ahora_timestamp = $ahora->getTimestamp();
$tres_horas_ms = 3 * 3600; // 3 horas en segundos
$ventana_margen = 5 * 60; // 5 minutos de margen para evitar duplicados

// Obtener fecha y hora actual formateada
$fecha_hoy = $ahora->format('Y-m-d');
$hora_actual = $ahora->format('H:i');

// Calcular la hora en la que deben notificarse (3 horas antes)
$ahora_mas_3h = (clone $ahora)->add(new DateInterval('PT3H'));
$hora_a_notificar_inicio = $ahora_mas_3h->format('H:i:00');
$hora_a_notificar_fin = $ahora_mas_3h->add(new DateInterval('PT5M'))->format('H:i:59');

// Query para encontrar turnos que ocurran en 3 horas
$stmt = $pdo->prepare("
    SELECT 
        a.id,
        a.fecha,
        a.hora,
        a.servicio,
        u.email,
        u.nombre,
        u.apellido
    FROM appointments a
    INNER JOIN users u ON a.user_id = u.id
    WHERE a.status = 'pendiente'
    AND DATE_ADD(CONCAT(a.fecha, ' ', a.hora), INTERVAL 0 SECOND) >= NOW()
    AND DATE_ADD(CONCAT(a.fecha, ' ', a.hora), INTERVAL 0 SECOND) < DATE_ADD(NOW(), INTERVAL 3 HOUR + 5 MINUTE)
    AND DATE_ADD(CONCAT(a.fecha, ' ', a.hora), INTERVAL 0 SECOND) >= DATE_ADD(NOW(), INTERVAL 3 HOUR - 5 MINUTE)
    AND NOT EXISTS (
        SELECT 1 FROM notification_log 
        WHERE appointment_id = a.id 
        AND notification_type = 'appointment_reminder'
    )
");

$stmt->execute();
$turnos = $stmt->fetchAll();

// Enviar notificaciones
$enviadas = 0;
foreach ($turnos as $turno) {
    $enviado = sendAppointmentNotification(
        $turno['email'],
        $turno['nombre'],
        $turno['apellido'],
        $turno['servicio'],
        $turno['fecha'],
        $turno['hora'],
        $config
    );

    // Registrar en log de notificaciones
    if ($enviado) {
        try {
            $log = $pdo->prepare("
                INSERT INTO notification_log (appointment_id, notification_type, status, created_at)
                VALUES (?, 'appointment_reminder', 'sent', NOW())
            ");
            $log->execute([$turno['id']]);
            $enviadas++;
        } catch (Exception $e) {
            // Si la tabla no existe aún, ignorar el error
        }
    }
}

// Respuesta
header('Content-Type: application/json');
echo json_encode(['ok' => true, 'enviadas' => $enviadas, 'hora_actual' => $ahora->format('Y-m-d H:i:s')]);
