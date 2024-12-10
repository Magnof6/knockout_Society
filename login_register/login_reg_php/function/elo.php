<?php
require_once dirname(__DIR__) . '/db_connect.php';

class Elo {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function extraerEloLuchadoresBefore($email_Luchador_1, $email_Luchador_2) {
        try {
            $sql = "SELECT puntos FROM luchador WHERE email = ?";

            // Fetch points for the first fighter
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $email_Luchador_1);
            $stmt->execute();
            $result = $stmt->get_result();
            $puntosL_1 = $result->fetch_assoc()['puntos'] ?? null;

            // Fetch points for the second fighter
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $email_Luchador_2);
            $stmt->execute();
            $result = $stmt->get_result();
            $puntosL_2 = $result->fetch_assoc()['puntos'] ?? null;

            if (is_null($puntosL_1) || is_null($puntosL_2)) {
                error_log("ERROR: Elo points not found for fighters: $email_Luchador_1, $email_Luchador_2");
            }

            return [$puntosL_1, $puntosL_2];
        } catch (Exception $e) {
            error_log("ERROR: Failed to fetch Elo points. Message: " . $e->getMessage());
        }
    }

    public function EloLuchadoresAfter($id_lucha, $email_Luchador_1, $email_Luchador_2, $resultado) {
        try {
            // Fetch initial Elo points
            list($puntosL_1, $puntosL_2) = $this->extraerEloLuchadoresBefore($email_Luchador_1, $email_Luchador_2);

            if (is_null($puntosL_1) || is_null($puntosL_2)) {
                error_log("ERROR: Cannot calculate Elo. Missing points for fighters.");
                return;
            }

            // Debugging initial Elo
            error_log("DEBUG: Initial Elo - Fighter 1: $puntosL_1, Fighter 2: $puntosL_2");

            // Example calculation logic (replace with actual Elo logic)
            $nuevo_elo_1 = $puntosL_1 + ($resultado === 1 ? 30 : -30);
            $nuevo_elo_2 = $puntosL_2 + ($resultado === 0 ? 30 : -30);

            // Update new Elo points
            $sql = "UPDATE luchador SET puntos = ? WHERE email = ?";
            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param('is', $nuevo_elo_1, $email_Luchador_1);
            $stmt->execute();
            error_log("DEBUG: Updated Elo for Fighter 1: $nuevo_elo_1");

            $stmt->bind_param('is', $nuevo_elo_2, $email_Luchador_2);
            $stmt->execute();
            error_log("DEBUG: Updated Elo for Fighter 2: $nuevo_elo_2");

        } catch (Exception $e) {
            error_log("ERROR: Failed to update Elo points. Message: " . $e->getMessage());
        }
    }
}
?>