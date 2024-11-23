<?php
    session_start();
    require_once 'db_connect.php';
    require_once 'function/apuestas.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        #menu {
            position: fixed; 
            top: 0;
            left: 0;
            width: 200px;  
            height: 100%;  
            background-color: #333;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.5);
        }

        #menu ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        #menu li {
            padding: 10px;
            text-align: center;
        }

        #menu li a {
            text-decoration: none;
            color: white;
            display: block;
        }

        #menu li a:hover {
            background-color: #575757;
        }

       
        .content {
            margin-left: 220px;  
            padding: 20px;
        }

        .table-container {
            margin-top: 20px;
            text-align: center;
        }

        a {
            margin: 10px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
        }

        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="menu-container">
            <div id="menu-icon" class="menu-icon">&#9776;</div>
            <h1>KNOCKOUT SOCIETY</h1>
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

    <!-- Menú lateral izquierdo -->
    <div id="menu" class="menu">
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="#">Acerca de</a></li>
            <li><a href="Contacto.php">Servicios</a></li>
            <li><a href="Fight.php">Buscar Pelea</a></li>
            <li><a href="Watch.php">Ver Peleas</a></li>
            <li><a href="Ranking.php">Ranking</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <div style="text-align: center;">
            <a href="function/mostrar.php?criterio=puntos">Puntos</a>
            <a href="function/mostrar.php?criterio=nombre">Nombre</a>
            <a href="function/mostrar.php?criterio=victorias">Victorias</a>
            <a href="function/mostrar.php?criterio=empates">Empates</a>
            <a href="function/mostrar.php?criterio=derrotas">Derrotas</a>
        </div>

        <!-- Aquí se mostrará la tabla generada por PHP -->
    </div>

    <div class="footer">
        <a href="https://kick.com/knockoutsociety" target="_blank" id="Kick-floating-button">
            <img src="imagenes/kickkk.png" alt="imagen-kick-Icono Flotante">
        </a>
    </div>
</body>
</html>

<?php
// Cerrar la conexión
    $conn->close();
?>
