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

// Inicializa variables para los resultados del matchmaking
$matchResult = null;
$errorMessage = "";
$successMessage = "";
$currentFightingStatus = null;

// Procesa la solicitud de Post 
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
// Consulta SQL para mostrar el nombre y apellido del usuario
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

        <div class="header">
            <div class="menu-container">
                <div id="menu-icon" class="menu-icon" onclick="toggleMenu()">&#9776;</div>
                <h1>Buscar Pelea</h1>
            </div>
            <div class="search-section">
                <label for="search">Buscar perfiles:</label>
                <input type="text" id="search" placeholder="Buscar...">
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
                <li><a href="#">Acerca de</a></li>
                <li><a href="Contacto.php">Contacto</a></li>
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
            <div class="matchmaking-results">
                <h3>¡Emparejamiento generado!</h3>
                <p><strong>Tu luchador:</strong> <?= htmlspecialchars($matchResult['user']['nombre'] . ' ' . $matchResult['user']['apellido']) ?> (<?= $matchResult['user']['username'] ?>)</p>
                <p><strong>Oponente:</strong> <?= htmlspecialchars($matchResult['opponent']['nombre'] . ' ' . $matchResult['opponent']['apellido']) ?> (<?= $matchResult['opponent']['username'] ?>)</p>
            </div>
        <?php endif; ?>
        </div>

        <?php include 'footer.php'; ?>
    </body>
</html>