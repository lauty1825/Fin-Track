<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../mailer/PHPMailer.php';
require_once __DIR__ . '/../mailer/SMTP.php';
require_once __DIR__ . '/../mailer/Exception.php';

function sendVerificationEmail($email, $nombre, $apellido, $link, $config) {
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

    $mail->isHTML(true);
    $mail->Subject = 'Verificá tu cuenta FinTrack';
    $mail->Body = "<p>Hola $nombre.</p><p>Confirmá tu cuenta: <a href='$link'>$link</a></p>";

    $mail->send(); return true;
  } catch (Exception $e) { return false; }
}
