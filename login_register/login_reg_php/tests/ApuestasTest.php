<?php

use PHPUnit\Framework\TestCase;

class ApuestasTest extends TestCase
{
    private $db;
    private $apuestas;

    protected function setUp(): void
    {
        // Conexión real a la base de datos
        $dsn = 'mysql:host=localhost;dbname=test_db';
        $username = 'root';
        $password = 'password';
        $this->db = new PDO($dsn, $username, $password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->apuestas = new Apuestas($this->db);

        // Inserta datos para pruebas en la tabla `apuesta`
        $this->db->exec("INSERT INTO apuesta (id, email_usuario, id_lucha, luchador_apostado, w, l, d, total) 
                         VALUES (1, 'test@example.com', 1, 'Luchador1', 100, 50, 20, 170)");
        $this->db->exec("INSERT INTO usuario (email, cartera) VALUES ('test@example.com', 100)");
    }

    protected function tearDown(): void
    {
        // Limpia los datos después de las pruebas
        $this->db->exec("DELETE FROM apuesta");
        $this->db->exec("DELETE FROM usuario");
    }

    public function testObtenerValoresT_ValidId_ReturnsSum()
    {
        $result = $this->apuestas->obtenerValoresT(1);
        $this->assertEquals([100, 50, 20], $result);
    }

    public function testObtenerValoresT_InvalidId_ReturnsDefault()
    {
        $result = $this->apuestas->obtenerValoresT(null);
        $this->assertEquals(["success" => false, "message" => "Invalid ID_lucha provided."], $result);
    }

    public function testApostadoPorUsuario_ValidInput_ReturnsValues()
    {
        $result = $this->apuestas->apostadoPorUsuario(1, 'test@example.com');
        $this->assertEquals([100, 50, 20], $result);
    }

    public function testAlgoritmoApuestas_ReturnsExpectedGanancias()
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

    public function testActualizarUsuarioApuesta_ValidUser_UpdatesWallet()
    {
        $ganancias = 50;
        $result = $this->apuestas->actualizarUsuarioApuesta($ganancias, 'test@example.com');
        $this->assertEquals(["success" => true, "message" => "Cartera actualizada exitosamente."], $result);

        // Verificar que la cartera se haya actualizado correctamente
        $stmt = $this->db->query("SELECT cartera FROM usuario WHERE email = 'test@example.com'");
        $cartera = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(150, $cartera['cartera']);
    }

    public function testApuestaActualizarUsuario_CompletesSuccessfully()
    {
        $this->apuestas->apuesta_actualizar_usuario(1, 1, 'test@example.com', 'w', 0.9);

        // Verificar que la cartera del usuario fue actualizada correctamente
        $stmt = $this->db->query("SELECT cartera FROM usuario WHERE email = 'test@example.com'");
        $cartera = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertGreaterThan(100, $cartera['cartera']); // La cartera debe haber aumentado
    }
}
