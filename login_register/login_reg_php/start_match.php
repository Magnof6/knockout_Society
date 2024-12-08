<?php
session_start();
require_once 'db_connect.php';
require_once 'function/Matchmaking.php';
require_once 'function/selects.php';

// Verify if the user is authenticated
if (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
} else {
    // If no active session, redirect the user to login
    header("Location: login.php");
    exit();
}

$successMessage = "";
$errorMessage = "";

// Process the start fight action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'start_fight') {
    try {
        // Update user's empezarPelea to 1
        $sql = "UPDATE luchador SET empezarPelea = 1 WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $user_email);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Find the opponent's email from the lucha table
            $sql = "SELECT id_luchador1, id_luchador2 FROM lucha WHERE (id_luchador1 = ? OR id_luchador2 = ?) AND estado <> 'lucha'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $user_email, $user_email);
            $stmt->execute();
            $result = $stmt->get_result();
            $match = $result->fetch_assoc();

            if ($match) {
                if ($match['id_luchador1'] == $user_email) {
                    $opponent_email = $match['id_luchador2'];
                } else {
                    $opponent_email = $match['id_luchador1'];
                }

                // Check opponent's empezarPelea value
                $sql = "SELECT empezarPelea FROM luchador WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $opponent_email);
                $stmt->execute();
                $result = $stmt->get_result();
                $opponent = $result->fetch_assoc();

                if ($opponent && $opponent['empezarPelea'] == 1) {
                    // Both users have started the fight
                    // Update the estado in lucha to 'lucha'
                    $sql = "UPDATE lucha SET estado = 'luchando' WHERE (id_luchador1 = ? OR id_luchador2 = ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ss', $user_email, $user_email);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        $successMessage = "La pelea ha comenzado.";
                    } else {
                        $errorMessage = "No se pudo actualizar el estado de la pelea.";
                    }
                } else {
                    $successMessage = "Has iniciado la pelea. Esperando a tu oponente.";
                }
            } else {
                $errorMessage = "No se encontró una lucha activa.";
            }
        } else {
            $errorMessage = "No se pudo actualizar tu estado de pelea.";
        }
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Start Fight</title>
    <style>
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <?php if ($successMessage): ?>
        <p class="success"><?= htmlspecialchars($successMessage) ?></p>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>
    <a href="fight.php">Volver a la página de pelea</a>
</body>
</html>