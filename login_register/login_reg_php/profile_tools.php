<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];


$sql = "SELECT * FROM usuario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$sql = "SELECT * FROM luchador WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$is_fighter = $result->num_rows > 0;


$fights = [];
if ($is_fighter) {
    $sql = "SELECT * FROM lucha WHERE id_luchador1 = ? OR id_luchador2 = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_email, $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $fights[] = $row;
    }
}

// cambio de password
$password_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuario SET password = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashed_password, $user_email);
            if ($stmt->execute()) {
                $password_message = "Password changed successfully.";
            } else {
                $password_message = "Error changing password.";
            }
        } else {
            $password_message = "New passwords do not match.";
        }
    } else {
        $password_message = "Current password is incorrect.";
    }
}

// para el registro si el usuario no es luchador
$fighter_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_fighter'])) {
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $location = $_POST['location'];
    $bloodtype = $_POST['bloodtype'];
    $lateralidad = $_POST['lateralidad'];

    $sql = "INSERT INTO luchador (email, altura, peso, ubicacion, grupoSang, lateralidad) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sddsss", $user_email, $height, $weight, $location, $bloodtype, $lateralidad);
    if ($stmt->execute()) {
        $fighter_message = "Successfully registered as a fighter.";
        $is_fighter = true;
    } else {
        $fighter_message = "Error registering as a fighter.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="header">
    <nav>
        <a href="profile_user_me.php" class="header-button">Atras</a>
    </nav>
</div>

<div class="profile-container">
    <h2>Bienvenido, <?php echo htmlspecialchars($user['username']); ?></h2>

    <?php if ($is_fighter): ?>
        <h3>Peleas Pasads</h3>
        <?php if (empty($fights)): ?>
            <p>Aun no has luchado.</p>
        <?php else: ?>
            <table class="fights-table">
                <tr>
                    <th>Fecha</th>
                    <th>Contrincante</th>
                    <th>Ganador</th>
                </tr>
                <?php foreach ($fights as $fight): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fight['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($fight['id_luchador1'] == $user_email ? $fight['id_luchador2'] : $fight['id_luchador1']); ?></td>
                        <td><?php echo htmlspecialchars($fight['id_ganador']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    <?php else: ?>
        <h3>Register as a Fighter</h3>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="fighter-form">
            Altura: <input type="number" step="1" name="height" required><br>
            Peso: <input type="number" step="1" name="weight" required><br>
            Ubicacion: <input type="text" name="location" required><br>
            Tipo Sanguineo:
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
                <option value="diestro">Diestro</option>
                <option value="zurdo">Zurdo</option>
                <option value="ambi">Ambi</option>
            </select><br>
            <input type="submit" name="register_fighter" value="Register as Fighter">
        </form>
        <?php if (!empty($fighter_message)): ?>
            <p><?php echo $fighter_message; ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <h3>Cambio password</h3>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="password-form">
        Password Actual: <input type="password" name="current_password" required><br>
        Password Nueva: <input type="password" name="new_password" required><br>
        Confirmar Password: <input type="password" name="confirm_password" required><br>
        <input type="submit" name="change_password" value="Change Password">
    </form>
    <?php if (!empty($password_message)): ?>
        <p><?php echo $password_message; ?></p>
    <?php endif; ?>
</div>

<div class="footer">
    <a href="https://kick.com/knockoutsociety" target="_blank" id="Kick-floating-button">
        <img src="imagenes/kickkk.png" alt="imagen-kick-Icono Flotante">
    </a>
</div>

<script src="script.js"></script>
</body>
</html>