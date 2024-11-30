<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../function/Matchmaking.php';

class MatchmakingTest extends TestCase
{
    private $db;
    private $matchmaking;

    protected function setUp(): void
    {
        $host = 'serverkn.ddns.net';
        $username = 'root';
        $password = 'PeleaDown$666';
        $dbname = 'knockout_dev';

        $this->db = new mysqli($host, $username, $password, $dbname);

        if ($this->db->connect_error) {
            $this->fail("Error al conectar con la base de datos: " . $this->db->connect_error);
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
        $this->db->query("TRUNCATE TABLE luchador");
        $this->db->query("TRUNCATE TABLE usuario");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

        $this->matchmaking = new Matchmaking($this->db);
    }
    

    protected function tearDown(): void
    {
        if ($this->db) {
            $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
            $this->db->query("TRUNCATE TABLE luchador");
            $this->db->query("TRUNCATE TABLE usuario");
            $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
            $this->db = null;
        }
    }

    public function testGenerateMatches_WithValidFighters_ReturnsCorrectMatches()
    {
        $this->db->query("
            INSERT INTO usuario (email, username, password, nombre, apellido, edad, sexo, cartera)
            VALUES 
                ('luchador1@example.com', 'luchador1', 'password', 'Nombre1', 'Apellido1', 30, 'masculino', 100),
                ('luchador2@example.com', 'luchador2', 'password', 'Nombre2', 'Apellido2', 28, 'masculino', 100),
                ('luchador3@example.com', 'luchador3', 'password', 'Nombre3', 'Apellido3', 32, 'masculino', 100),
                ('luchador4@example.com', 'luchador4', 'password', 'Nombre4', 'Apellido4', 25, 'masculino', 100)
        ");

        $this->db->query("
            INSERT INTO luchador (email, peso, altura, victorias, derrotas, empates, puntos, grupoSang, ubicacion, lateralidad, buscando_pelea)
            VALUES 
                ('luchador1@example.com', 80, 180, 10, 2, 1, 100, 'A+', 'Ciudad1', 'diestro', 1),
                ('luchador2@example.com', 78, 175, 9, 3, 2, 95, 'B+', 'Ciudad1', 'zurdo', 1),
                ('luchador3@example.com', 82, 185, 8, 4, 1, 90, 'O-', 'Ciudad1', 'diestro', 1),
                ('luchador4@example.com', 76, 170, 7, 5, 0, 85, 'AB+', 'Ciudad2', 'zurdo', 1)
        ");

        $matches = $this->matchmaking->generateMatches();

        $this->assertCount(2, $matches);
        $this->assertEquals('luchador1@example.com', $matches[0][0]['email']);
        $this->assertEquals('luchador2@example.com', $matches[0][1]['email']);
        $this->assertEquals('luchador3@example.com', $matches[1][0]['email']);
        $this->assertEquals('luchador4@example.com', $matches[1][1]['email']);
    }

    public function testGenerateMatches_WithInsufficientFighters_ThrowsException()
    {
        $this->db->query("
            INSERT INTO usuario (email, username, password, nombre, apellido, edad, sexo, cartera)
            VALUES 
                ('luchador1@example.com', 'luchador1', 'password', 'Nombre1', 'Apellido1', 30, 'masculino', 100)
        ");

        $this->db->query("
            INSERT INTO luchador (email, peso, altura, victorias, derrotas, empates, puntos, grupoSang, ubicacion, lateralidad, buscando_pelea)
            VALUES 
                ('luchador1@example.com', 80, 180, 10, 2, 1, 100, 'A+', 'Ciudad1', 'diestro', 1)
        ");

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("No hay suficientes luchadores para el matchmaking.");

        $this->matchmaking->generateMatches();
    }
}
