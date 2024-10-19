<?php
    require_once 'db_connect.php';
    session_start();
    // Consulta SQL para obtener nombre y apellido
    if (isset($_SESSION['user_email'])) {
        $user_email = $_SESSION['user_email'];
        // Puedes usar $user_email en consultas o para mostrar el email
    } else {
        // Si no hay sesión activa, redirige al usuario al login
        header("Location: login.php");
        exit();
    }

    $sql = "SELECT nombre, apellido FROM usuario";
    $result = $conn->query($sql);

?>

<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Página con Menú Lateral y Perfil</title>
        <!-- Enlazar el archivo CSS externo -->
        <link rel="stylesheet" href="styles.css">
        <style>
            /* Ocultar la lista de usuarios por defecto */
            #userSection {
                display: none;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="menu-container">
                <div id="menu-icon" class="menu-icon" onclick="toggleMenu()">&#9776;</div>
                <h1>KNOCKOUT SOCIETY</h1>
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
        <!-- Menú lateral izquierdo -->
        <div id = "menu"class="menu">
            <ul>
                <li><a href="#" onclick="toggleUserSection()">Perfiles de otras personas</a></li>
                <li><a href="#">Inicio</a></li>
                <li><a href="#">Acerca de</a></li>
                <li><a href="Contacto.php">Contacto</a></li>
                <li><a href="Fight.php">Buscar Pelea</a></li>
                <li><a href="Watch.php">Ver Peleas</a></li>
                <li><a href="Ranking.php">Ranking</a></li>
            </ul>
            <!-- Modificar el enlace para que active la visibilidad -->
            
        </div>
        <!-- Contenido principal -->
        <div class="content">
            <!-- Envuelve el título y la tabla en un contenedor común con id="userSection" -->
            <div id="userSection">
                <h1>Lista de Usuarios</h1>
                <table>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                    </tr>
                    <?php
                    if ($result->num_rows > 0) {
                        // Mostrar datos en filas
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr><td>" . $row["nombre"] . "</td><td>" . $row["apellido"] . "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No hay resultados</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>

        <script>
            // Función para mostrar/ocultar la sección completa (título + tabla)
            function toggleUserSection() {
                var userSection = document.getElementById("userSection");
                if (userSection.style.display === "none") {
                    userSection.style.display = "block";
                } else {
                    userSection.style.display = "none";
                }
            }
        </script>
        <div class="footer">
        <!-- Boton que te lleva a Kick -->
            <a href="https://kick.com/knockoutsociety" target="_blank" id="Kick-floating-button">
                <img src="imagenes/kickkk.png" alt="imagen-kick-Icono Flotante">
            </a>
        </div>
     <script src="script.js"></script>
    </body>
</html>

<?php
// Cerrar la conexión
    $conn->close();
?>
