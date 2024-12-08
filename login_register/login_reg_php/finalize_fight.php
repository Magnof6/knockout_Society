<?php
session_start();
require_once 'db_connect.php';

// Verify if the user is authenticated and has a current fight ID in session
if (isset($_SESSION['user_email']) && isset($_SESSION['current_fight_id'])) {
    $user_email = $_SESSION['user_email'];
    $fight_id = $_SESSION['current_fight_id'];
} else {
    // Redirect to fight.php with error message
    header("Location: fight.php?error=No active fight to finalize.");
    exit();
}

// Fetch fight details to verify user involvement
$sql = "SELECT id_luchador1, id_luchador2 FROM lucha WHERE id = ? AND estado = 'luchando'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $fight_id);
$stmt->execute();
$result = $stmt->get_result();
$fight = $result->fetch_assoc();

if (!$fight) {
    // Fight not found or not in 'luchando' state
    header("Location: Fight.php?error=Fight not found or already finalized.");
    exit();
}

// Check if the user is one of the fighters
if ($fight['id_luchador1'] != $user_email && $fight['id_luchador2'] != $user_email) {
    // User not authorized to finalize this fight
    header("Location: Fight.php?error=Unauthorized to finalize this fight.");
    exit();
}

// Fetch fighters' names for the select dropdown
$sql = "SELECT u.nombre, u.email FROM usuario u WHERE u.email = ? OR u.email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $fight['id_luchador1'], $fight['id_luchador2']);
$stmt->execute();
$result = $stmt->get_result();
$fighters = $result->fetch_all(MYSQLI_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and process inputs
    $hora_final = date('Y-m-d', strtotime($_POST['hora_final']));
    $ganador = $_POST['ganador'];
    $num_rondas = $_POST['num_rondas'];
    
    // Validation logic
    if (!in_array($ganador, array($fight['id_luchador1'], $fight['id_luchador2']))) {
        die("Invalid winner email.");
    }
    if (!is_numeric($num_rondas) || $num_rondas < 1) {
        die("Number of rounds must be a positive integer.");
    }
    
    // Insert into peleando table
    $sql = "INSERT INTO peleando (email_luchador, hora_final, ganador, num_rondas, id_lucha) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssiii', $user_email, $hora_final, $ganador, $num_rondas, $fight_id);
    $stmt->execute();
    
    // Update lucha table
    $sql = "UPDATE lucha SET estado = 'finalizado' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $fight_id);
    $stmt->execute();
    
    // Update luchador table for both fighters
    $sql = "UPDATE luchador SET emparejado = 0, empezarPelea = 0 WHERE email = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $fight['id_luchador1'], $fight['id_luchador2']);
    $stmt->execute();
    
    // Unset the fight ID from session
    unset($_SESSION['current_fight_id']);
    
    // Redirect back to fight.php with success message
    header("Location: fight.php?success=Fight finalized successfully.");
    exit();
}
?>

<!-- HTML form -->
<form method="POST" action="">
    <label for="hora_final">Hora Final:</label>
    <input type="datetime-local" name="hora_final" required><br>
    
    <label for="ganador">Ganador:</label>
    <select name="ganador" required>
        <?php foreach ($fighters as $fighter): ?>
            <option value="<?= $fighter['email'] ?>"><?= $fighter['nombre'] ?></option>
        <?php endforeach; ?>
    </select><br>
    
    <label for="num_rondas">Número de Rondas:</label>
    <input type="number" name="num_rondas" min="1" required><br>
    
    <button type="submit">Finalizar Pelea</button>
</form>