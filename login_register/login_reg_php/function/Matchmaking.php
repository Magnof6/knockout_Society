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
        }
/**$query = "
            INSERT INTO lucha (id_luchador1, id_luchador2, estado, fecha, hora_inicio, ubicacion) 
            VALUES (?, ?, 'pendiente', CURDATE(), CURTIME(), ?)
        ";
        $stmt = $this->db->prepare($query);
        $ubicacion = "Arena Central";
        $stmt->bind_param("sss", $userEmail, $opponent['email'], $ubicacion);
        $stmt->execute();
*/
/**No descomentar esto, si no el matchmaking no funca*/
        $query = "UPDATE luchador SET emparejado = 1 WHERE email IN (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $userEmail, $opponent['email']);
        $stmt->execute();

        return [
            'user' => $userFighter,
            'opponent' => $opponent,
        ];
    }
}
