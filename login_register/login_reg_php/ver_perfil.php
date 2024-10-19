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
        <form action="registrar_luchador.php" method="post">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="submit" value="Registrarse como luchador">
        </form>
    <?php endif; ?>

    <a href="ver_perfil.php">Ver Perfil</a>

    </body>
</html>
