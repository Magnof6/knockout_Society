<?php
session_start();
require_once 'db_connect.php';
require_once 'function/Matchmaking.php';

// Verifica si el usuario está autenticado
if (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
} else {
    // Si no hay sesión activa, redirige al usuario al login
    header("Location: login.php");
    exit();
}

// Inicializa variables
$matchResult = null;
$errorMessage = "";
$successMessage = "";
$currentFightingStatus = null;
$hasActiveMatch = false; // Estado inicial para emparejado

// Procesa las solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $matchmaking = new Matchmaking($conn);

        if ($_POST['action'] === 'matchmaking') {
            // Buscar pelea (Matchmaking)
            $matchResult = $matchmaking->generateMatchForUser($user_email);

            if ($matchResult) {
                // Actualiza el estado de emparejamiento
                $sql = "UPDATE luchador SET emparejado = 1 WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $user_email);
                $stmt->execute();
                $successMessage = "¡Emparejamiento completado con éxito!";
                $hasActiveMatch = true; // Marca como emparejado
            } else {
                $errorMessage = "No se pudo generar un emparejamiento.";
            }
        } elseif ($_POST['action'] === 'toggle_status') {
            // Alternar estado "Buscando pelea"
            $newState = $_POST['new_state'];
            $sql = "UPDATE luchador SET buscando_pelea = ?, emparejado = 0 WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('is', $newState, $user_email);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $currentFightingStatus = $newState;
                $hasActiveMatch = false; // Restablece el emparejamiento
                $successMessage = $newState ? "Has activado 'Buscando pelea'." : "Has desactivado 'Buscando pelea'.";
            } else {
                $errorMessage = "No se pudo actualizar el estado.";
            }
        }
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Obtener el estado actual de "Buscando pelea" y "Emparejado"
$sql = "SELECT buscando_pelea, emparejado FROM luchador WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();
$luchador = $result->fetch_assoc();

if ($luchador) {
    $currentFightingStatus = $luchador['buscando_pelea'];
    $hasActiveMatch = $luchador['emparejado'];
}

// Consulta para obtener datos del usuario
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
        button {
            padding: 10px 20px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
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
    <div class="header">
        <h1>Buscar pelea</h1>
    </div>

    <div class="matchmaking-container">
        <h2>Hola, <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?> (<?= htmlspecialchars($user['username']) ?>)</h2>
        <p>Estado actual: <strong><?= $currentFightingStatus ? "Activo" : "Desactivado" ?></strong></p>

        <!-- Botón para alternar estado -->
        <form method="POST">
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="new_state" value="<?= $currentFightingStatus ? 0 : 1 ?>">
            <button type="submit"><?= $currentFightingStatus ? "Desactivar" : "Activar" ?> "Buscando pelea"</button>
        </form>

        <!-- Botón para buscar pelea -->
        <?php if ($currentFightingStatus && !$hasActiveMatch): ?>
            <form method="POST">
                <input type="hidden" name="action" value="matchmaking">
                <button type="submit">Buscar pelea</button>
            </form>
        <?php endif; ?>

        <!-- Botón "Empezar pelea" (solo si el emparejamiento ya está completo) -->
        <?php if ($hasActiveMatch): ?>
            <form method="POST" action="start_match.php">
                <button type="submit">Empezar pelea</button>
            </form>
        <?php endif; ?>

        <!-- Mensajes de éxito o error -->
        <?php if ($successMessage): ?>
            <p class="success"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>