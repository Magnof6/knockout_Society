<?php

use PHPUnit\Framework\TestCase;

// Incluye el archivo de la clase Apuestas
require_once __DIR__ . '/../function/apuestas.php';

class ApuestasTest extends TestCase
{
    private $db;
    private $apuestas;
    private $backupData = [];

    protected function setUp(): void
    {
        $dsn = 'mysql:host=serverkn.ddns.net;dbname=knockout';
        $username = 'root';
        $password = 'PeleaDown$666';
        $this->db = new PDO($dsn, $username, $password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->apuestas = new Apuestas($this->db);

        $this->backupDatabase(); // Crea un respaldo antes de resetear
        $this->resetDatabase();
        $this->initializeDatabase();
    }

    private function backupDatabase(): void
    {
        $this->backupData['usuario'] = $this->db->query("SELECT * FROM usuario")->fetchAll(PDO::FETCH_ASSOC);
        $this->backupData['luchador'] = $this->db->query("SELECT * FROM luchador")->fetchAll(PDO::FETCH_ASSOC);
        $this->backupData['categoria'] = $this->db->query("SELECT * FROM categoria")->fetchAll(PDO::FETCH_ASSOC);
        $this->backupData['lucha'] = $this->db->query("SELECT * FROM lucha")->fetchAll(PDO::FETCH_ASSOC);
        $this->backupData['apuesta'] = $this->db->query("SELECT * FROM apuesta")->fetchAll(PDO::FETCH_ASSOC);
        $this->backupData['replays'] = $this->db->query("SELECT * FROM replays")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function restoreDatabase(): void
    {
        foreach ($this->backupData as $table => $rows) {
            foreach ($rows as $row) {
                $columns = implode(", ", array_keys($row));
                $values = implode(", ", array_map(fn($value) => $this->db->quote($value), $row));
                $this->db->exec("INSERT INTO $table ($columns) VALUES ($values)");
            }
        }
    }

    private function resetDatabase(): void
    {
        $this->db->exec("SET FOREIGN_KEY_CHECKS=0");
        $this->db->exec("TRUNCATE TABLE apuesta");
        $this->db->exec("TRUNCATE TABLE lucha");
        $this->db->exec("TRUNCATE TABLE luchador");
        $this->db->exec("TRUNCATE TABLE categoria");
        $this->db->exec("TRUNCATE TABLE usuario");
        $this->db->exec("TRUNCATE TABLE replays");
        $this->db->exec("SET FOREIGN_KEY_CHECKS=1");
    }

    private function initializeDatabase(): void
    {
        // Usuario
        $this->db->exec("INSERT INTO usuario (email, username, password, nombre, apellido, edad, sexo, cartera)
                         VALUES ('test@example.com', 'testuser', 'password', 'Nombre', 'Apellido', 30, 'masculino', 100)");

        // Luchador
        $this->db->exec("INSERT INTO luchador (email, peso, altura, victorias, derrotas, empates, puntos, grupoSang, ubicacion, lateralidad, buscando_pelea)
                         VALUES ('test@example.com', 70, 175, 10, 5, 2, 100, 'O+', 'Madrid', 'diestro', 1)");

        // Categoría
        $this->db->exec("INSERT INTO categoria (id, descripcion, nombre)
                         VALUES (1, 'mma', 'lucha libre')");

        // Lucha
        $this->db->exec("INSERT INTO lucha (id_lucha, id_luchador1, id_luchador2, id_categoria, id_ganador, num_rondas, fecha, hora_inicio, hora_final, estado, ubicacion)
                         VALUES (1, 'test@example.com', 'test@example.com', 1, 'test@example.com', 3, '2024-11-24', '12:00:00', '12:30:00', 'pendiente', 'Madrid')");

        // Apuesta
        $this->db->exec("INSERT INTO apuesta (id, email_usuario, id_lucha, luchador_apostado, w, l, d, total)
                         VALUES (1, 'test@example.com', 1, 'test@example.com', 100, 50, 20, 170)");
    }

    protected function tearDown(): void
    {
        $this->resetDatabase();
        $this->restoreDatabase(); // Restaura los datos originales
    }

    public function testObtenerValoresT_ValidId_ReturnsSum(): void
    {
        $result = $this->apuestas->obtenerValoresT(1);
        $this->assertSame([100, 50, 20], array_map('intval', $result));
    }

    public function testObtenerValoresT_InvalidId_ReturnsDefault(): void
    {
        $result = $this->apuestas->obtenerValoresT(null);
        $this->assertSame(["success" => false, "message" => "Invalid ID_lucha provided."], $result);
    }

    public function testApostadoPorUsuario_ValidInput_ReturnsValues(): void
    {
        $result = $this->apuestas->apostadoPorUsuario(1, 'test@example.com');
        $this->assertSame([100, 50, 20], $result !== false ? array_map('intval', $result) : []);
    }

    public function testAlgoritmoApuestas_ReturnsExpectedGanancias(): void
    {
        $w_tt = 100;
        $w_t = 100;
        $l_tt = 50;
        $l_t = 50;
        $d_tt = 20;
        $d_t = 20;
        $t_tt = $w_tt + $l_tt + $d_tt;
        $resultado = 'w';
        $comision = 0.9;

        $expectedGanancias = (100 * (1 + (1 - 100 / $t_tt)) * $comision)
                           + (50 * (1 + (1 - 50 / $t_tt)) * $comision)
                           + (20 * (1 + (1 - 20 / $t_tt)) * $comision);

        $result = $this->apuestas->algoritmo_apuestas($w_tt, $w_t, $l_tt, $l_t, $d_tt, $d_t, $t_tt, $resultado, $comision);
        $this->assertEquals($expectedGanancias, $result);
    }

    public function testActualizarUsuarioApuesta_ValidUser_UpdatesWallet(): void
    {
        $ganancias = 50;
        $result = $this->apuestas->actualizarUsuarioApuesta($ganancias, 'test@example.com');
        $this->assertSame(["success" => true, "message" => "Cartera actualizada exitosamente."], $result);

        $stmt = $this->db->query("SELECT cartera FROM usuario WHERE email = 'test@example.com'");
        $cartera = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertSame(150, (int)$cartera['cartera']);
    }

    public function testApuestaActualizarUsuario_CompletesSuccessfully(): void
    {
        $this->apuestas->apuesta_actualizar_usuario(1, 1, 'test@example.com', 'w', 0.9);

        $stmt = $this->db->query("SELECT cartera FROM usuario WHERE email = 'test@example.com'");
        $cartera = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertGreaterThan(100, (int)$cartera['cartera']);
    }
}
