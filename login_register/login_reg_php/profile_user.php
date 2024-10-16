<?php
require_once 'db_connect.php';
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Consulta SQL para obtener nombre y apellido del usuario
$sql = "SELECT nombre, apellido FROM usuario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos generales para centrar el contenido */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }

        /* Contenedor para el perfil */
        .profile-container {
            background-color: white;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Título */
        h2 {
            margin-bottom: 20px;
            color : black;

        }

        /* Estilos para los botones */
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .button {
            padding: 10px 20px;
            border: none;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            flex: 1;
            margin: 0 5px;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <!-- Contenedor principal del perfil -->
    <div class="profile-container">
        <h2>Perfil de, <?php echo htmlspecialchars($user['nombre']) . " " . htmlspecialchars($user['apellido']); ?></h2>

        <!-- Contenedor de los botones -->
        <div class="button-container">
            <!-- Botón "Registrarse como Luchador" -->
            <button class="button" onclick="window.location.href='register_fighter.php'">Registrarse como Luchador</button>

            <!-- Botón "Cambiar Contraseña" -->
            <button class="button" onclick="window.location.href='change_password.php'">Cambiar Contraseña</button>
        </div>
    </div>

</body>
</html>
