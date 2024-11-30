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
$matchResults = [];
$errorMessage = "";

// Procesa la solicitud de matchmaking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'matchmaking') {
    try {
        // Conexión a la base de datos ya establecida en db_connect.php
        $matchmaking = new Matchmaking($conn);
        $matchResults = $matchmaking->generateMatches();
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

// Consulta SQL para mostrar el nombre y apellido del usuario
$sql = "SELECT nombre, apellido FROM usuario WHERE email = ?";
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
    <title>Buscar Pelea</title>
    <link rel="stylesheet" href="./styles.css">
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
        </ul>
    </div>

    <div class="matchmaking-container">
        <h2>Hola, <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>. ¿Listo para buscar una pelea?</h2>

        <!-- Formulario para activar el matchmaking -->
        <form method="POST">
            <input type="hidden" name="action" value="matchmaking">
            <button type="submit" class="matchmaking-button">Generar Matchmaking</button>
        </form>

        <!-- Mostrar resultados del matchmaking -->
        <div class="matchmaking-results">
            <?php if (!empty($matchResults)): ?>
                <h3>Resultados del Matchmaking:</h3>
                <ul>
                    <?php foreach ($matchResults as $index => $match): ?>
                        <li>Match <?= $index + 1 ?>: <?= htmlspecialchars($match[0]['email']) ?> vs <?= htmlspecialchars($match[1]['email']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php elseif ($errorMessage): ?>
                <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
