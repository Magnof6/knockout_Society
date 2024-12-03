<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Apuestas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
        }
        form {
            margin-bottom: 20px;
            display: none; /* Oculto por defecto */
        }
        form div {
            margin-bottom: 10px;
        }
        #apuestaForm {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const toggleButton = document.getElementById("toggleFormButton");
            const form = document.getElementById("apuestaForm");
            const cancelButton = document.getElementById("cancelFormButton");

            toggleButton.addEventListener("click", () => {
                form.style.display = "block"; // Mostrar el formulario
                toggleButton.style.display = "none"; // Ocultar el botón
            });

            cancelButton.addEventListener("click", () => {
                form.style.display = "none"; // Ocultar el formulario
                toggleButton.style.display = "inline-block"; // Mostrar el botón
            });
        });
    </script>
</head>
<body>
    <h1>Gestión de Apuestas</h1>

    <!-- Botón para abrir el formulario -->
    <button id="toggleFormButton">Realizar Apuesta</button>

    <!-- Formulario para crear una apuesta -->
    <form method="POST" action="" id="apuestaForm">
        <h2>Crear Apuesta</h2>
        <div>
            <label for="id_apuesta">ID Apuesta:</label>
            <input type="text" id="id_apuesta" name="id_apuesta" required>
        </div>
        <div>
            <label for="email_usuario">Email Usuario:</label>
            <input type="email" id="email_usuario" name="email_usuario" required>
        </div>
        <div>
            <label for="id_lucha">ID Lucha:</label>
            <input type="text" id="id_lucha" name="id_lucha" required>
        </div>
        <div>
            <label for="luchador_apostado">Luchador Apostado:</label>
            <input type="text" id="luchador_apostado" name="luchador_apostado" required>
        </div>
        <div>
            <label for="w">Ganadas (W):</label>
            <input type="number" id="w" name="w" required>
        </div>
        <div>
            <label for="l">Perdidas (L):</label>
            <input type="number" id="l" name="l" required>
        </div>
        <div>
            <label for="d">Empates (D):</label>
            <input type="number" id="d" name="d" required>
        </div>
        <div>
            <label for="total">Total (Opcional):</label>
            <input type="number" id="total" name="total">
        </div>
        <button type="submit" name="crear_apuesta">Crear Apuesta</button>
        <button type="button" id="cancelFormButton">Cancelar</button>
    </form>

    <?php
session_start();
require_once 'db_connect.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Obtener peleas de la base de datos
$sql = "SELECT * FROM lucha";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$fights = [];
while ($row = $result->fetch_assoc()) {
    $fights[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peleas Pasadas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos principales */
        #video-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            position: relative;
            width: 80%;
            max-width: 900px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .popup-content iframe {
            width: 100%;
            height: 500px;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            color: black;
            cursor: pointer;
        }

        .profile-container {
            background-color: white;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 40px;
            width: 80%;
            max-width: 1200px;
            margin: 0 auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: black;
        }

        .fights-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        .fights-table th, .fights-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .fights-table th {
            background-color: dodgerblue;
            color: white;
            font-weight: bold;
        }

        .fights-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .fights-table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
    <script>
        function openPopup(videoUrl) {
            document.getElementById('video-popup').style.display = 'flex';
            document.getElementById('video-frame').src = videoUrl;
        }

        function closePopup() {
            document.getElementById('video-popup').style.display = 'none';
            document.getElementById('video-frame').src = '';
        }
    </script>
</head>
<body>
    <div class="profile-container">
        <h2>Resultados de Peleas</h2>
        <table class="fights-table">
            <thead>
            <tr>
                <th>Luchador 1</th>
                <th>Luchador 2</th>
                <th>Categoría</th>
                <th>Ganador</th>
                <th>Número de Rondas</th>
                <th>Fecha</th>
                <th>Hora de Inicio</th>
                <th>Hora Final</th>
                <th>Estado</th>
                <th>Ubicación</th>
                <th>Ver pelea</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($fights as $fight): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fight['id_luchador1']); ?></td>
                    <td><?php echo htmlspecialchars($fight['id_luchador2']); ?></td>
                    <td><?php echo htmlspecialchars($fight['id_categoria']); ?></td>
                    <td><?php echo htmlspecialchars($fight['id_ganador']); ?></td>
                    <td><?php echo htmlspecialchars($fight['num_rondas']); ?></td>
                    <td><?php echo htmlspecialchars($fight['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($fight['hora_inicio']); ?></td>
                    <td><?php echo htmlspecialchars($fight['hora_final']); ?></td>
                    <td><?php echo htmlspecialchars($fight['estado']); ?></td>
                    <td><?php echo htmlspecialchars($fight['ubicacion']); ?></td>
                    <td>
                        <?php
                        $id_lucha = $fight['id_lucha'];
                        $sql_replay = "SELECT url FROM replays WHERE id_lucha = ?";
                        $stmt_replay = $conn->prepare($sql_replay);
                        $stmt_replay->bind_param("i", $id_lucha);
                        $stmt_replay->execute();
                        $result_replay = $stmt_replay->get_result();
                        $replay = $result_replay->fetch_assoc();
                        ?>
                        <?php if (!empty($replay['url'])): ?>
                            <a href="<?php echo htmlspecialchars($replay['url']); ?>" target="_blank">Ver</a>
                            <a href="javascript:void(0);" onclick="openPopup('<?php echo htmlspecialchars($replay['url']); ?>')">Popup</a>
                        <?php else: ?>
                            <span>No disponible</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pop-up para el video -->
    <div id="video-popup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <iframe id="video-frame" src="" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
</body>
</html>
