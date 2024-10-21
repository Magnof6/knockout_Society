<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        // Conectar a la base de datos para realizar pruebas
        $this->conn = new mysqli('localhost', 'usuario_db', 'contraseña_db', 'nombre_db');
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Iniciar la sesión
        session_start();
    }

    public function testLoginSuccessful()
    {
        // Datos de prueba: reemplaza con credenciales válidas en tu base de datos
        $email = 'test@example.com'; // Asegúrate de que este usuario exista en tu base de datos
        $password = 'contraseña_valid'; // Asegúrate de que esta contraseña coincida

        // Realizar una solicitud POST simulada
        $_POST['email'] = $email;
        $_POST['password'] = $password;

        // Incluir el archivo de login que contiene la lógica de inicio de sesión
        ob_start();
        include 'login_reg_php/login.php'; // Asegúrate de usar la ruta correcta
        ob_end_clean();

        // Verificar que la sesión se ha establecido correctamente
        $this->assertEquals($_SESSION['user_email'], $email);
        $this->assertEquals($_SESSION['username'], 'nombre_usuario'); // Cambia a lo que deberías obtener del DB

        // Verificar que se redirige a la página correcta
        $this->assertStringContainsString('Location: index.php', headers_list()[0]); // Esto verifica la redirección
    }

    protected function tearDown(): void
    {
        // Cerrar la conexión a la base de datos
        $this->conn->close();
    }
}
