<?php

class Matchmaking
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function generateMatches(): array
    {
        $query = "
            SELECT 
                email, peso, altura, puntos, victorias, derrotas, empates 
            FROM 
                luchador
            WHERE 
                buscando_pelea = 1
            ORDER BY 
                puntos DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $luchadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($luchadores) < 2) {
            throw new Exception("No hay suficientes luchadores para el matchmaking.");
        }

        $matches = [];
        while (count($luchadores) > 1) {
            $luchador1 = array_shift($luchadores);
            $bestMatch = $this->findBestMatch($luchador1, $luchadores);
            $matches[] = [$luchador1, $bestMatch];

            $luchadores = array_filter($luchadores, function ($luchador) use ($bestMatch) {
                return $luchador['email'] !== $bestMatch['email'];
            });
        }

        return $matches;
    }

    private function findBestMatch(array $fighter, array $candidates): array
    {
        $bestMatch = null;
        $bestDistance = PHP_INT_MAX;

        foreach ($candidates as $candidate) {
            $distance = $this->calculateDistance($fighter, $candidate);
            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $bestMatch = $candidate;
            }
        }

        return $bestMatch;
    }

    private function calculateDistance(array $fighter1, array $fighter2): float
    {
        $weights = [
            'puntos' => 3,
            'peso' => 2,
            'altura' => 1,
        ];

        $distance = 0;

        foreach ($weights as $attribute => $weight) {
            $value1 = $fighter1[$attribute] ?? 0;
            $value2 = $fighter2[$attribute] ?? 0;

            $distance += $weight * pow($value1 - $value2, 2);
        }

        return sqrt($distance);
    }
}
