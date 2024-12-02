<?php

class Matchmaking
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function alternarEstadoBP(string $email, int $valor): array
    {
        if (empty($email)) {
            return ["success" => false, "message" => "El correo electrónico es obligatorio."];
        }

        $checkQuery = "SELECT email FROM luchador WHERE email = ?";
        $stmt = $this->db->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ["success" => false, "message" => "El correo no está registrado como luchador."];
        }

        $updateQuery = "UPDATE luchador SET buscando_pelea = ? WHERE email = ?";
        $stmt = $this->db->prepare($updateQuery);
        $stmt->bind_param("is", $valor, $email);

        if ($stmt->execute()) {
            return ["success" => true, "message" => "Estado actualizado correctamente."];
        } else {
            return ["success" => false, "message" => "Error al actualizar el estado: " . $stmt->error];
        }
    }

    public function generateMatchForUser(string $userEmail): ?array
    {
        $query = "
            SELECT 
                luchador.email, usuario.username, usuario.nombre, usuario.apellido, 
                luchador.peso, luchador.altura, luchador.puntos
            FROM 
                luchador
            JOIN 
                usuario ON luchador.email = usuario.email
            WHERE 
                luchador.email = ? 
                AND luchador.buscando_pelea = 1
                AND luchador.emparejado = 0
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        $userFighter = $result->fetch_assoc();

        if (!$userFighter) {
            throw new Exception("No estás disponible para el matchmaking.");
        }

        $query = "
            SELECT 
                luchador.email, usuario.username, usuario.nombre, usuario.apellido, 
                luchador.peso, luchador.altura, luchador.puntos
            FROM 
                luchador
            JOIN 
                usuario ON luchador.email = usuario.email
            WHERE 
                luchador.email != ? 
                AND luchador.buscando_pelea = 1
                AND luchador.emparejado = 0
            ORDER BY RAND() LIMIT 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $opponent = $stmt->get_result()->fetch_assoc();

        if (!$opponent) {
            throw new Exception("No se encontraron luchadores disponibles para el matchmaking.");
        }else{
            $query = "UPDATE luchador SET emparejado = 1 WHERE email IN (?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ss", $userEmail, $opponent['email']);
            $stmt->execute();
    
            $this->annadirPelea($userEmail, $opponent['email']);

            return [
                'user' => $userFighter,
                'opponent' => $opponent,
            ];
   
            //$this->annadirPelea($userFighter , $opponent);
        }
        
    }
    public function annadirPelea($userFighter, $userEmail) {
        $usernames = $this->extraerDatosParaAnnadir($userEmail, $userFighter);
        if ($usernames) {
            $user_a = $usernames[0];
            $user_b = $usernames[1];
        }
    
        $num_rondas = 3;
        $id_categoria = 80;
        $ubicacion = 'Ring KnockOut Society';
        $hora_inicio = '19:30:00';
    
        $sql = "INSERT INTO lucha (id_luchador1, id_luchador2, id_categoria, num_rondas, fecha, hora_inicio, ubicacion) 
                VALUES (?, ?, ?, ?, CURDATE(), ?, ?)";
        $insert_fighter = $this->db->prepare($sql);
        if ($insert_fighter === false) {
            die('Prepare failed: ' . $this->db->error);
        }
        $insert_fighter->bind_param("ssiiSS", $user_a, $user_b, $id_categoria, $num_rondas, $hora_inicio, $ubicacion);
        $insert_fighter->execute();
        $insert_fighter->close();
    }

    public function extraerDatosParaAnnadir($email_Luchador_1 , $email_Luchador_2){

        $sql = "SELECT username FROM usuario WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email_Luchador_1);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $username_1 = $row['username'];

        $sql = "SELECT username FROM usuario WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email_Luchador_2);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $username_2 = $row['username'];


        $usernames = [$username_1 , $username_2];

        return $usernames;
/* 
        $sql = "SELECT puntos FROM usuario WHERE email = :email_Luchador_2";
        $stmt = $this->conn->prepare($sql);
        $stmt -> bindParam(':email_Luchador_2' , $email_Luchador_2, PDO::PARAM_STR);
        $stmt->execute();
        $puntosL_2 = $stmt->fetch(PDO::FETCH_ASSOC);
        $puntosL_2 = $puntosL_2['puntos'];

        $sql = "SELECT puntos FROM usuario WHERE email = :email_Luchador_2";
        $stmt = $this->conn->prepare($sql);
        $stmt -> bindParam(':email_Luchador_2' , $email_Luchador_2, PDO::PARAM_STR);
        $stmt->execute();
        $puntosL_2 = $stmt->fetch(PDO::FETCH_ASSOC);
        $puntosL_2 = $puntosL_2['puntos']; */


        /** PARA FUTURO
         * UPDATE lucha
        *SET estado = 'finalizada', hora_final = CURTIME()
        *WHERE id_lucha = X;
         */
    }
}
