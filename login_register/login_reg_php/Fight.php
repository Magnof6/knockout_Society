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

// Initialize variables
$matchResult = null;
$errorMessage = "";
$successMessage = "";
$currentFightingStatus = null;
$hasActiveMatch = false; // Initial state for matched

// Process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $matchmaking = new Matchmaking($conn);

        if ($_POST['action'] === 'matchmaking') {
            // Search for a fight (Matchmaking)
            $matchResult = $matchmaking->generateMatchForUser($user_email);

            if ($matchResult) {
                // Update the matched status
                $sql = "UPDATE luchador SET emparejado = 1 WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $user_email);
                $stmt->execute();
                $successMessage = "Matchmaking completed successfully!";
                $hasActiveMatch = true; // Mark as matched
            } else {
                $errorMessage = "Could not generate a match.";
            }
        } elseif ($_POST['action'] === 'toggle_status') {
            $newState = $_POST['new_state'];
            
            // Update the status in the database
            $sql = "UPDATE luchador SET buscando_pelea = ?, emparejado = 0 WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('is', $newState, $user_email);
            $stmt->execute();
        
            if ($stmt->affected_rows > 0) {
                $currentFightingStatus = $newState;
        
                // If "Buscando pelea" is deactivated, delete the active match
                if ($newState == 0) {
                    // Check if the user is in a fight
                    $sql = "SELECT * FROM lucha WHERE id_luchador1 = ? OR id_luchador2 = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ss', $user_email, $user_email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $match = $result->fetch_assoc();

                    if ($match) {
                        // Get opponent's email
                        if ($match['id_luchador1'] == $user_email) {
                            $opponent_email = $match['id_luchador2'];
                        } else {
                            $opponent_email = $match['id_luchador1'];
                        }

                        // Delete the fight
                        $sql = "DELETE FROM lucha WHERE id_luchador1 = ? OR id_luchador2 = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('ss', $user_email, $opponent_email);
                        $stmt->execute();

                        // Update both users' emparejado status
                        $sql = "UPDATE luchador SET emparejado = 0 WHERE email = ? OR email = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('ss', $user_email, $opponent_email);
                        $stmt->execute();

                        $successMessage = "Has desactivado 'Buscando pelea' y tu lucha activa ha sido eliminada.";
                    } else {
                        $successMessage = "Has desactivado 'Buscando pelea'.";
                    }
                
                    $hasActiveMatch = false; 
                }
            } else {
                $errorMessage = "Could not update the status.";
            }
        }
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Get the current status of "Buscando pelea" and "Emparejado"
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

// Query to get user data
$sql = "SELECT nombre, apellido, username FROM usuario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$cartera = cartera($conn, $_SESSION['user_email']);
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
            <label for="cartera">Cartera:</label>
            <input type="number" id="cartera" value="<?php echo $cartera; ?>" disabled>
        </div>
        <div class="profile-dropdown">
            <button class="profile-button">Perfil ▼</button>
            <div class="profile-content">
                <a href="profile_user.php">Ver Perfil</a>
                <a href="logout.php">Cerrar sesión</a>
            </div>
        </div>
    </div>
    <div id="menu" class="menu">
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="Contacto.php">Contacto</a></li>
            <li><a href="Watch.php">Ver Peleas</a></li>
            <li><a href="Ranking.php">Ranking</a></li>
            <li><a href="apuestaHTML.php">Apuestas</a></li>
        </ul>
    </div>

    <div class="matchmaking-container">
        <h2>Hola, <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?> (<?= htmlspecialchars($user['username']) ?>)</h2>
        <p>Current status: <strong><?= $currentFightingStatus ? "Active" : "Inactive" ?></strong></p>

        <!-- Button to toggle status -->
        <form method="POST">
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="new_state" value="<?= $currentFightingStatus ? 0 : 1 ?>">
            <button type="submit"><?= $currentFightingStatus ? "Desactivar" : "Activar" ?> "Buscando pelea"</button>
        </form>

        <!-- Button to search for a fight -->
        <?php if ($currentFightingStatus && !$hasActiveMatch): ?>
            <form method="POST">
                <input type="hidden" name="action" value="matchmaking">
                <button type="submit">Buscar pelea</button>
            </form>
        <?php endif; ?>

        <!-- "Start fight" button (only if the match is already complete) -->
        <?php if ($hasActiveMatch): ?>
            <form method="POST" action="start_match.php">
                <input type="hidden" name="action" value="start_fight">
                <button type="submit">Empezar pelea</button>
            </form>
        <?php endif; ?>

        <!-- Success or error messages -->
        <?php if ($successMessage): ?>
            <p class="success"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>