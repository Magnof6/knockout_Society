<?php
    session_start();
    require_once 'db_connect.php';
    require 'function/inserts.php';
    
    
    $insert = new Inserts($conn);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $cartera = 100;
        $is_fighter = isset($_POST['is_fighter']) ? 1 : 0;

    
        $result = $insert->registerUser($email, $username, $password, $name, $lastname, $age, $gender, $cartera , $is_fighter);
    
        if ($result['success']) {
            $success_message = $result['message'];
        } else {
            $error_message = $result['message'];
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_fighter'])) {
        $email = $_POST['email']; // El email debe ser de un usuario ya registrado
        $height = $_POST['height'];
        $weight = $_POST['weight'];
        $location = $_POST['location'];
        $bloodtype = $_POST['bloodtype'];
        $lateralidad = $_POST['lateralidad'];
    
        $result = $insert->registerFighter($email, $height, $weight, $location, $bloodtype, $lateralidad);
    
        if ($result['success']) {
            $success_message = $result['message'];
        } else {
            $error_message = $result['message'];
        }
    }
    
    
?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <link rel="stylesheet" href="styles.css">
        <script>
            function toggleFighterFields() {
                var fighterFields = document.getElementById("fighterFields");
                fighterFields.style.display = document.getElementById("is_fighter").checked ? "block" : "none";
            }
        </script>
    </head>
    <body>
        <div class="header">
            <nav>
                <a href="Principio.php" class="header-button">Back</a>
            </nav>
        </div>

        <h2>Register</h2>
        <?php
        if (!empty($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        if (!empty($success_message)) {
            echo "<p style='color: green;'>$success_message</p>";
        }
        ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            Correo: <input type="email" name="email" required><br>
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            Nombre: <input type="text" name="name" required><br>
            Apellido: <input type="text" name="lastname" required><br>
            Edad: <input type="number" name="age" required><br>
            Sexo:
            <select name="gender" required>
                <option value="Masculino">Male</option>
                <option value="Femenino">Female</option>
            <input type="checkbox" id="is_fighter" name="is_fighter" onchange="toggleFighterFields()"> Registrar como luchador?<br>
            <div id="fighterFields" style="display: none;">
                Altura: <input type="number" step="0.01" name="height"><br>
                Peso: <input type="number" step="0.1" name="weight"><br>
                Ubicacion: <input type="text" name="location"><br>
                Grupo Sanguineo:
                <select name="bloodtype">
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select><br>
                Lateralidad:
                <select name="lateralidad">
                    <option value="diestro">Derecha</option>
                    <option value="zurdo">Izquierda</option>
                    <option value="ambi">Ambidiestro</option>
                </select><br>
            </div>
            <input type="submit" value="Register">
            <a href="login.php" class="cambio-registro">Login</a>
        </form>

        <?php include 'footer.php'; ?>
    </body>
</html>


