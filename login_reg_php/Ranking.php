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
                <div class="auth-buttons">
                    <a href="login.php" class="header-button">Login</a>
                    <a href="register.php" class="header-button">Register</a>
                </div>
                <h1>Ranking</h1>
            </div>
    </div>
    <div id="menu" class="menu">
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="">Acerca de</a></li>
                <li><a href="Contacto.php">Contacto</a></li>
                <li><a href="Fight.php">Buscar Pelea</a></li>
                <li><a href="Watch.php">Ver Peleas</a></li>
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