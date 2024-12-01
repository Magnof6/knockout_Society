<?php
session_start();
require_once 'db_connect.php';
require_once 'function/Matchmaking.php';

// Verifica si el usuario está autenticado
if (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
} else {
    header("Location: login.php");
    exit();
}

$matchResult = null;
$errorMessage = "";
$successMessage = "";
$currentFightingStatus = null;

// Procesa las solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $matchmaking = new Matchmaking($conn);

        if ($_POST['action'] === 'matchmaking') {
            $matchResult = $matchmaking->generateMatchForUser($user_email);
            $successMessage = "¡Se ha generado un emparejamiento!";
        } elseif ($_POST['action'] === 'toggle_status') {
            $newState = $_POST['new_state'];
            $toggleResult = $matchmaking->alternarEstadoBP($user_email, $newState);

            if ($toggleResult['success']) {
                $successMessage = $toggleResult['message'];
            } else {
                $errorMessage = $toggleResult['message'];
            }
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

// Obtener el estado actual de "Buscando Pelea"
$sql = "SELECT buscando_pelea FROM luchador WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();
$luchador = $result->fetch_assoc();

if ($luchador) {
    $currentFightingStatus = $luchador['buscando_pelea'];
}

// Obtener datos del usuario para mostrar en la interfaz
$sql = "SELECT nombre, apellido, username FROM usuario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fight Matchmaking</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            margin: 5px 0;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Fight Matchmaking</h1>
            <p>Hola, <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?> (<?= htmlspecialchars($user['username']) ?>)</p>
        </div>

        <p>Estado actual: <strong><?= $currentFightingStatus ? "Activo" : "Desactivado" ?></strong></p>

        <form method="POST">
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="new_state" value="<?= $currentFightingStatus ? 0 : 1 ?>">
            <button type="submit"><?= $currentFightingStatus ? "Desactivar" : "Activar" ?> "Buscando Pelea"</button>
        </form>

        <?php if ($currentFightingStatus): ?>
            <form method="POST">
                <input type="hidden" name="action" value="matchmaking">
                <button type="submit">Buscar Pelea</button>
            </form>
        <?php else: ?>
            <p class="error">Debes activar "Buscando Pelea" antes de buscar una pelea.</p>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <p class="success"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <?php if ($matchResult): ?>
            <div class="matchmaking">
                <h3>¡Emparejamiento generado!</h3>
                <p><strong>Tu luchador:</strong> <?= htmlspecialchars($matchResult['user']['nombre'] . ' ' . $matchResult['user']['apellido']) ?> (<?= $matchResult['user']['username'] ?>)</p>
                <p><strong>Oponente:</strong> <?= htmlspecialchars($matchResult['opponent']['nombre'] . ' ' . $matchResult['opponent']['apellido']) ?> (<?= $matchResult['opponent']['username'] ?>)</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
