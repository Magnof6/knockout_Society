<?php
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../installs/vendor/autoload.php'; // Solo este require es necesario
require __DIR__ . '/../login_reg_php/login.php';

class LoginTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli('localhost', 'usuario_db', 'contraseña_db', 'nombre_db');
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        session_start();
    }

    public function testLoginSuccessful()
    {
        $email = 'test@example.com'; // Asegúrate de que este usuario exista
        $password = 'contraseña_valid'; // Asegúrate de que esta contraseña coincida

        $_POST['email'] = $email;
        $_POST['password'] = $password;

        ob_start();
        include 'login_reg_php/login.php';
        ob_end_clean();

        $this->assertEquals($_SESSION['user_email'], $email);
        $this->assertEquals($_SESSION['username'], 'nombre_usuario'); // Asegúrate de que esto sea dinámico
        $this->assertStringContainsString('Location: index.php', headers_list()[0]);
    }

    protected function tearDown(): void
    {
        session_destroy();
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function testLoginFailed()
    {
        $email = 'invalido@example.com';
        $password = 'contraseña_incorrecta';

        $_POST['email'] = $email;
        $_POST['password'] = $password;

        ob_start();
        include 'login_reg_php/login.php';
        ob_end_clean();

        $this->assertArrayNotHasKey('user_email', $_SESSION, 'La sesión no debería contener user_email.');
        $this->assertStringContainsString('Location: login.php', headers_list()[0]);
    }
}
