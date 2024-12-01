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
            // Buscar pelea (Matchmaking)
            $matchResult = $matchmaking->generateMatchForUser($user_email);
            $successMessage = "¡Se ha generado un emparejamiento!";
        } elseif ($_POST['action'] === 'toggle_status') {
            // Alternar estado "Buscando Pelea"
            $newState = $_POST['new_state'];
            $toggleResult = $matchmaking->alternarEstadoBP($user_email, $newState);

            if (is_array($toggleResult) && !$toggleResult['success']) {
                $errorMessage = $toggleResult['message'];
            } else {
                $successMessage = "Estado cambiado correctamente.";
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
        .container { max-width: 800px; margin: auto; padding: 20px; font-family: Arial, sans-serif; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .matchmaking { margin-top: 20px; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; border: none; border-radius: 5px; background-color: #007bff; color: white; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Fight Matchmaking</h1>
            <p>Hola, <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?> (<?= htmlspecialchars($user['username']) ?>)</p>
        </div>

        <!-- Estado actual de "Buscando Pelea" -->
        <p>Estado actual: <strong><?= $currentFightingStatus ? "Activo" : "Desactivado" ?></strong></p>

        <!-- Botón para alternar estado -->
        <form method="POST">
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="new_state" value="<?= $currentFightingStatus ? 0 : 1 ?>">
            <button type="submit"><?= $currentFightingStatus ? "Desactivar" : "Activar" ?> "Buscando Pelea"</button>
        </form>

        <!-- Botón para buscar pelea -->
        <?php if ($currentFightingStatus): ?>
            <form method="POST">
                <input type="hidden" name="action" value="matchmaking">
                <button type="submit">Buscar Pelea</button>
            </form>
        <?php else: ?>
            <p class="error">Debes activar "Buscando Pelea" antes de buscar una pelea.</p>
        <?php endif; ?>

        <!-- Mostrar mensajes de éxito o error -->
        <?php if ($successMessage): ?>
            <p class="success"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <!-- Mostrar resultados del matchmaking -->
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
