<?php
session_start();
require_once 'db_connect.php';

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

if (!$user) {
    // Si no se encuentra al usuario
    die("Usuario no encontrado.");
}

// Consulta para determinar si el usuario es luchador
$sql = "SELECT * FROM luchador WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$is_fighter = $result->num_rows > 0;

// Consulta para obtener peleas pasadas si es luchador
$fights = [];
if ($is_fighter) {
    $sql = "SELECT * FROM lucha WHERE id_luchador1 = ? OR id_luchador2 = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_email, $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $fights[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }

        .profile-container {
            background-color: white;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: black;
        }

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

        .fights-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .fights-table th, .fights-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .fights-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <h2>Bienvenido, <?php echo htmlspecialchars($user['nombre']) . " " . htmlspecialchars($user['apellido']); ?></h2>

        <?php if ($is_fighter): ?>
            <!-- Si es luchador, mostrar peleas pasadas -->
            <h3>Peleas Pasadas</h3>
            <?php if (empty($fights)): ?>
                <p>Aún no has luchado.</p>
            <?php else: ?>
                <table class="fights-table">
                    <tr>
                        <th>Fecha</th>
                        <th>Contrincante</th>
                        <th>Ganador</th>
                    </tr>
                    <?php foreach ($fights as $fight): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fight['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($fight['id_luchador1'] == $user_email ? $fight['id_luchador2'] : $fight['id_luchador1']); ?></td>
                            <td><?php echo htmlspecialchars($fight['id_ganador']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        <?php else: ?>
            <!-- Si no es luchador, mostrar opciones adicionales -->
            <div class="button-container">
                <button class="button" onclick="window.location.href='register_fighter.php'">Registrarse como Luchador</button>
                <button class="button" onclick="window.location.href='change_password.php'">Cambiar Contraseña</button>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
