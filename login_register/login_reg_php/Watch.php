<?php
    session_start();
    require_once 'db_connect.php';

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['user_email'])) {
        header("Location: login.php");
        exit();
    }

    $user_email = $_SESSION['user_email'];

    $sql = "SELECT l.*, 
               u1.nombre AS luchador1_nombre, 
               u2.nombre AS luchador2_nombre 
        FROM lucha l
        LEFT JOIN usuario u1 ON l.id_luchador1 = u1.email
        LEFT JOIN usuario u2 ON l.id_luchador2 = u2.email";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $fights = [];
    while ($row = $result->fetch_assoc()) {
        $fights[] = $row;
    }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Peleas Pasadas</title>
        <link rel="stylesheet" href="styles.css">
        <style>
            #video-popup {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            }

            .popup-content {
                position: relative;
                width: 80%;
                max-width: 900px;
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .popup-content iframe {
                width: 100%;
                height: 500px;
            }

            .close-btn {
                position: absolute;
                top: 10px;
                right: 15px;
                font-size: 24px;
                color: black;
                cursor: pointer;
            }

            .profile-container {
                background-color: white;
                border: 2px solid #ddd;
                border-radius: 10px;
                padding: 40px;
                width: 80%;
                max-width: 1200px;
                margin: 0 auto;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                text-align: center;
            }

            h2 {
                color: black;
            }

            .fights-table {
                width: 100%;
                margin-top: 20px;
                border-collapse: collapse;
                font-family: Arial, sans-serif;
            }

            .fights-table th, .fights-table td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: left;
                color: #333;
            }

            .fights-table th {
                background-color: #f4f4f4;
                color: #000;
                font-weight: bold;
            }

            .fights-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .fights-table tr:hover {
                background-color: #f1f1f1;
            }

            .fights-table th, .fights-table td {
                padding: 15px;
            }

            .fights-table th {
                background-color: dodgerblue;
                color: white;
            }
        </style>
        <script>
            function toggleMenu() {
                var menu = document.getElementById("menu");
                menu.style.display = menu.style.display === "block" ? "none" : "block";
            }

            function buscarPeleador() {
                var busqueda = document.getElementById("busqueda").value;
                alert("Buscando peleador: " + busqueda);
            }

            function openPopup(videoUrl) {
                const iframeContainer = document.getElementById('iframe-container');
                iframeContainer.innerHTML = `
                    <iframe 
                        id="video-frame" 
                        width="560" 
                        height="315" 
                        src="${videoUrl}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                `;
                document.getElementById('video-popup').style.display = 'flex';
            }

            function closePopup() {
                const iframeContainer = document.getElementById('iframe-container');
                iframeContainer.innerHTML = ''; // Elimina el iframe del DOM
                document.getElementById('video-popup').style.display = 'none';
            }
        </script>
    </head>
    <body>
        <div class="header">
            <div class="menu-container">
                <div id="menu-icon" class="menu-icon" onclick="toggleMenu()">&#9776;</div>
                <h1>Peleas Pasadas</h1>
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
                <li><a href="Fight.php">Buscar Pelea</a></li>
                <li><a href="Watch.php">Ver Peleas</a></li>
                <li><a href="Ranking.php">Ranking</a></li>
                <li><a href="apuestaHTML.php">Apuestas</a></li>
            </ul>
        </div>

        <div class="profile-container">
            <h2>Resultados de Peleas</h2>
            <table class="fights-table">
                <thead>
                <tr>
                    <th>Luchador 1</th>
                    <th>Luchador 2</th>
                    <th>Categoría</th>
                    <th>Ganador</th>
                    <th>Número de Rondas</th>
                    <th>Fecha</th>
                    <th>Hora de Inicio</th>
                    <th>Hora Final</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    <th>Ver pelea</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($fights as $fight): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fight['luchador1_nombre'] ?? 'Desconocido'); ?></td>
                        <td><?php echo htmlspecialchars($fight['luchador2_nombre'] ?? 'Desconocido'); ?></td>
                        <td><?php echo htmlspecialchars($fight['id_categoria']); ?></td>
                        <td><?php echo htmlspecialchars($fight['id_ganador'] ?? 'No disponible'); ?></td>
                        <td><?php echo htmlspecialchars($fight['num_rondas']); ?></td>
                        <td><?php echo htmlspecialchars($fight['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($fight['hora_inicio']); ?></td>
                        <td><?php echo htmlspecialchars($fight['hora_final']); ?></td>
                        <td><?php echo htmlspecialchars($fight['estado']); ?></td>
                        <td><?php echo htmlspecialchars($fight['ubicacion']); ?></td>
                        <td>
                            <?php
                            $id_lucha = $fight['id_lucha'];
                            $sql_replay = "SELECT url FROM replays WHERE id_lucha = ?";
                            $stmt_replay = $conn->prepare($sql_replay);
                            $stmt_replay->bind_param("i", $id_lucha);
                            $stmt_replay->execute();
                            $result_replay = $stmt_replay->get_result();
                            $replay = $result_replay->fetch_assoc();
                            if ($replay) {
                                $embedUrl = str_replace("watch?v=", "embed/", htmlspecialchars($replay['url']));
                                echo '<a href="javascript:void(0);" onclick="openPopup(\'' . $embedUrl . '\')">Ver</a>';
                            } else {
                                echo 'No disponible';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="video-popup" style="display: none;">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup()">&times;</span>
                <div id="iframe-container"></div>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </body>
</html>