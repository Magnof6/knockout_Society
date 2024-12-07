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
$readyToStart = false; // Indica si ambos luchadores están listos para empezar la pelea

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
            $sql = "UPDATE luchador SET buscando_pelea = ?, emparejado = 0, empezarPelea = 0 WHERE email = ?";
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
        } elseif ($_POST['action'] === 'start_fight') {
            // Marcar el usuario como listo para empezar la pelea
            $sql = "UPDATE luchador SET empezarPelea = 1 WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $user_email);
            $stmt->execute();

            // Verificar si ambos luchadores están listos
            $sql = "SELECT COUNT(*) as readyCount 
                    FROM luchador 
                    WHERE emparejado = 1 AND empezarPelea = 1";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

            if ($row['readyCount'] >= 2) {
                $readyToStart = true; // Ambos luchadores están listos
                $successMessage = "¡Ambos luchadores están listos! La pelea puede comenzar.";
            } else {
                $successMessage = "Estás listo para la pelea. Esperando al oponente...";
            }
        }
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Obtener el estado actual de "Buscando pelea", "Emparejado" y "Empezar pelea"
$sql = "SELECT buscando_pelea, emparejado, empezarPelea FROM luchador WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();
$luchador = $result->fetch_assoc();

if ($luchador) {
    $currentFightingStatus = $luchador['buscando_pelea'];
    $hasActiveMatch = $luchador['emparejado'];
    $readyToStart = ($luchador['empezarPelea'] == 1);
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
        <div class="menu-container">
            <div id="menu-icon" class="menu-icon" onclick="toggleMenu()">&#9776;</div>
            <h1>Buscar Pelea</h1>
        </div>
        <div class="search-section">
            <!-- Add search functionality here if needed -->
        </div>
        <div class="profile-dropdown">
            <button class="profile-button">Perfil ▼</button>
            <div class="profile-content">
                <a href="profile_user.php">Ver Perfil</a>
                <a href="#">Configuraciones</a>
                <a href="logout.php">Cerrar sesión</a>
            </div>
        </div>
    </div>
    <div id="menu" class="menu">
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="Fight.php">Buscar Pelea</a></li>
            <li><a href="Watch.php">Ver Peleas</a></li>
            <li><a href="Ranking.php">Ranking</a></li>
            <li><a href="apuestaHTML.php">Apuestas</a></li>
        </ul>
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

        <!-- Botón "Empezar pelea" -->
        <?php if ($hasActiveMatch && !$readyToStart): ?>
            <form method="POST">
                <input type="hidden" name="action" value="start_fight">
                <button type="submit">Empezar pelea</button>
            </form>
        <?php elseif ($readyToStart): ?>
            <p class="success">¡La pelea puede comenzar!</p>
        <?php endif; ?>

        <!-- Mensajes de éxito o error -->
        <?php if ($successMessage): ?>
            <p class="success"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
