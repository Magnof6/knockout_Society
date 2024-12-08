<?php
session_start();
require_once 'db_connect.php'; // Conexión a la base de datos
require __DIR__ . '/vendor/autoload.php'; // Ruta ajustada para autoload.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica que la sesión esté activa
    if (!isset($_SESSION['user_email']) || !isset($_SESSION['username'])) {
        echo "Debes iniciar sesión para enviar una consulta.";
        exit;
    }

    // Obtener datos de la sesión y sanitizar el mensaje
    $email = $_SESSION['user_email'];
    $username = $_SESSION['username'] ?? "Usuario Anónimo";
    $name = $_SESSION['username'] ?? "Usuario Anónimo";
    $message = htmlspecialchars(trim($_POST['message']));

    // Validar el mensaje
    if (empty($message)) {
        echo "Por favor, escribe un mensaje.";
        exit;
    }

    // Insertar datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO consultas (nombre, correo, username, mensaje) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        echo "Error al preparar la consulta: " . $conn->error;
        exit;
    }

    $stmt->bind_param("ssss", $name, $email, $username, $message);

    if ($stmt->execute()) {
        try {
            $mail = new PHPMailer(true);

            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'knockoutsociety338@gmail.com';
            $mail->Password = 'xzbv thsj qmkz rgqc';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configurar remitente y destinatario
            $mail->setFrom('knockoutsociety338@gmail.com', 'Club de la Lucha');
            $mail->addAddress($email, $username);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = "Confirmación de tu consulta";
            $mail->Body = "
                <p>Hola <strong>$username</strong>,</p>
                <p>Gracias por ponerte en contacto con nosotros. Hemos recibido tu mensaje:</p>
                <blockquote>$message</blockquote>
                <p>Nos pondremos en contacto contigo lo antes posible.</p>
                <p>Saludos,<br>Equipo del Club de la Lucha</p>
            ";
            $mail->AltBody = "Hola $username,\n\nGracias por ponerte en contacto con nosotros. Hemos recibido tu mensaje:\n\n$message\n\nNos pondremos en contacto contigo lo antes posible.\n\nSaludos,\nEquipo del Club de la Lucha";

            $mail->send();
            echo "¡Gracias por tu mensaje, $username! Revisa tu correo para una confirmación.";
        } catch (Exception $e) {
            echo "Tu mensaje fue guardado, pero no se pudo enviar la confirmación por correo. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Hubo un error al enviar tu mensaje. Por favor, inténtalo nuevamente.";
    }

    $stmt->close();
    $conn->close();
}
?>
