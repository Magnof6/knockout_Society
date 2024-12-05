<?php
session_start();
require_once 'db_connect.php'; // Ensure you have the database connection
require_once 'function/Inserts.php'; // Include the Inserts class

// Verify if the user is logged in
if (!isset($_SESSION['user_email']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Assign the email from the session to a variable
$user_email = $_SESSION['user_email'];

// Instantiate the Inserts class
$inserts = new Inserts($conn);

$password_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Call the changePassword method
    $password_message = $inserts->changePassword($user_email, $current_password, $new_password, $confirm_password);
    if ($password_message["success"]) {
        $_SESSION['user_password'] = $new_password;
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
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('Imagenes/FondoPantalla.webp') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-sizing: border-box;
        }

        h1 {
            margin-bottom: 20px;
        }

        input[type="password"] {
            width: calc(100% - 20px);
            max-width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #218838;
        }

        .back-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
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
    </div>
</body>
</html>
