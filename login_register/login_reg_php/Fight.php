<?php
session_start();
require_once 'db_connect.php';
require_once 'function/Matchmaking.php';
require_once 'function/selects.php';
require_once 'function/afterFight.php';

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
$activeFight = false; // To check if in 'luchando' state

function validateTwoRecordsForFight($conn, $id_lucha) {
    $sql = "SELECT COUNT(*) as total FROM peleando WHERE id_lucha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_lucha);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] === 2; // Devuelve true si hay exactamente 2 registros
}

// Process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $action = $_POST['action'];
        $matchmaking = new Matchmaking($conn);
        if($action === 'matchmaking'){
            if ($activeFight || $hasActiveMatch) {
                $errorMessage = "Ya tienes una pelea activa o estás emparejado.";
            } else {
                // Proceed with matchmaking
                $matchResult = $matchmaking->generateMatchForUser($user_email);

                if ($matchResult) {
                    $sql = "UPDATE luchador SET emparejado = 1 WHERE email = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('s', $user_email);
                    $stmt->execute();
                    $successMessage = "Matchmaking completed successfully!";
                    $hasActiveMatch = true; // Mark as matched
                } else {
                    $errorMessage = "No se pudo generar un emparejamiento.";
                }
            }
                } elseif ($action === 'toggle_status') {
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
                                $sql = "DELETE FROM lucha WHERE id_lucha = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('i', $match['id_lucha']);
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
            } elseif($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'finalize_fight') {
                try {
                    $hora_final = $_POST['hora_final'];
                    $ganador = $_POST['ganador'];
                    $num_rondas = $_POST['num_rondas'];
            
                    // Obtener el id_lucha actual (puedes adaptarlo según tu lógica)
                    $sql = "SELECT id_lucha FROM lucha WHERE (id_luchador1 = ? OR id_luchador2 = ?) AND estado = 'luchando'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ss', $user_email, $user_email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $lucha = $result->fetch_assoc();
                    
                    if ($lucha) {
                        $id_lucha = $lucha['id_lucha'];
            
                        // Insertar en la tabla peleando
                        $sql = "INSERT INTO peleando (email_luchador, hora_final, ganador, num_rondas, id_lucha) 
                                VALUES (?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('sssii', $user_email, $hora_final, $ganador, $num_rondas, $id_lucha);
                        $stmt->execute();
            
                        if ($stmt->affected_rows > 0) {
                            $successMessage = "Datos de la pelea guardados exitosamente.";
                        } else {
                            $errorMessage = "Error al guardar los datos de la pelea.";
                        }
                    } else {
                        $errorMessage = "No se encontró la lucha activa.";
                    }
                } catch (Exception $e) {
                    $errorMessage = "Error: " . $e->getMessage();
                }
            }
            
        }catch (Exception $e) {
          
            $errorMessage = "Error: " . $e->getMessage();
        }
}/* if ($_POST['action'] === 'finalize_fight') {
                    // Finalize the fight
                    $sql = "UPDATE lucha SET estado = 'finalizado' WHERE (id_luchador1 = ? OR id_luchador2 = ?) AND estado = 'luchando'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ss', $user_email, $user_email);
                    $stmt->execute();
        
                    // Update luchador's status
                    $sql = "UPDATE luchador SET emparejado = 0, buscando_pelea = 0 WHERE email = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('s', $user_email);
                    $stmt->execute();
        
                    $successMessage = "Pelea finalizada.";
                    $hasActiveMatch = false;
                    $activeFight = false;
                }else */
    
 

// Get the current status of "Buscando pelea" and "Emparejado"
$sql = "SELECT buscando_pelea, emparejado FROM luchador WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();
$luchador = $result->fetch_assoc();

if ($luchador) {
    $currentFightingStatus = $luchador['buscando_pelea'] ?? null;
    $hasActiveMatch = $luchador['emparejado'] ?? false;
}

// Check for active fight with estado = 'luchando'
$sql = "SELECT estado FROM lucha WHERE (id_luchador1 = ? OR id_luchador2 = ?) AND estado = 'luchando'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $user_email, $user_email);
$stmt->execute();
$result = $stmt->get_result();
$activeFight = $result->fetch_assoc();
if ($activeFight) {
    $activeFight = true;
} else {
    $activeFight = false;
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
            <button type="submit" <?= $activeFight ? 'disabled' : '' ?>><?= $currentFightingStatus ? "Desactivar" : "Activar" ?> "Buscando pelea"</button>
        </form>

        <!-- Button to search for a fight -->
        <?php if ($currentFightingStatus && !$hasActiveMatch && !$activeFight): ?>
            <form method="POST">
                <input type="hidden" name="action" value="matchmaking">
                <button type="submit">Buscar pelea</button>
            </form>
        <?php endif; ?>

        <!-- "Start fight" button (only if the match is already complete) -->
        <?php if ($hasActiveMatch && !$activeFight): ?>
            <form method="POST" action="start_match.php">
                <input type="hidden" name="action" value="start_fight">
                <button type="submit">Empezar pelea</button>
            </form>
        <?php endif; ?>
        
        <!-- "Finalizar" button if in 'luchando' state -->
        <?php if ($activeFight): ?>
            <button type="button" onclick="showFinalizeForm()">Finalizar</button>
        <?php endif; ?>

<!-- Formulario para finalizar pelea -->

<div id="finalize-form" style="display: none;">
    <form method="POST">
        <input type="hidden" name="action" value="finalize_fight">
        <label for="hora_final">Hora Final:</label>
        <input type="time" id="hora_final" name="hora_final" step="1" required><br>
        
        <label for="ganador">Ganador (Email):</label>
        <input type="email" id="ganador" name="ganador" required><br>
        
        <label for="num_rondas">Número de Rondas:</label>
        <input type="number" id="num_rondas" name="num_rondas" required><br>
        
        <button type="submit">Enviar</button>
        <button type="button" onclick="hideFinalizeForm()">Cancelar</button>
    </form>
</div>
    
    <script>
        function showFinalizeForm() {
            document.getElementById('finalize-form').style.display = 'block';
        }
        function hideFinalizeForm() {
            document.getElementById('finalize-form').style.display = 'none';
        }
        function submitFinalizeForm() {
            var form = document.getElementById('finalize-fight-form');
            var formData = new FormData(form);
            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    alert('Pelea finalizada correctamente.');
                    hideFinalizeForm();
                    // Optionally refresh the page or update the UI
                } else {
                    alert('Error al finalizar la pelea: ' + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al finalizar la pelea.');
            });
        }
    </script>

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