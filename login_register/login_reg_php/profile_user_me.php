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
        <link rel="stylesheet" href="./styles.css?v=1.0">
        <style>
            /* Ocultar la lista de usuarios por defecto */
            #userSection {
                display: none;
            }
        </style>
    </head>
    <body>

        <!-- Menú lateral izquierdo -->
        <div class="sidebar">
            <a href="#">Inicio</a>
            <!-- Modificar el enlace para que active la visibilidad -->
            <a href="#" onclick="toggleUserSection()">Perfiles de otras personas</a>
            <a href="services.php">Servicios</a>
            <a href="about.php">Acerca de</a>
            <a href="contact.php">Contacto</a>
            <a href="buscar.php">Buscar Pelea</a>
            <a href="ver_peleas.php">Ver Peleas</a>
            <a href="ranking.php">Ranking</a>
            <div class="search-section">
                <label for="search">Buscar perfiles:</label>
                <input type="text" id="search" placeholder="Buscar...">
            </div>
        </div>

        <!-- Barra de navegación superior -->
        <div class="navbar">
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

    </body>
</html>

<?php
// Cerrar la conexión
    $conn->close();
?>
