<?php

class Matchmaking
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function generateMatchForUser(string $userEmail): ?array
    {
        $query = "
            SELECT 
                luchador.email, usuario.username, usuario.nombre, usuario.apellido, 
                luchador.peso, luchador.altura, luchador.puntos, usuario.edad, usuario.sexo
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
                luchador.peso, luchador.altura, luchador.puntos, usuario.edad, usuario.sexo
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

        $query = "
            INSERT INTO lucha (id_luchador1, id_luchador2, estado, fecha, hora_inicio, ubicacion) 
            VALUES (?, ?, 'pendiente', CURDATE(), CURTIME(), ?)
        ";
        $stmt = $this->db->prepare($query);
        $ubicacion = "Arena Central";
        $stmt->bind_param("sss", $userEmail, $opponent['email'], $ubicacion);
        $stmt->execute();

        $query = "UPDATE luchador SET emparejado = 1 WHERE email IN (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $userEmail, $opponent['email']);
        $stmt->execute();

        return [
            'user' => $userFighter,
            'opponent' => $opponent,
        ];
    }

    public function finishMatch(int $matchId, string $winnerEmail): bool
    {
        $query = "
            UPDATE lucha 
            SET estado = 'finalizada', id_ganador = ?, hora_final = CURTIME() 
            WHERE id_lucha = ?
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $winnerEmail, $matchId);
        $stmt->execute();

        $query = "
            UPDATE luchador 
            SET emparejado = 0 
            WHERE email IN (
                SELECT id_luchador1 FROM lucha WHERE id_lucha = ? UNION 
                SELECT id_luchador2 FROM lucha WHERE id_lucha = ?
            )
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $matchId, $matchId);
        return $stmt->execute();
    }
}
