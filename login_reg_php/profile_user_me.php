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
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Menú lateral izquierdo */
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #333;
            padding-top: 20px;
            color: white;
        }

        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .sidebar .search-section {
            margin-top: 30px;
            padding: 15px 25px;
        }

        .sidebar input[type="text"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }

        /* Barra superior (perfil en la derecha) */
        .navbar {
            display: flex;
            justify-content: flex-end;
            background-color: #333;
            padding: 10px 20px;
            color: white;
            position: fixed;
            top: 0;
            left: 250px; /* Ajuste para dejar espacio al menú lateral */
            right: 0;
            z-index: 1000;
        }

        /* Perfil desplegable en la esquina derecha */
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-button {
            background-color: #333;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }

        .profile-button:hover {
            background-color: #575757;
        }

        .profile-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .profile-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .profile-content a:hover {
            background-color: #f1f1f1;
        }

        .profile-dropdown:hover .profile-content {
            display: block;
        }

        /* Contenido principal */
        .content {
            margin-left: 250px; /* Ajuste para dejar espacio al menú lateral */
            margin-top: 60px;
            padding: 20px;
        }

        /* Estilos para la tabla */
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
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
