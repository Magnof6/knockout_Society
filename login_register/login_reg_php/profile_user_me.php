<?php
// Datos de conexión a la base de datos
$servername = "serverkn.ddns.net";
$username = "root"; // Usuario de la base de datos
$password = "PeleaDown$666"; // Contraseña de la base de datos
$dbname = "knockout"; // Nombre de tu base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta SQL para obtener nombre y apellido
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
</head>
<body>

    <!-- Menú lateral izquierdo -->
    <div class="sidebar">
        <a href="#">Inicio</a>
        <a href="#">Perfiles de otras personas</a>
        <a href="#">Proyectos</a>
        <a href="#">Configuración</a>
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
                <a href="#">Ver Perfil</a>
                <a href="#">Configuraciones</a>
                <a href="#">Cerrar sesión</a>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="content">
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

</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
