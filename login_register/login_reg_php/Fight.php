<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <div class="menu-container">
            <div id="menu-icon" class="menu-icon" onclick="toggleMenu()">&#9776;</div>
            <h1>Buscar Pelea</h1>
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
                <li><a href="">Acerca de</a></li>
                <li><a href="Contacto.php">Contacto</a></li>
                <li><a href="Watch.php">Ver Peleas</a></li>
                <li><a href="Ranking.php">Ranking</a></li>
            </ul>
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