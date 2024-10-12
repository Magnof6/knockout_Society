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
        <div id="menu-icon" class="menu-icon" onclick="toggleMenu()">&#9776;</div>
        <div class ="auth-buttons">
            <a href="login.php"  class="login-button">Login</a>
            <a href="register.php" class ="register-button">Register</a>
        </div>
    </div>
    <div id="menu" class="menu">
            <ul>
                <li><a href="#">Inicio</a></li>
                <li><a href="#">Servicios</a></li>
                <li><a href="#">Acerca de</a></li>
                <li><a href="#">Contacto</a></li>
                <li><a href="#">Buscar Pelea</a></li>
                <li><a href="#">Ver Peleas</a></li>
                <li><a href="#">Ranking</a></li>
            </ul>
        </div>
    <h1>KNOCKOUT SOCIETY</h1>
    <div class="footer">
        <!-- Boton que te lleva a Kick -->
        <a href="https://kick.com/knockoutsociety" target="_blank" id="Kick-floating-button">
            <img src="imagenes/kickkk.png" alt="imagen-kick-Icono Flotante">
        </a>
    </div>
    
    <script src="script.js"></script>
</body>
</html>
