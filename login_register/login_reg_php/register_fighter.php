<?php
session_start();
require_once 'db_connect.php'; 
require_once 'function/inserts.php'; 

if (!isset($_SESSION['user_email']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$error_message = "";
$success_message = "";
$user_email = $_SESSION['user_email'];
$insert = new Inserts($conn);


// Verificar si el usuario ya es un luchador
$sql = "SELECT * FROM luchador WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<p style='color: red;'>Ya estás registrado como luchador.</p>";
    exit();
}


//Recolectamos datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $altura = $_POST['altura'];
    $peso = $_POST['peso'];
    $ubicacion = $_POST['ubicacion'];
    $grupoSang = $_POST['grupoSang'];
    $lateralidad = $_POST['lateralidad'];

    if (empty($altura) || empty($peso) || empty($ubicacion) || empty($grupoSang) || empty($lateralidad)) {
        $error_message = "Todos los campos son requeridos.";
    } else {
        $result = $insert->registerFighter($user_email, $altura, $peso, $ubicacion, $grupoSang, $lateralidad);
        
        if ($result['success']) {
            $success_message = $result['message'];
        } else {
            $error_message = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registrar como Luchador</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="header">
            <nav>
                <a href="index.php" class="header-button">Inicio</a>
            </nav>
        </div>
        <h2>Registrar como Luchador</h2>
        <?php
        if (!empty($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        if (!empty($success_message)) {
            echo "<p style='color: green;'>$success_message</p>";
        }
        ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label for="altura">Altura (cm):</label>
            <input type="number" name="altura" required><br>

            <label for="peso">Peso (kg):</label>
            <input type="number" step="0.1" name="peso" required><br>

            <label for="ubicacion">Ubicación:</label>
            <input type="text" name="ubicacion" required><br>

            <label for="grupoSang">Grupo Sanguíneo:</label>
            <select name="grupoSang" required>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select><br>

            <label for="lateralidad">Lateralidad:</label>
            <select name="lateralidad" required>
                <option value="diestro">Derecha</option>
                <option value="zurdo">Izquierda</option>
                <option value="ambi">Ambidiestro</option>
            </select><br>

            <input type="submit" value="Registrar como luchador">
        </form>

        <?php include 'footer.php'; ?>
    </body>
</html>
s