<?php

/**
 * Esta clase encapsula toda la lógica de inserts a las diferentes tablas,
 * las que estan ahora mismo implementadas son las de usuario y luchador.
 * 
 * Si quereis insertar cualquier cosa hacedlo por aquí.
 * 
 */
// session_start();
require_once dirname(__DIR__) . '/db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Class Buscando_pelea{

    public function alternarEstadoBP($email , $valor){
        $error_message = "";
        $success_message = "";

        if (empty($email)) {
            return ["success" => false, "message" => "All fields are required."];
        }

        $check_luchador = $this->conn->prepare("SELECT email FROM luchador WHERE email = ?");
        $check_luchador->bind_param("s", $email);
        $check_luchador->execute();
        $result = $check_luchador->get_result();

        if ($result->num_rows == 0) {
            return ["success" => false, "message" => "El correo no está registrado como luchador."];
        }

            $sql = "UPDATE luchador SET buscando_pelea = ? WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $valor, $email);        
                if ($stmt->execute()){
                    return "State changed successfully.";
                }else {
                    return "Error changing state: " . $stmt->error;
                    }
        }

    }

    
    
