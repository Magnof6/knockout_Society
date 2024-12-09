<?php
session_start();
require_once 'db_connect.php';
require_once 'function/Matchmaking.php';
require_once 'function/afterFight.php';
require_once 'function/selects.php';

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
} else {
    header("Location: login.php");
    exit();
}

// Inicializar variables
$matchResult = null;
$errorMessage = "";
$successMessage = "";
$currentFightingStatus = null;
$hasActiveMatch = false;
$activeFight = false;

// Definir la función `cartera` para obtener el balance del usuario
/* function cartera($conn, $email) {
    $sql = "SELECT cartera FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['cartera'] ?? 0; // Devuelve 0 si no se encuentra la cartera
    } else {
        return 0; // Devuelve 0 si la consulta falla
    }
} */

// Función para validar que existan exactamente dos registros para la pelea en la tabla `peleando`
function validateTwoRecordsForFight($conn, $id_lucha) {
    $sql = "SELECT COUNT(*) as total FROM peleando WHERE id_lucha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_lucha);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] === 2; // Devuelve true si hay exactamente 2 registros
}

// Procesar solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'finalize_fight') {
            // Obtener datos del formulario
            $horaFinal = $_POST['hora_final']; // Formato esperado: HH:mm:ss
            $ganador = $_POST['ganador'];
            $numRondas = $_POST['num_rondas'];

            // Obtener detalles de la pelea activa
            $sql = "SELECT id_lucha, id_luchador1, id_luchador2 FROM lucha WHERE (id_luchador1 = ? OR id_luchador2 = ?) AND estado = 'luchando'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $user_email, $user_email);
            $stmt->execute();
            $result = $stmt->get_result();
            $lucha = $result->fetch_assoc();

            if (!$lucha) {
                $errorMessage = "No hay una pelea activa para finalizar.";
            } elseif ($ganador !== $lucha['id_luchador1'] && $ganador !== $lucha['id_luchador2']) {
                $errorMessage = "El ganador debe ser uno de los luchadores activos.";
            } else {
                $id_lucha = $lucha['id_lucha'];

                // Insertar datos en la tabla `peleando`
                $sql = "INSERT INTO peleando (email_luchador, hora_final, ganador, num_rondas, id_lucha) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sssii', $user_email, $horaFinal, $ganador, $numRondas, $id_lucha);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    // Validar si ambos luchadores han registrado la hora final
                    if (validateTwoRecordsForFight($conn, $id_lucha)) {
                        // Finalizar la pelea
                        $afterFight = new AfterFight($conn);
                        $resultEstado = $afterFight->comparadorPeleando($id_lucha);
                        if ($resultEstado) {
                            $afterFight->afterFightTerminada($id_lucha, $lucha['id_luchador1'], $lucha['id_luchador2'], $ganador);
                            $successMessage = "¡Pelea finalizada exitosamente!";
                        } else {
                            $afterFight->afterFightCancelada($id_lucha);
                            $successMessage = "La pelea fue cancelada.";
                        }
                    } else {
                        $errorMessage = "Ambos luchadores deben registrar la misma hora final.";
                    }
                } else {
                    $errorMessage = "No se pudo registrar la hora final en la tabla 'peleando'.";
                }
            }
        }
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Obtener estado actual de "Buscando pelea" y "Emparejado"
$sql = "SELECT buscando_pelea, emparejado FROM luchador WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();
$luchador = $result->fetch_assoc();
$currentFightingStatus = $luchador['buscando_pelea'] ?? null;
$hasActiveMatch = $luchador['emparejado'] ?? false;

// Verificar pelea activa con estado 'luchando'
$sql = "SELECT id_lucha FROM lucha WHERE (id_luchador1 = ? OR id_luchador2 = ?) AND estado = 'luchando'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $user_email, $user_email);
$stmt->execute();
$result = $stmt->get_result();
$activeFight = $result->fetch_assoc() ? true : false;

// Obtener datos del usuario
$sql = "SELECT nombre, apellido, username FROM usuario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$cartera = cartera($conn, $user_email); // Usar la función cartera
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

        <form method="POST">
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="new_state" value="<?= $currentFightingStatus ? 0 : 1 ?>">
            <button type="submit" <?= $activeFight ? 'disabled' : '' ?>><?= $currentFightingStatus ? "Desactivar" : "Activar" ?> "Buscando pelea"</button>
        </form>

        <?php if ($activeFight): ?>
            <button type="button" onclick="showFinalizeForm()">Finalizar</button>
        <?php endif; ?>

        <div id="finalize-form" style="display:none;">
            <h3>Finalizar Pelea</h3>
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

        <?php if ($successMessage): ?>
            <p class="success"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
    </div>

    <script>
        function showFinalizeForm() {
            document.getElementById('finalize-form').style.display = 'block';
        }

        function hideFinalizeForm() {
            document.getElementById('finalize-form').style.display = 'none';
        }
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>
