<?php

/**
 * Esta clase encapsula toda la lógica de inserts a las diferentes tablas,
 * las que estan ahora mismo implementadas son las de usuario y luchador.
 * 
 * Si quereis insertar cualquier cosa hacedlo por aquí.
 * 
 */



Class Inserts{
    public $conn;

    public function __construct($dbConnection){
        $this->conn = $dbConnection;
    }


    // Registrar Usuario
    public function registerUser($email, $username, $password, $name, $lastname, $age, $gender, $cartera , $is_fighter){
        $error_message = "";
        $success_message = "";

        // Validación de campos obligatorios
        if (empty($email) || empty($username) || empty($password) || empty($name) || empty($lastname) || empty($cartera)|| empty($age) || empty($gender)) {
            return ["success" => false, "message" => "All fields are required."];
        }

        // Verificar si el usuario ya existe
        $check_user = $this->conn->prepare("SELECT email FROM usuario WHERE email = ?");
        $check_user->bind_param("s", $email);
        $check_user->execute();
        $result = $check_user->get_result();

        if ($result->num_rows > 0) {
            return ["success" => false, "message" => "User with this email already exists."];
        }

        // Insertar en la tabla usuario
        $insert_user = $this->conn->prepare(
            "INSERT INTO usuario (email, username, password, nombre, apellido, edad, sexo, cartera) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $insert_user->bind_param("sssssisd", $email, $username, $password_hashed, $name, $lastname, $age, $gender, $cartera);

        if ($insert_user->execute()) {
            $success_message = "User registration successful!";
            // Si llama a $is_fighter
            if ($is_fighter) {
                return $this->registerFighter($email, $_POST['height'], $_POST['weight'], $_POST['location'], $_POST['bloodtype'], $_POST['lateralidad']);
            }
        } else {
            $error_message = "Error registering user: " . $insert_user->error;
        }

        if ($is_fighter) {
            return $this->registerFighter($email, $_POST['height'], $_POST['weight'], $_POST['location'], $_POST['bloodtype'], $_POST['lateralidad']);
        }

        $insert_user->close();
        return ["success" => !empty($success_message), 
                "message" => $success_message ?: $error_message];
    }

    // Registrar Luchador
    public function registerFighter($email, $height, $weight, $location, $bloodtype, $lateralidad){
        if (empty($height) || empty($weight) || empty($location) || empty($bloodtype) || empty($lateralidad)) {
            return ["success" => false, "message" => "All fighter fields are required."];
        }

        // Insertar en la tabla luchador
        $insert_fighter = $this->conn->prepare(
            "INSERT INTO luchador (email, peso, altura, grupoSang, ubicacion, lateralidad) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $insert_fighter->bind_param("siisss", $email, $weight, $height, $bloodtype, $location, $lateralidad);

        if ($insert_fighter->execute()) {
            $success_message = "Fighter registration successful!";
        } else {
            $error_message = "Error registering fighter: " . $insert_fighter->error;
        }

        $insert_fighter->close();
        return ["success" => isset($success_message), 
                "message" => $success_message ?? $error_message];
    }

    // Crear una apuesta
    public function crearApuesta($id_apuesta, $email_usuario , $id_lucha , $luchador_apostado, $w, $l, $d, $total = null){
        if (empty($id_apuesta) || empty($email_usuario) || empty($id_lucha) || empty($luchador_apostado) || empty($w) || empty($l) || empty($d)) {
                throw new Exception("Missing required fields.");
            }

        $insert_apuesta = $this->conn->prepare(
            "INSERT INTO apuesta ($id_apuesta, $email_usuario , $id_lucha , $luchador_apostado, $w, $l, $d, $total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_apuesta->bind_param("isisiiii" , $id_apuesta, $email_usuario , $id_lucha , $luchador_apostado, $w, $l, $d, $total);

        if ($insert_apuesta->execute()) {
            return true;
        } else {
            return false;
        }
    }
}