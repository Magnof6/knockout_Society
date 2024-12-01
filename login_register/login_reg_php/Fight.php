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
        } elseif ($_POST['action'] === 'finish_match') {
            $matchId = $_POST['match_id'];
            $winnerEmail = $_POST['winner_email'];
            $matchmaking->finishMatch($matchId, $winnerEmail);
            $successMessage = "La lucha ha finalizado.";
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

// Consulta SQL para mostrar el estado actual del luchador
$sql = "SELECT buscando_pelea FROM luchador WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();
$luchador = $result->fetch_assoc();

if ($luchador) {
    $currentFightingStatus = $luchador['buscando_pelea'];
}

// Consulta SQL para obtener el nombre y apellido del usuario
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
</head>
<body>
    <div class="header">
        <h1>Fight Matchmaking</h1>
        <p>Estado actual: <strong><?= $currentFightingStatus ? "Activo" : "Desactivado" ?></strong></p>
        <form method="POST">
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="new_state" value="<?= $currentFightingStatus ? 0 : 1 ?>">
            <button type="submit"><?= $currentFightingStatus ? "Desactivar" : "Activar" ?></button>
        </form>
    </div>

    <div class="matchmaking">
        <h2>Realizar búsqueda:</h2>
        <form method="POST">
            <input type="hidden" name="action" value="matchmaking">
            <button type="submit">Buscar Pelea</button>
        </form>

        <?php if ($successMessage): ?>
            <p class="success"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <?php if ($matchResult): ?>
            <h3>¡Matchmaking generado!</h3>
            <p>
                <strong>Tu luchador:</strong> <?= htmlspecialchars($matchResult['user']['nombre'] . ' ' . $matchResult['user']['apellido']) ?> (<?= $matchResult['user']['username'] ?>)
                <br>
                <strong>Oponente:</strong> <?= htmlspecialchars($matchResult['opponent']['nombre'] . ' ' . $matchResult['opponent']['apellido']) ?> (<?= $matchResult['opponent']['username'] ?>)
                <br>
                <strong>Ubicación:</strong> Arena Central
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
