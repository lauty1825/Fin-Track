<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../mailer/PHPMailer.php';
require_once __DIR__ . '/../mailer/SMTP.php';
require_once __DIR__ . '/../mailer/Exception.php';

function sendAppointmentNotification($email, $nombre, $apellido, $servicio, $fecha, $hora, $config) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $config->smtp->host;
        $mail->SMTPAuth = true;
        $mail->Username = $config->smtp->username;
        $mail->Password = $config->smtp->password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config->smtp->port;

        $mail->setFrom($config->smtp->from_email, $config->smtp->from_name);
        $mail->addAddress($email, "$nombre $apellido");

        // Formatear fecha
        $fecha_formateada = date('d/m/Y', strtotime($fecha));

        $mail->isHTML(true);
        $mail->Subject = 'Recordatorio: Tu turno es en 3 horas';
        $mail->Body = "
            <h2>Recordatorio de Turno</h2>
            <p>Hola <strong>$nombre $apellido</strong>,</p>
            <p>Te recordamos que tienes un turno en <strong>3 horas</strong>:</p>
            <ul>
                <li><strong>Servicio:</strong> $servicio</li>
                <li><strong>Fecha:</strong> $fecha_formateada</li>
                <li><strong>Hora:</strong> $hora</li>
            </ul>
            <p>Por favor, llega 10 minutos antes.</p>
            <p>¡Gracias!</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
