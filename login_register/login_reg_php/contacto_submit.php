<?php
session_start();
require_once 'db_connect.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica que la sesión esté activa
    if (!isset($_SESSION['user_email']) || !isset($_SESSION['username'])) {
        echo "Debes iniciar sesión para enviar una consulta.";
        exit;
    }

    // Obtén los datos de la sesión
    $username = $_SESSION['username'];
    $email = $_SESSION['user_email'];
    $name = $_SESSION['username']; // Ajusta esto si tienes un campo 'nombre' en la sesión
    $message = htmlspecialchars(trim($_POST['message']));

    if (empty($message)) {
        echo "Por favor, escribe un mensaje.";
        exit;
    }

    // Inserta los datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO consultas (nombre, correo, username, mensaje) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        echo "Error al preparar la consulta: " . $conn->error;
        exit;
    }

    $stmt->bind_param("ssss", $name, $email, $username, $message);

    if ($stmt->execute()) {
        echo "¡Gracias por tu mensaje, $username! Nos pondremos en contacto contigo pronto.";
    } else {
        echo "Hubo un error al enviar tu mensaje. Por favor, inténtalo nuevamente.";
    }

    $stmt->close();
    $conn->close();
}
?>
