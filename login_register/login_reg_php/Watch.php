<?php
    session_start();
    require_once 'db_connect.php';

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['user_email'])) {
        header("Location: login.php");
        exit();
    }

    $user_email = $_SESSION['user_email'];


    $sql = "SELECT * FROM lucha";
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
                // Función para realizar la búsqueda de peleadores
                var busqueda = document.getElementById("busqueda").value;
                alert("Buscando peleador: " + busqueda);
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
            <!-- Perfil desplegable en la esquina derecha -->
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
                        <td><?php echo htmlspecialchars($fight['id_luchador1']); ?></td>
                        <td><?php echo htmlspecialchars($fight['id_luchador2']); ?></td>
                        <td><?php echo htmlspecialchars($fight['id_categoria']); ?></td>
                        <td><?php echo htmlspecialchars($fight['id_ganador']); ?></td>
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
                            ?>
                            <a href="<?php echo htmlspecialchars($replay['url']); ?>" target="_blank">Ver</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php include 'footer.php'; ?>
    </body>
</html>
