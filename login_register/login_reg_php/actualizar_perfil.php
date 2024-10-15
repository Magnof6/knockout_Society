<?php
session_start();
$conn = new mysqli($servername, $username, $password, $dbname);

// Obtener datos del formulario
$nombre_usuario = $_POST['nombre_usuario'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT); // Asegúrate de usar hashing para contraseñas
$user_id = $_SESSION['user_id'];

// Actualizar en la base de datos
$sql = "UPDATE usuario SET nombre_usuario='$nombre_usuario', contrasena='$contrasena' WHERE id='$user_id'";
if ($conn->query($sql) === TRUE) {
    echo "Perfil actualizado con éxito.";
} else {
    echo "Error al actualizar el perfil: " . $conn->error;
}

$conn->close();
?>
