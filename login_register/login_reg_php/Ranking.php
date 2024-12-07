<?php
require_once 'db_connect.php';
require_once 'function/selects.php';
session_start();

$error_message = "";

$criterio = isset($_GET['criterio']) ? $_GET['criterio'] : 'puntos';

$criterios_validos = ['puntos', 'luchador.email', 'victorias', 'empates', 'derrotas'];
if (!in_array($criterio, $criterios_validos)) {
    $error_message = "Criterio no válido.";
} else {
    $sql = "SELECT usuario.username, luchador.puntos, luchador.victorias, luchador.empates, luchador.derrotas, luchador.peso, luchador.altura, luchador.lateralidad
            FROM luchador
            JOIN usuario ON luchador.email = usuario.email
            ORDER BY $criterio DESC";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $error_message = "Error al preparar la consulta: " . $conn->error;
    } else {
        $stmt->execute();
        $result = $stmt->get_result();
    }
}

$user_email = $_SESSION['user_email'] ?? '';
$cartera = cartera($conn, $user_email);
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ranking</title>
        <link rel="stylesheet" href="styles.css">
        <script src="script.js"></script>
        <style>
            .popup {
                display: none;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 50%;
                background-color: white;
                padding: 20px;
                border: 2px solid #ddd;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                z-index: 1000;
                text-align: center;
            }

            .popup h3 {
                margin-top: 0;
            }

            .popup .close-button {
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                padding: 10px 15px;
                cursor: pointer;
                font-size: 16px;
                text-decoration: none;
                display: block;
                margin-top: 10px;
                text-align: center;
            }

            .popup .close-button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
    <div class="header">
        <div class="menu-container">
            <div id="menu-icon" class="menu-icon" onclick="toggleMenu()">&#9776;</div>
            <h1>Ranking de Luchadores</h1>
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
            <li><a href="#">Acerca de</a></li>
            <li><a href="Contacto.php">Servicios</a></li>
            <li><a href="Fight.php">Buscar Pelea</a></li>
            <li><a href="Watch.php">Ver Peleas</a></li>
            <li><a href="Ranking.php">Ranking</a></li>
            <li><a href="apuestaHTML.php">Apuestas</a></li>
        </ul>
    </div>

    <div class="container">
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="GET" action="Ranking.php">
            <label for="criterio">Ordenar por:</label>
            <select name="criterio" id="criterio">
                <option value="puntos" <?php echo $criterio === 'puntos' ? 'selected' : ''; ?>>Puntos</option>
                <option value="luchador.email" <?php echo $criterio === 'username' ? 'selected' : ''; ?>>Username</option>
                <option value="victorias" <?php echo $criterio === 'victorias' ? 'selected' : ''; ?>>Victorias</option>
                <option value="empates" <?php echo $criterio === 'empates' ? 'selected' : ''; ?>>Empates</option>
                <option value="derrotas" <?php echo $criterio === 'derrotas' ? 'selected' : ''; ?>>Derrotas</option>
            </select>
            <button type="submit">Actualizar</button>
        </form>

        <?php if (isset($result) && $result->num_rows > 0): ?>
            <table border="1">
                <tr>
                    <th>Username</th>
                    <th>Puntos</th>
                    <th>Victorias</th>
                    <th>Empates</th>
                    <th>Derrotas</th>
                </tr>
                <?php while ($fila = $result->fetch_assoc()): ?>
                    <tr onclick="showFightDetails('<?php echo htmlspecialchars(json_encode($fila)); ?>')">
                        <td><?php echo htmlspecialchars($fila['username']); ?></td>
                        <td><?php echo htmlspecialchars($fila['puntos']); ?></td>
                        <td><?php echo htmlspecialchars($fila['victorias']); ?></td>
                        <td><?php echo htmlspecialchars($fila['empates']); ?></td>
                        <td><?php echo htmlspecialchars($fila['derrotas']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No se encontraron resultados.</p>
        <?php endif; ?>
    </div>

    <div id="fightPopup" class="popup">
        <h3>Detalles del Luchador</h3>
        <p><strong>Username:</strong> <span id="popupUsername"></span></p>
        <p><strong>Puntos:</strong> <span id="popupPuntos"></span></p>
        <p><strong>Victorias:</strong> <span id="popupVictorias"></span></p>
        <p><strong>Empates:</strong> <span id="popupEmpates"></span></p>
        <p><strong>Derrotas:</strong> <span id="popupDerrotas"></span></p>
        <p><strong>Peso:</strong> <span id="popupPeso"></span></p>
        <p><strong>Altura:</strong> <span id="popupAltura"></span></p>
        <p><strong>Lateralidad:</strong> <span id="popupLateralidad"></span></p>
        <button class="close-button" onclick="closePopup('fightPopup')">Cerrar</button>
    </div>

    <script>
        function showFightDetails(fight) {
            var fightDetails = JSON.parse(fight);
            document.getElementById("popupUsername").textContent = fightDetails.username;
            document.getElementById("popupPuntos").textContent = fightDetails.puntos;
            document.getElementById("popupVictorias").textContent = fightDetails.victorias;
            document.getElementById("popupEmpates").textContent = fightDetails.empates;
            document.getElementById("popupDerrotas").textContent = fightDetails.derrotas;
            document.getElementById("popupPeso").textContent = fightDetails.peso;
            document.getElementById("popupAltura").textContent = fightDetails.altura;
            document.getElementById("popupLateralidad").textContent = fightDetails.lateralidad;
            document.getElementById("fightPopup").style.display = "block";

        }

        function closePopup(popupId) {
            document.getElementById(popupId).style.display = "none";
        }
    </script>
    <?php include 'footer.php'; ?>
    </body>
    </html>

<?php
$conn->close();
?>