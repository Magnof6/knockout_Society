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

$email_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_email'])) {
    $current_password = $_POST['current_password'];
    $new_email = $_POST['new_email'];
    $confirm_email = $_POST['confirm_email'];

    // Call the changeEmail method
    $result = $inserts->changeEmail($user_email, $current_password, $new_email, $confirm_email);
    $email_message = $result["message"];

    // Update the session with the new email if the change was successful
    if ($result["success"]) {
        $_SESSION['user_email'] = $new_email;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Email</title>
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

        h2 {
            color: #333;
        }

        form {
            margin-top: 20px;
            max-width: 400px;
            width: 100%;
        }

        label {
            display: block;
            margin-bottom: 8px;
            text-align: left;
        }

        input[type="password"], input[type="email"] {
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
    <div class="container">
        <h2>Cambiar Email</h2>

        <form action="" method="POST">
            <label for="current_password">Contraseña Actual:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_email">Nuevo Email:</label>
            <input type="email" id="new_email" name="new_email" required>

            <label for="confirm_email">Confirmar Nuevo Email:</label>
            <input type="email" id="confirm_email" name="confirm_email" required>

            <button type="submit" name="change_email">Cambiar Email</button>
        </form>

        <!-- Mensaje de resultado -->
        <?php if (!empty($email_message)) : ?>
            <div class="message <?= strpos($email_message, 'successfully') !== false ? 'success' : '' ?>">
                <?= $email_message; ?>
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
