<?php
require_once 'db_connect.php';
session_start();

$error_message = "";

$criterio = isset($_GET['criterio']) ? $_GET['criterio'] : 'puntos';

$criterios_validos = ['puntos', 'email', 'victorias', 'empates', 'derrotas'];
if (!in_array($criterio, $criterios_validos)) {
    $error_message = "Criterio no válido.";
} else {
    $sql = "SELECT email, puntos, victorias, empates, derrotas FROM luchador ORDER BY $criterio DESC";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $error_message = "Error al preparar la consulta: " . $conn->error;
    } else {
        $stmt->execute();
        $result = $stmt->get_result();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos básicos para el menú */
        .menu {
            background-color: #333;
            overflow: hidden;
        }
        .menu ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        .menu ul li {
            float: left;
        }
        .menu ul li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        .menu ul li a:hover {
            background-color: #575757;
        }
    </style>
    <script>
        function toggleUserSection() {
            alert('Funcionalidad en desarrollo: Perfiles de otras personas');
        }
    </script>
</head>
<body>
    <div id="menu" class="menu">
        <ul>
            <li><a href="#" onclick="toggleUserSection()">Perfiles de otras personas</a></li>
            <li><a href="#">Inicio</a></li>
            <li><a href="#">Acerca de</a></li>
            <li><a href="Contacto.php">Servicios</a></li>
            <li><a href="Fight.php">Buscar Pelea</a></li>
            <li><a href="Watch.php">Ver Peleas</a></li>
            <li><a href="Ranking.php">Ranking</a></li>
        </ul>
    </div>

    <div class="container">
        <h1>Ranking de Luchadores</h1>
        
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="GET" action="Ranking.php">
            <label for="criterio">Ordenar por:</label>
            <select name="criterio" id="criterio">
                <option value="puntos" <?php echo $criterio === 'puntos' ? 'selected' : ''; ?>>Puntos</option>
                <option value="email" <?php echo $criterio === 'email' ? 'selected' : ''; ?>>Email</option>
                <option value="victorias" <?php echo $criterio === 'victorias' ? 'selected' : ''; ?>>Victorias</option>
                <option value="empates" <?php echo $criterio === 'empates' ? 'selected' : ''; ?>>Empates</option>
                <option value="derrotas" <?php echo $criterio === 'derrotas' ? 'selected' : ''; ?>>Derrotas</option>
            </select>
            <button type="submit">Actualizar</button>
        </form>

        <?php if (isset($result) && $result->num_rows > 0): ?>
            <table border="1">
                <tr>
                    <th>Email</th>
                    <th>Puntos</th>
                    <th>Victorias</th>
                    <th>Empates</th>
                    <th>Derrotas</th>
                </tr>
                <?php while ($fila = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['email']); ?></td>
                        <td><?php echo htmlspecialchars($fila['puntos']); ?></td>
                        <td><?php echo htmlspecialchars($fila['victorias']); ?></td>
                        <td><?php echo htmlspecialchars($fila['empates']); ?></td>
                        <td><?php echo htmlspecialchars($fila['derrotas']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No se encontraron resultados.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
