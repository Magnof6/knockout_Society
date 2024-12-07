<?php
session_start();
require_once 'db_connect.php';
require_once 'function/inserts.php';
require_once 'function/selects.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";
$error = "";

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "CSRF token inválido.";
    } else {
        $id_apuesta = uniqid('apuesta_', true); // Generar un ID único
        $id_lucha = filter_input(INPUT_POST, 'id_lucha', FILTER_SANITIZE_NUMBER_INT);
        $luchador_apostado = filter_input(INPUT_POST, 'luchador_apostado', FILTER_SANITIZE_STRING);
        $w = filter_input(INPUT_POST, 'ganadas', FILTER_VALIDATE_INT);
        $l = filter_input(INPUT_POST, 'perdidas', FILTER_VALIDATE_INT);
        $d = filter_input(INPUT_POST, 'empates', FILTER_VALIDATE_INT);

        if ($id_lucha && $luchador_apostado && $w !== false && $l !== false && $d !== false) {
            if ($w < 0 || $l < 0 || $d < 0) {
                $error = "Los valores de 'ganadas', 'perdidas' y 'empates' no pueden ser negativos.";
            } else {
                $crearApuesta = new inserts($conn);
                if ($crearApuesta->crearApuesta($_SESSION['user_email'], $id_lucha, $luchador_apostado, $w, $l, $d)) {
                    $mensaje = "Apuesta creada exitosamente.";
                } else {
                    $error = "Error al crear la apuesta. Intenta nuevamente.";
                }
            }
        } else {
            $error = "Por favor, completa todos los campos correctamente.";
        }
    }
}

$cartera = cartera($conn, $_SESSION['user_email']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Apuestas</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <style>
        /* Ocultar la lista de usuarios por defecto */
        #userSection {
            display: none;
        }

        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="menu-container">
        <div id="menu-icon" class="menu-icon" onclick="toggleMenu()">&#9776;</div>
        <h1>Gestión de Apuestas</h1>
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
        <li><a href="Ranking.php">Ranking</a></li>
        <li><a href="Fight.php">Buscar Pelea</a></li>
        <li><a href="Watch.php">Ver Peleas</a></li>
        <li><a href="apuestasHTML.php">Apuestas</a></li>
    </ul>
</div>

<div class="container">
    <?php if ($mensaje): ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <h2>Listado de Luchas</h2>
    <?php
    $sql = "SELECT id_lucha, id_luchador1, id_luchador2, id_categoria, id_ganador, num_rondas, fecha, hora_inicio, hora_final, estado, ubicacion FROM lucha";
    $result = $conn->query($sql);

    if ($result === FALSE) {
        echo "<p class='error'>Error al cargar las luchas: " . $conn->error . "</p>";
    } elseif ($result->num_rows > 0) {
        echo '<table>
                    <thead>
                        <tr>
                            <th>ID Lucha</th>
                            <th>Luchador 1</th>
                            <th>Luchador 2</th>
                            <th>Categoría</th>
                            <th>Ganador</th>
                            <th>Número de Rondas</th>
                            <th>Fecha</th>
                            <th>Hora Inicio</th>
                            <th>Hora Final</th>
                            <th>Estado</th>
                            <th>Ubicación</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                        <td>' . htmlspecialchars($row['id_lucha']) . '</td>
                        <td>' . htmlspecialchars($row['id_luchador1']) . '</td>
                        <td>' . htmlspecialchars($row['id_luchador2']) . '</td>
                        <td>' . htmlspecialchars($row['id_categoria']) . '</td>
                        <td>' . (isset($row['id_ganador']) ? htmlspecialchars($row['id_ganador']) : 'Sin ganador') . '</td>
                        <td>' . htmlspecialchars($row['num_rondas']) . '</td>
                        <td>' . htmlspecialchars($row['fecha']) . '</td>
                        <td>' . htmlspecialchars($row['hora_inicio']) . '</td>
                        <td>' . htmlspecialchars($row['hora_final']) . '</td>
                        <td>' . htmlspecialchars($row['estado']) . '</td>
                        <td>' . htmlspecialchars($row['ubicacion']) . '</td>
                        <td><button onclick="openModal(' . htmlspecialchars($row['id_lucha']) . ', \'' . htmlspecialchars($row['id_luchador1']) . '\', \'' . htmlspecialchars($row['id_luchador2']) . '\')">Apostar</button></td>
                    </tr>';
        }
        echo '</tbody></table>';
    } else {
        echo "<p>No hay luchas registradas.</p>";
    }

    $conn->close();
    ?>
</div>

<!-- Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" id="id_lucha" name="id_lucha">
            <div>
                <label for="luchador_apostado">Luchador Apostado:</label>
                <select id="luchador_apostado" name="luchador_apostado" required>
                    <option value="">Seleccione un luchador</option>
                </select>
            </div>
            <div>
                <label for="ganadas">Ganadas (W):</label>
                <input type="number" id="ganadas" name="ganadas" required>
            </div>
            <div>
                <label for="perdidas">Perdidas (L):</label>
                <input type="number" id="perdidas" name="perdidas" required>
            </div>
            <div>
                <label for="empates">Empates (D):</label>
                <input type="number" id="empates" name="empates" required>
            </div>
            <button type="submit">Crear Apuesta</button>
            <button type="button" onclick="closeModal()">Cancelar</button>
        </form>
    </div>
</div>

<script>
    function openModal(id_lucha, luchador1, luchador2) {
        document.getElementById('id_lucha').value = id_lucha;
        var luchadorSelect = document.getElementById('luchador_apostado');
        luchadorSelect.innerHTML = '<option value="' + luchador1 + '">' + luchador1 + '</option><option value="' + luchador2 + '">' + luchador2 + '</option>';
        document.getElementById('myModal').style.display = "block";
    }

    function closeModal() {
        document.getElementById('myModal').style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('myModal')) {
            closeModal();
        }
    }
</script>
</body>
</html>
