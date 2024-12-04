<?php
require_once 'db_connect.php'; // Incluye la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (empty($name) || empty($email) || empty($message)) {
        echo "Por favor, completa todos los campos.";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO consultas (nombre, correo, mensaje) VALUES (?, ?, ?)");
    if ($stmt === false) {
        echo "Error al preparar la consulta: " . $conn->error;
        exit;
    }

    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        echo "¡Gracias por tu consulta, $name! Nos pondremos en contacto contigo pronto.";
    } else {
        echo "Hubo un error al enviar tu consulta. Por favor, inténtalo nuevamente.";
    }

    $stmt->close();
    $conn->close();
}
?>
