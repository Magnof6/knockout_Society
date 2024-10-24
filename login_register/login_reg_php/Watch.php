<!--
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Peleas y Ranking</title>
        <link rel="stylesheet" href="styles.css">
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
        <h1>Peleas</h1>
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
        <li><a href="">Acerca de</a></li>
        <li><a href="Contacto.php">Contacto</a></li>
        <li><a href="Fight.php">Buscar Pelea</a></li>
        <li><a href="Ranking.php">Ranking</a></li>
    </ul>
</div>


<div class="filtros-container">
    <form id="filtros-form">
        <label for="modalidad">Modalidad:</label>
        <select id="modalidad" name="modalidad">
            <option value="todas">Todas</option>
            <option value="mma">MMA</option>
            <option value="boxeo">Boxeo</option>
        </select>

        <label for="fecha">Fecha:</label>
        <select id="fecha" name="fecha">
            <option value="reciente">Recientes</option>
            <option value="semana">Hace una semana</option>
            <option value="antiguos">Antiguos</option>
        </select>

        <label for="vistos">Más vistos:</label>
        <select id="vistos" name="vistos">
            <option value="todos">Todos</option>
            <option value="mas_vistos">Más vistos</option>
        </select>

        <label for="categoria">Categoría (Peso):</label>
        <select id="categoria" name="categoria">
            <option value="todas">Todas</option>
            <option value="ligero">Peso Ligero</option>
            <option value="medio">Peso Medio</option>
            <option value="pesado">Peso Pesado</option>
        </select>

        <button type="submit">Filtrar</button>
    </form>
</div>


<div class="buscador-container">
    <input type="text" id="busqueda" name="busqueda" placeholder="Buscar peleadores...">
    <button type="button" onclick="buscarPeleador()">Buscar</button>
</div>


<div class="videos-container">
    <h2>Videos de Peleas</h2>
    <div class="video-item">
        <h3>Título del video 1</h3>
        <video controls>
            <source src="ruta/del/video1.mp4" type="video/mp4">
            Tu navegador no soporta la reproducción de videos.
        </video>
    </div>
    <div class="video-item">
        <h3>Título del video 2</h3>
        <video controls>
            <source src="ruta/del/video2.mp4" type="video/mp4">
            Tu navegador no soporta la reproducción de videos.
        </video>
    </div>

</div>

<div class="footer">
    <a href="https://kick.com/knockoutsociety" target="_blank" id="Kick-floating-button">
        <img src="imagenes/kickkk.png" alt="imagen-kick-Icono Flotante">
    </a>
</div>

<script src="script.js"></script>
</body>
</html>
-->

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
        <h1>Servicios</h1>
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
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="footer">
    <!-- Boton que te lleva a Kick -->
    <a href="https://kick.com/knockoutsociety" target="_blank" id="Kick-floating-button">
        <img src="imagenes/kickkk.png" alt="imagen-kick-Icono Flotante">
    </a>
</div>
<script src="script.js"></script>
</body>
</html>
