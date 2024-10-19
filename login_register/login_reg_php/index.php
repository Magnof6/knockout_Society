<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <div class="menu-container">
            <div id="menu-icon" class="menu-icon" onclick="toggleMenu()">&#9776;</div>
            <div class="auth-buttons">
                <a href="login.php" class="header-button">Login</a>
                <a href="register.php" class="header-button">Register</a>
            </div>
            <h1>KNOCKOUT SOCIETY</h1>
        </div>
    </div>
    
    <!-- Menú de navegación -->
    <div id="menu" class="menu">
        <ul>
            <li><a href="#">Inicio</a></li>
            <li><a href="services.php">Servicios</a></li>
            <li><a href="about.php">Acerca de</a></li>
            <li><a href="contact.php">Contacto</a></li>
            <li><a href="buscar.php">Buscar Pelea</a></li>
            <li><a href="ver_peleas.php">Ver Peleas</a></li> <!-- Enlace añadido -->
            <li><a href="ranking.php">Ranking</a></li>
        </ul>
    </div>
    
    <!-- Botón flotante de Kick -->
    <div class="footer">
        <a href="https://kick.com/knockoutsociety" target="_blank" id="Kick-floating-button">
            <img src="imagenes/kickkk.png" alt="imagen-kick-Icono Flotante">
        </a>
    </div>
    
    <script src="script.js"></script>
</body>
</html>
