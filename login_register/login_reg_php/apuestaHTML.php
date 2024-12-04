<?php
session_start();
require_once 'db_connect.php';
require_once 'function/inserts.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";
$error = "";

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "CSRF token inválido.";
    } else {
        // Validar y sanitizar los datos del formulario
        $id_apuesta = uniqid('apuesta_', true); // Generar un ID único
        $id_lucha = filter_input(INPUT_POST, 'id_lucha', FILTER_SANITIZE_NUMBER_INT);
        $luchador_apostado = filter_input(INPUT_POST, 'luchador_apostado', FILTER_SANITIZE_STRING);
        $w = filter_input(INPUT_POST, 'ganadas', FILTER_VALIDATE_INT);
        $l = filter_input(INPUT_POST, 'perdidas', FILTER_VALIDATE_INT);
        $d = filter_input(INPUT_POST, 'empates', FILTER_VALIDATE_INT);

        // Verificar que los datos sean válidos
        if ($id_lucha && $luchador_apostado && $w !== false && $l !== false && $d !== false) {
            // Validar que los valores no sean negativos
            if ($w < 0 || $l < 0 || $d < 0) {
                $error = "Los valores de 'ganadas', 'perdidas' y 'empates' no pueden ser negativos.";
            } else {
                $crearApuesta = new inserts($conn);
                if ($crearApuesta->crearApuesta($_SESSION['user_email'], $id_lucha, $luchador_apostado, $w, $l, $d)) {
                    $mensaje = "Apuesta creada exitosamente.";
                } else {
                    $error = "Error al crear la apuesta. Intenta nuevamente.";
                }
            }
        } else {
            $error = "Por favor, completa todos los campos correctamente.";
        }
    }
}
?>
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
            background-color: #f5f5f5;
        }
        form {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
        }
        form div {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .mensaje, .error {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .mensaje {
            color: #155724;
            background-color: #d4edda;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
        }
    </style>
</head>
<body>
    <h1>Gestión de Apuestas</h1>

    <?php if ($mensaje): ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div>
            <label for="id_lucha">ID Lucha:</label>
            <input type="number" id="id_lucha" name="id_lucha" required>
        </div>
        <div>
            <label for="luchador_apostado">Luchador Apostado:</label>
            <input type="text" id="luchador_apostado" name="luchador_apostado" required>
        </div>
        <div>
            <label for="ganadas">Ganadas (W):</label>
            <input type="number" id="ganadas" name="ganadas" required>
        </div>
        <div>
            <label for="perdidas">Perdidas (L):</label>
            <input type="number" id="perdidas" name="perdidas" required>
        </div>
        <div>
            <label for="empates">Empates (D):</label>
            <input type="number" id="empates" name="empates" required>
        </div>
        <button type="submit">Crear Apuesta</button>
        <button type="button" onclick="window.location.href='index.php';">Cancelar</button>
    </form>
</body>
</html>
