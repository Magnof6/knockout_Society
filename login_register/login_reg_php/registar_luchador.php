<?php
// Suponiendo que ya tienes conexión a la base de datos
session_start();
$user_id = $_SESSION['user_id']; // Asegúrate de tener el ID del usuario en la sesión

// Consulta para obtener datos del usuario
$sql = "SELECT nombre_usuario, es_luchador FROM usuario WHERE id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Verificar si el usuario es luchador
$is_fighter = $user['es_luchador'] == 1;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <script>
        // Función para mostrar/ocultar los campos del luchador
        function toggleFighterFields() {
            var fields = document.getElementById("fighterFields");
            if (fields.style.display === "none") {
                fields.style.display = "block";
            } else {
                fields.style.display = "none";
            }
        }
    </script>
</head>
<body>

<h1>Mi Perfil</h1>

<!-- Formulario para cambiar el nombre de usuario y contraseña -->
<form action="actualizar_perfil.php" method="post">
    <label for="nombre_usuario">Nombre de usuario:</label>
    <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo $user['nombre_usuario']; ?>" required>
    
    <label for="contrasena">Nueva contraseña:</label>
    <input type="password" id="contrasena" name="contrasena" required>
    
    <input type="submit" value="Actualizar">
</form>

<!-- Ver historial de peleas si el usuario es luchador -->
<?php if ($is_fighter): ?>
    <h2>Historial de Peleas</h2>
    <?php
    // Consulta para obtener el historial de peleas
    $sql_historial = "SELECT fecha, resultado FROM peleas WHERE usuario_id = '$user_id'";
    $result_historial = $conn->query($sql_historial);
    
    if ($result_historial->num_rows > 0) {
        echo "<ul>";
        while ($row = $result_historial->fetch_assoc()) {
            echo "<li>Fecha: " . $row['fecha'] . " - Resultado: " . $row['resultado'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No hay historial de peleas.</p>";
    }
?>

<?php else: ?>
    <h2>Registrar como Luchador</h2>
    <button id="registerFighterButton" onclick="toggleFighterFields()">Registrarse como luchador</button>
    <div id="fighterFields" style="display: none;">
        <form action="registrar_luchador.php" method="post">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            Altura: <input type="number" step="0.01" name="height" required><br>
            Peso: <input type="number" step="0.1" name="weight" required><br>
            Ubicación: <input type="text" name="location" required><br>
            Grupo Sanguíneo:
            <select name="bloodtype" required>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select><br>
            Lateralidad:
            <select name="lateralidad" required>
                <option value="diestro">Derecha</option>
                <option value="zurdo">Izquierda</option>
                <option value="ambi">Ambidiestro</option>
            </select><br>
            
            <input type="submit" value="Registrar como luchador">
        </form>
    </div>
<?php endif; ?>

<a href="ver_perfil.php">Ver Perfil</a>

</body>
</html>
