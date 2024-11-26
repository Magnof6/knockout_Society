<?php
session_start();
require_once 'db_connect.php'; // Asegúrate de tener la conexión a la base de datos

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_email']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Asigna el email de la sesión a una variable
$user_email = $_SESSION['user_email'];

$sql = "SELECT * FROM usuario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$password_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verifica la contraseña actual con la almacenada
    if (password_verify($current_password, $user['password'])) {
        if ($new_password == $current_password || $confirm_password == $current_password) {
            $password_message = "New password can't be the same as old password"; 
        }
        elseif ($new_password === $confirm_password) {
            // Hashea la nueva contraseña
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Actualiza la contraseña en la base de datos
            $sql = "UPDATE usuario SET password = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashed_password, $user_email);
            if ($stmt->execute()) {
                $password_message = "Password changed successfully.";
            } else {
                $password_message = "Error changing password: " . $stmt->error;
            }
        } else {
            $password_message = "New passwords do not match.";
        }
    } else {
        $password_message = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }

        h2 {
            color: #333;
        }

        form {
            margin-top: 20px;
            max-width: 400px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            color: red;
            margin-top: 20px;
        }

        .success {
            color: green;
        }

        .back-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h2>Cambiar Contraseña</h2>

    <form action="" method="POST">
        <label for="current_password">Contraseña Actual:</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">Nueva Contraseña:</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Confirmar Nueva Contraseña:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" name="change_password">Cambiar Contraseña</button>
    </form>

    <!-- Mensaje de resultado -->
    <?php if (!empty($password_message)) : ?>
        <div class="message <?= strpos($password_message, 'successfully') !== false ? 'success' : '' ?>">
            <?= $password_message; ?>
        </div>
    <?php endif; ?>

    <!-- Botón para volver a profile_user.php -->
    <div class="back-button">
        <form action="profile_user.php" method="GET">
            <button type="submit">Volver al perfil</button>
        </form>
    </div>

</body>
</html>
