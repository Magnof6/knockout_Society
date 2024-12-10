<?php
session_start();
require_once 'db_connect.php';
require_once 'function/afterFight.php';

if (isset($_SESSION['user_email']) && isset($_SESSION['current_fight_id'])) {
    $user_email = $_SESSION['user_email'];
    $fight_id = $_SESSION['current_fight_id'];
} else {
    header("Location: fight.php?error=No active fight to finalize.");
    exit();
}

// Validate form inputs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $winner = $_POST['ganador'] ?? null;
    $end_time = $_POST['hora_final'] ?? null;
    $rounds = $_POST['numero_de_rondas'] ?? null;

    if (!$winner || !$end_time || !$rounds) {
        header("Location: fight.php?error=Incomplete form submission.");
        exit();
    }

    try {
        // Fetch fight details to verify user involvement
        $sql = "SELECT id_luchador1, id_luchador2 FROM lucha WHERE id = ? AND estado = 'luchando'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $fight_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $fight = $result->fetch_assoc();

        if (!$fight) {
            header("Location: Fight.php?error=Fight not found or already finalized.");
            exit();
        }

        // Determine fighter emails
        $fighter1 = $fight['id_luchador1'];
        $fighter2 = $fight['id_luchador2'];

        // Update fight status to finalized and set the winner
        $update_sql = "UPDATE lucha SET estado = 'finalizada', ganador = ?, hora_final = ?, rondas = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('ssii', $winner, $end_time, $rounds, $fight_id);
        $update_stmt->execute();

        // Update Elo and records
        $afterFight = new AfterFight($conn);
        $afterFight->afterFightTerminada($fight_id, $fighter1, $fighter2, $winner);

        // Update fighter records in the luchador table
        if ($winner === $fighter1) {
            $win_update_sql = "UPDATE luchador SET victorias = victorias + 1 WHERE email = ?";
            $loss_update_sql = "UPDATE luchador SET derrotas = derrotas + 1 WHERE email = ?";
            $winner_email = $fighter1;
            $loser_email = $fighter2;
        } elseif ($winner === $fighter2) {
            $win_update_sql = "UPDATE luchador SET victorias = victorias + 1 WHERE email = ?";
            $loss_update_sql = "UPDATE luchador SET derrotas = derrotas + 1 WHERE email = ?";
            $winner_email = $fighter2;
            $loser_email = $fighter1;
        } else {
            $draw_update_sql = "UPDATE luchador SET empates = empates + 1 WHERE email = ?";
            $stmt = $conn->prepare($draw_update_sql);
            $stmt->bind_param('s', $fighter1);
            $stmt->execute();
            $stmt->bind_param('s', $fighter2);
            $stmt->execute();
        }

        // Execute win/loss updates
        if (isset($win_update_sql) && isset($loss_update_sql)) {
            $stmt = $conn->prepare($win_update_sql);
            $stmt->bind_param('s', $winner_email);
            $stmt->execute();

            $stmt = $conn->prepare($loss_update_sql);
            $stmt->bind_param('s', $loser_email);
            $stmt->execute();
        }

        // Success feedback
        header("Location: fight.php?success=Fight finalized, winner updated, and records adjusted.");
    } catch (Exception $e) {
        // Error handling
        header("Location: fight.php?error=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: fight.php?error=Invalid request method.");
}
?>
