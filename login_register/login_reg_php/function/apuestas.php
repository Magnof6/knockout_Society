<?php
require_once dirname(__DIR__) . '/db_connect.php';

class Apuestas {
    public $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function ActualizadorGeneralApuestas($id_lucha, $resultado) {
        try {
            // Fetch all bets for the fight
            $sql = "SELECT email_usuario, luchador_apostado, w, l, d FROM apuesta WHERE id_lucha = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id_lucha);
            $stmt->execute();
            $result = $stmt->get_result();

            // Process each user's bets
            while ($row = $result->fetch_assoc()) {
                $email_usuario = $row['email_usuario'];
                $luchador_apostado = $row['luchador_apostado'];
                $bet_w = $row['w'];
                $bet_l = $row['l'];
                $bet_d = $row['d'];

                // Calculate the total for the bet
                $total = 0;
                if ($resultado == 1 && $luchador_apostado == $row['luchador_apostado']) {
                    // Fighter wins
                    $total += $bet_w * 2; // Double the win bet
                    $total -= $bet_l + $bet_d; // Lose the loss and draw bets
                } elseif ($resultado == 0 && $luchador_apostado == $row['luchador_apostado']) {
                    // Fighter loses
                    $total += $bet_l * 2; // Double the loss bet
                    $total -= $bet_w + $bet_d; // Lose the win and draw bets
                } elseif ($resultado == 0.5) {
                    // Draw
                    $total += $bet_d * 2; // Double the draw bet
                    $total -= $bet_w + $bet_l; // Lose the win and loss bets
                }

                // Update the total in the apuesta table
                $update_sql = "UPDATE apuesta SET total = ? WHERE email_usuario = ? AND id_lucha = ?";
                $update_stmt = $this->conn->prepare($update_sql);
                $update_stmt->bind_param("dsi", $total, $email_usuario, $id_lucha);
                $update_stmt->execute();

                // Update the user's wallet balance
                $wallet_update_sql = "UPDATE usuario SET cartera = cartera + ? WHERE email = ?";
                $wallet_stmt = $this->conn->prepare($wallet_update_sql);
                $wallet_stmt->bind_param("ds", $total, $email_usuario);
                $wallet_stmt->execute();

                // Debugging log
                error_log("DEBUG: Updated total: $total and wallet for user: $email_usuario");
            }
        } catch (Exception $e) {
            error_log("ERROR: Failed to process bets. Message: " . $e->getMessage());
        }
    }
}
?>