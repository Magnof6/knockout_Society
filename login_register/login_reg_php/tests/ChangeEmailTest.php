<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../function/inserts.php';
class ChangeEmailTest extends TestCase
{
    protected static $db;

    public static function setUpBeforeClass(): void
    {
        $host = 'serverkn.ddns.net';
        $username = 'root';
        $password = 'PeleaDown$666';
        $dbname = 'knockout_dev';

        self::$db = new mysqli($host, $username, $password, $dbname);
        self::$db->query("SET FOREIGN_KEY_CHECKS = 0");
        self::$db->query("TRUNCATE TABLE luchador");
        self::$db->query("TRUNCATE TABLE usuario");
        self::$db->query("SET FOREIGN_KEY_CHECKS = 1");

    }

    public static function tearDownAfterClass():void
    {
        self::$db = null;
    }
    // Que pueda cambiar el mail y devuelva el mensaje correcto
    public function testChangeEmail1()
    {
        $inserts = new Inserts(self::$db);

        $result = $inserts->registerUser('correoEjemplo1@gmail.com', 'usuarioEjemplo1', 'password', 'Ejemplo1', 'Ejemplo1', 20, 'masculino', 1.0, 0);
        $this->assertTrue($result["success"], 'User registration failed');

        $email_before = self::$db->query("SELECT email FROM usuario WHERE email = 'correoEjemplo1@gmail.com'")->fetch_assoc()['email'];
        $this->assertEquals('correoEjemplo1@gmail.com', $email_before);
        // como inserts no tiene tests un poco de testing de registerUser por desconfianza

        $result = $inserts->changeEmail('correoEjemplo1@gmail.com', 'password', 'nuevoCorreo1@gmail.com', 'nuevoCorreo1@gmail.com');

        $email_after = self::$db->query("SELECT email FROM usuario WHERE email = 'nuevoCorreo1@gmail.com'")->fetch_assoc()['email'];

        $this->assertEquals('nuevoCorreo1@gmail.com', $email_after);
        $this->assertEquals($result["message"],"Email changed successfully.");
    }

    public function testChangeEmail2()
    {
        $inserts = new Inserts(self::$db);

        $result = $inserts->registerUser('correoEjemplo2@gmail.com', 'usuarioEjemplo2', 'password', 'Ejemplo2', 'Ejemplo2', 20, 'masculino', 1.0, 0);
        $this->assertTrue($result["success"], 'User registration failed');

        $email_before = self::$db->query("SELECT email FROM usuario WHERE email = 'correoEjemplo2@gmail.com'")->fetch_assoc()['email'];
        $this->assertEquals('correoEjemplo2@gmail.com', $email_before);
        // como inserts no tiene tests un poco de testing de registerUser por desconfianza

        $result = $inserts->changeEmail('correoEjemplo2@gmail.com', 'password', 'nuevoCorreo2@gmail.com', 'nuevoCorreo2@gmail.com');

        $email_after = self::$db->query("SELECT email FROM usuario WHERE email = 'nuevoCorreo2@gmail.com'")->fetch_assoc()['email'];

        $this->assertEquals('nuevoCorreo2@gmail.com', $email_after);
        $this->assertEquals($result["message"],"Email changed successfully.");
    }
    // Que falle si mail nuevo y de confirmacion no son iguales
    public function testConfirmEmail()
    {
        $inserts = new Inserts(self::$db);

        $result = $inserts->registerUser('correoEjemplo3@gmail.com', 'usuarioEjemplo3', 'password', 'Ejemplo3', 'Ejemplo3', 20, 'masculino', 1.0, 0);
        $this->assertTrue($result["success"], 'User registration failed');

        $result = $inserts->changeEmail('correoEjemplo3@gmail.com', 'password', 'nuevoCorreo3@gmail.com', 'nuevoCorreoDistinto@gmail.com');

        $this->assertFalse($result["success"]);
        $this->assertEquals($result["message"],"New emails do not match.");
    }

    public function testIncorrectPassword(){
        $inserts = new Inserts(self::$db);

        $result = $inserts->registerUser('correoEjemplo4@gmail.com', 'usuarioEjemplo4', 'password', 'Ejemplo4', 'Ejemplo4', 20, 'masculino', 1.0, 0);
        $this->assertTrue($result["success"], 'User registration failed');

        $result = $inserts->changeEmail('correoEjemplo4@gmail.com', 'wrongPassword', 'nuevoCorreo4@gmail.com', 'nuevoCorreo4@gmail.com');

        $this->assertFalse($result["success"]);
        $this->assertEquals($result["message"],"Password is incorrect.");
    }

    // Si el mail no existe deberia quejarse 
    public function testValidEmail(){
        $inserts = new Inserts(self::$db);

        $result = $inserts->registerUser('correoEjemplo5@gmail.com', 'usuarioEjemplo5', 'password', 'Ejemplo5', 'Ejemplo5', 20, 'masculino', 1.0, 0);
        $this->assertTrue($result["success"], 'User registration failed');

        $result = $inserts->changeEmail('esteCorreoDefinitivamenteNoExiste@gmail.com', 'password', 'nuevoCorreo5@gmail.com', 'nuevoCorreo5@gmail.com');

        $this->assertFalse($result["success"]);
        $this->assertEquals($result["message"],"No user found with that email.");
    }

    // Que no pueda sobreescribir a otro usuario sin querer
    public function testNewEmailDoesntExist(){
        $inserts = new Inserts(self::$db);

        $result = $inserts->registerUser('correoEjemplo6@gmail.com', 'usuarioEjemplo6', 'password', 'Ejemplo6', 'Ejemplo6', 20, 'masculino', 1.0, 0);
        $resul2 = $inserts->registerUser('correoExistente@gmail.com', 'correoExistente6', 'password', 'existente6', 'existente6', 20, 'masculino', 1.0, 0);
        $this->assertTrue($result["success"], 'User registration failed');
        $this->assertTrue($resul2["success"], 'User registration failed');

        $result = $inserts->changeEmail('correoEjemplo6@gmail.com', 'password', 'correoExistente@gmail.com', 'correoExistente@gmail.com');

        $this->assertFalse($result["success"]);
        $this->assertEquals($result["message"],"New email is already in use.");
    }

    // Cuando el correo se cambie, que se cambie también en la tabla luchador
    public function testLuchadorCascade() {
        $inserts = new Inserts(self::$db);

        $result = $inserts->registerUser('correoEjemplo7@gmail.com', 'usuarioEjemplo7', 'password', 'Ejemplo7', 'Ejemplo7', 20, 'masculino', 1.0, 0);
        $resultluchador = $inserts->registerFighter('correoEjemplo7@gmail.com',175,55,"Madrid","A+","diestro");
        $this->assertTrue($result["success"], 'User registration failed');
        $this->assertTrue($resultluchador["success"], 'User registration failed');

        $result = $inserts->changeEmail('correoEjemplo7@gmail.com', 'password', 'nuevoCorreo7@gmail.com', 'nuevoCorreo7@gmail.com');
        
        $email_luchador = self::$db->query("SELECT email FROM luchador WHERE email = 'nuevoCorreo7@gmail.com'")->fetch_assoc()['email'];

        $this->assertEquals('nuevoCorreo7@gmail.com', $email_luchador);
        $this->assertEquals($result["message"],"Email changed successfully.");
    }
}
?>