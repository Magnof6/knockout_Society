<!-- Add this at the top of your existing register.php file -->
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
<div class="navbar">
    <nav>
        <a href="index.php">Home</a>
    </nav>
</div>

<!-- Your existing form and PHP code here -->

<div class="footer">
    <a href="https://kick.com/knockoutsociety">
        <img src="imagenes/kick3.jpg" alt="Footer Image">
    </a>
</div>
</body>
</html>

<?php
require_once 'db_connect.php';

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $is_fighter = isset($_POST['is_fighter']) ? 1 : 0;

    // Check if all fields are filled
    if (empty($email) || empty($username) || empty($_POST['password']) || empty($name) || empty($lastname) || empty($age) || empty($gender)) {
        $error_message = "All fields are required.";
    } else {
        // Check if user already exists
        $check_user = $conn->prepare("SELECT email FROM usuario WHERE email = ?");
        $check_user->bind_param("s", $email);
        $check_user->execute();
        $result = $check_user->get_result();

        if ($result->num_rows > 0) {
            $error_message = "User with this email already exists.";
        } else {
            // Insert into usuario table
            $insert_user = $conn->prepare("INSERT INTO usuario (email, username, password, nombre, apellido, edad, sexo) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert_user->bind_param("sssssss", $email, $username, $password, $name, $lastname, $age, $gender);

            if ($insert_user->execute()) {
                $success_message = "Registration successful!";

                // If registering as a fighter
                if ($is_fighter) {
                    $height = $_POST['height'];
                    $weight = $_POST['weight'];
                    $location = $_POST['location'];
                    $bloodtype = $_POST['bloodtype'];
                    $lateralidad = $_POST['lateralidad'];


                    // Insert into luchador table
                    $insert_fighter = $conn->prepare("INSERT INTO luchador (email, peso, altura, grupoSang, lateralidad) VALUES (?, ?, ?)");
                    $insert_fighter->bind_param("sdd", $email, $height, $weight, $location, $bloodtype, $lateralidad);

                    if (!$insert_fighter->execute()) {
                        $error_message = "Error registering fighter details: " . $insert_fighter->error;
                    }
                    $insert_fighter->close();
                }
            } else {
                $error_message = "Error: " . $insert_user->error;
            }
            $insert_user->close();
        }
        $check_user->close();
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
    Email: <input type="email" name="email" required><br>
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    Name: <input type="text" name="name" required><br>
    Last Name: <input type="text" name="lastname" required><br>
    Age: <input type="number" name="age" required><br>
    Gender:
    <select name="gender" required>
        <option value="Masculino">Male</option>
        <option value="Femenino">Female</option>
    </select><br>
    <input type="checkbox" id="is_fighter" name="is_fighter" onchange="toggleFighterFields()"> Register as a fighter?<br>
    <div id="fighterFields" style="display: none;">
        Height: <input type="number" step="0.01" name="height"><br>
        Weight: <input type="number" step="0.1" name="weight"><br>
        Location: <input type="text" name="location"><br>
        Blood Type:
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
            <option value="Derecha">Derecha</option>
            <option value="Izquierda">Izquierda</option>
    </div>
    <input type="submit" value="Register">
</form>
<p>Already have an account? <a href="login.php">Login here</a></p>
<p><a href="index.php">Back to Home</a></p>
</body>
</html>
<?php
require_once 'db_close.php';
?>