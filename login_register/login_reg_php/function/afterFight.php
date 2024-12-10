<?php
require_once dirname(__DIR__) . '/db_connect.php';
require 'apuestas.php';
require 'elo.php';

class AfterFight {
    private $conn;
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Importante: $email_Luchador_1 debe ser el ganador en caso de haberlo.
    public function afterFightTerminada($id_lucha, $email_Luchador_1, $email_Luchador_2, $ganador) {
        try {
            // Determine the result
            if ($ganador == $email_Luchador_1) {
                $resultado = 1;
            } elseif ($ganador == $email_Luchador_2) {
                $resultado = 0;
            } else {
                $resultado = 0.5;
            }

            // Debugging
            error_log("DEBUG: Starting Elo, record, and field reset updates for fight ID: $id_lucha");

            // Update Elo points
            $elo = new Elo($this->conn);
            $elo->EloLuchadoresAfter($id_lucha, $email_Luchador_1, $email_Luchador_2, $resultado);

            $apuesta = new Apuestas($this->conn);
            $apuesta->ActualizadorGeneralApuestas($id_lucha, $resultado);

            // Update fighting records
            if ($resultado == 1) {
                // Fighter 1 wins, Fighter 2 loses
                $sql = "UPDATE luchador SET victorias = victorias + 1 WHERE email = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('s', $email_Luchador_1);
                $stmt->execute();
                error_log("DEBUG: Updated wins for Fighter 1: $email_Luchador_1");

                $sql = "UPDATE luchador SET derrotas = derrotas + 1 WHERE email = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('s', $email_Luchador_2);
                $stmt->execute();
                error_log("DEBUG: Updated losses for Fighter 2: $email_Luchador_2");

            } elseif ($resultado == 0) {
                // Fighter 2 wins, Fighter 1 loses
                $sql = "UPDATE luchador SET victorias = victorias + 1 WHERE email = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('s', $email_Luchador_2);
                $stmt->execute();
                error_log("DEBUG: Updated wins for Fighter 2: $email_Luchador_2");

                $sql = "UPDATE luchador SET derrotas = derrotas + 1 WHERE email = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('s', $email_Luchador_1);
                $stmt->execute();
                error_log("DEBUG: Updated losses for Fighter 1: $email_Luchador_1");

            } else {
                // Draw
                $sql = "UPDATE luchador SET empates = empates + 1 WHERE email = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('s', $email_Luchador_1);
                $stmt->execute();
                error_log("DEBUG: Updated draws for Fighter 1: $email_Luchador_1");

                $stmt->bind_param('s', $email_Luchador_2);
                $stmt->execute();
                error_log("DEBUG: Updated draws for Fighter 2: $email_Luchador_2");
            }

            // Reset fields in luchador for both participants
            $reset_sql = "UPDATE luchador SET buscando_pelea = 0, emparejado = 0, empezarPelea = 0 WHERE email = ?";
            $stmt = $this->conn->prepare($reset_sql);

            $stmt->bind_param('s', $email_Luchador_1);
            $stmt->execute();
            error_log("DEBUG: Reset fields for Fighter 1: $email_Luchador_1");

            $stmt->bind_param('s', $email_Luchador_2);
            $stmt->execute();
            error_log("DEBUG: Reset fields for Fighter 2: $email_Luchador_2");

            // Debugging success
            error_log("DEBUG: Finished updates for fight ID: $id_lucha");

        } catch (Exception $e) {
            error_log("ERROR: AfterFight failed with message: " . $e->getMessage());
        }
    }


   public function afterFightCancelada($id_lucha){
    $apuestaC = new Apuestas($this->conn);
    $apuestaC->cancelarApuestas($id_lucha);
   }

   public function comparadorPeleando($id_lucha) {
    if (!$this->conn) {
        return false;
    }

    $sql = "SELECT hora_final, ganador, num_rondas FROM peleando WHERE id_lucha = ?";
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("i", $id_lucha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return false;
    } else {
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        if (count($rows) == 2) {
            $firstRow = $rows[0];
            foreach ($rows as $row) {
                if ($row !== $firstRow) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
}