<?php

Class Inserts{
    public $conn;

    public function __construct($dbConnection){
        $this->conn = $dbConnection;
    }


    // Registrar Usuario
    public function registerUser($email, $username, $password, $name, $lastname, $age, $gender, $is_fighter){
        $error_message = "";
        $success_message = "";

        // Validación de campos obligatorios
        if (empty($email) || empty($username) || empty($password) || empty($name) || empty($lastname) || empty($age) || empty($gender)) {
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
            "INSERT INTO usuario (email, username, password, nombre, apellido, edad, sexo) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $insert_user->bind_param("sssssis", $email, $username, $password_hashed, $name, $lastname, $age, $gender);

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
}