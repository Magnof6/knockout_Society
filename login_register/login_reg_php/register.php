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
        <a href="index.php" class = "header-button">Home</a>
    </nav>
</div>
<div class="footer">
        <!-- Boton que te lleva a Kick -->
        <a href="https://kick.com/knockoutsociety" target="_blank" id="Kick-floating-button">
            <img src="imagenes/kickkk.png" alt="imagen-kick-Icono Flotante">
        </a>
    </div>
    
    <script src="script.js"></script>
</div>
</body>
</html>



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
    </select><br>
    <input type="checkbox" id="is_fighter" name="is_fighter" onchange="toggleFighterFields()"> Registrar como luchador?<br>
    <div id="fighterFields" style="display: none;">
        Altura: <input type="number" step="0.01" name="height"><br>
        Peso: <input type="number" step="0.1" name="weight"><br>
        Ubicacion: <input type="text" name="location"><br>
        Grupo Sanguineo
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
    <a href="login.php" class = "cambio-registro">Login</a>
</form>
</body>
</html>

