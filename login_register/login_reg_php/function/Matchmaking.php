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

        // Verificar que el luchador existe en la base de datos
        $checkQuery = "SELECT email FROM luchador WHERE email = ?";
        $stmt = $this->db->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ["success" => false, "message" => "El correo no está registrado como luchador."];
        }

        // Actualizar el estado de "buscando pelea"
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
        // Obtener los datos del luchador
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

        // Buscar un oponente disponible
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
        }

        // Emparejar luchadores
        $query = "UPDATE luchador SET emparejado = 1 WHERE email IN (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $userEmail, $opponent['email']);
        $stmt->execute();

        // Registrar la pelea en la tabla lucha
        $this->annadirPelea($userEmail, $opponent['email']);

        return [
            'user' => $userFighter,
            'opponent' => $opponent,
        ];
    }

    public function annadirPelea($userFighter, $userEmail)
    {
        // Asegurarse de usar un id_categoria válido
        $id_categoria = 1;  // Cambiar a un valor de id_categoria válido que exista en la tabla categoria

        // Verificar si el id_categoria existe
        $checkCategoriaQuery = "SELECT id FROM categoria WHERE id = ?";
        $stmt = $this->db->prepare($checkCategoriaQuery);
        $stmt->bind_param("i", $id_categoria);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            die("Error: El id_categoria no existe en la tabla categoria.");
        }

        // Si id_categoria es válido, continuar con la inserción de la pelea
        $num_rondas = 3;
        $ubicacion = 'Ring KnockOut Society';
        $hora_inicio = '19:30:00';
        $estado = 'pendiente';

        $sql = "INSERT INTO lucha (id_luchador1, id_luchador2, id_categoria, num_rondas, fecha, hora_inicio, estado, ubicacion) 
                VALUES (?, ?, ?, ?, CURDATE(), ?, ?, ?)";
        $insert_fighter = $this->db->prepare($sql);

        if ($insert_fighter === false) {
            die('Prepare failed: ' . $this->db->error);
        }

        $insert_fighter->bind_param("ssiisss", $userEmail, $userFighter, $id_categoria, $num_rondas, $hora_inicio, $estado, $ubicacion);
        $insert_fighter->execute();
        $insert_fighter->close();
    }
}
