<?php

/**
 * Esta clase encapsula toda la lógica de inserts a las diferentes tablas,
 * las que estan ahora mismo implementadas son las de usuario y luchador.
 * 
 * Si quereis insertar cualquier cosa hacedlo por aquí.
 * 
 */
// session_start();
require_once dirname(__DIR__) . '/db_connect.php';
require_once dirname(__DIR__) . '/function/apuestas.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
            return ["success" => false, "message" => "All fields are required."];              // Nota: si cartera==0 lo considera empty.
        }

        if (!is_numeric($age) || $age <= 0) {
            return ["success" => false, "message" => "La edad debe ser un número positivo."];
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
    private function checkWalletBalance($email_usuario) {
        try {
            $sql = "SELECT cartera FROM usuario WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $email_usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $row = $result->fetch_assoc()) {
                return ["success" => true, "cartera" => $row['cartera']];
            } else {
                error_log("ERROR: Wallet balance not found for user: $email_usuario");
                return ["success" => false, "message" => "Wallet balance not found."];
            }
        } catch (Exception $e) {
            error_log("ERROR: Exception while checking wallet balance. Message: " . $e->getMessage());
            return ["success" => false, "message" => "Error checking wallet balance."];
        }
    }

    public function crearApuesta($email_usuario, $id_lucha, $luchador_apostado, $w, $l, $d) {
        try {
            // Check user wallet balance
            $cartera_response = $this->checkWalletBalance($email_usuario);

            if (!$cartera_response['success']) {
                // Handle error (e.g., insufficient funds or query failure)
                return ["success" => false, "message" => $cartera_response['message']];
            }

            $cartera = $cartera_response['cartera'];

            // Compare the bet amount with the user's wallet balance
            if (($w + $l + $d) <= $cartera) {
                // Insert bet into the database
                $sql = "INSERT INTO apuesta (email_usuario, id_lucha, luchador_apostado, w, l, d) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('sisiid', $email_usuario, $id_lucha, $luchador_apostado, $w, $l, $d);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    error_log("DEBUG: Bet created successfully for user: $email_usuario, fight ID: $id_lucha");
                    return ["success" => true, "message" => "Bet created successfully."];
                } else {
                    error_log("ERROR: Failed to create bet. SQL Error: " . $this->conn->error);
                    return ["success" => false, "message" => "Failed to create bet."];
                }
            } else {
                error_log("ERROR: Insufficient funds for user: $email_usuario");
                return ["success" => false, "message" => "Insufficient funds."];
            }
        } catch (Exception $e) {
            error_log("ERROR: Exception in crearApuesta: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred while placing the bet."];
        }

    }
    

    
    

        /* $insert_apuesta = $this->conn->prepare(
            "INSERT INTO apuesta ($email_usuario , $id_lucha , $luchador_apostado, $w, $l, $d, NULL) VALUES (?, ?, ?, ?, ?, ?, ?, NULL)");
        $insert_apuesta->bind_param("sisiii" , $email_usuario , $id_lucha , $luchador_apostado, $w, $l, $d);

        if ($insert_apuesta->execute()) {
            return true;
        } else {
            return false;
        } */
    

    /**
     * Funcion para cambiar la contraseña
     * 
     * Cambia la contraseña de un usuario. Para poder cambiar la contraseña se deben cumplir:
     * 1. Contraseña actual es correcta
     * 2. Contraseña actual no es igual a nueva contraseña o confirmar contraseña
     * 3. Nueva contraseña es igual a confirmar contraseña
     * 
     * Si se produce un error en el cambio de contraseña también lo maneja. 
     * 
     * @param string $user_email email del usuario, proveniente por ejemplo de la sesión
     * @param string $current_password la contraseña actual sin cifrar.
     * @param string $new_password la contraseña nueva.
     * @param string $confirm_password la contraseña que se usa para confirmar que se ha escrito correctamente.
     * 
     * @return string Devuelve un texto de respuesta, ej. si ha sido exitoso o ha habido un error
     */
    public function changePassword($user_email, $current_password, $new_password, $confirm_password) {
        $sql = "SELECT * FROM usuario WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verifica la contraseña actual con la almacenada
        if (password_verify($current_password, $user['password'])) {
            if ($new_password == $current_password || $confirm_password == $current_password) {
                return ["success"=>false,"message"=>"New password can't be the same as old password."]; 
            } elseif ($new_password === $confirm_password) {
                // Hashea la nueva contraseña
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Actualiza la contraseña en la base de datos
                $sql = "UPDATE usuario SET password = ? WHERE email = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ss", $hashed_password, $user_email);
                
                if ($stmt->execute()) {
                    return ["success"=>true,"message"=>"Password changed successfully."];
                } else {
                    return ["success"=>false,"message"=>"Error changing password: " . $stmt->error];
                }
            } else {
                return ["success"=>false,"message"=>"New passwords do not match."];
            }
        } else {
            return ["success"=>false,"message"=>"Current password is incorrect."];
        }
    }
    /**
     * Changes the email address of a user.
     * 
     * Se encarga de que:
     * 1. Mail sea correcto
     * 2. Contraseña sea correcta
     * 3. Mails nuevo y confirm sean iguales
     * 4. No exista el mail nuevo de antemano
     *
     * @param string $email The current email address of the user.
     * @param string $password The current password of the user.
     * @param string $new_email The new email address to be set.
     * @param string $confirm_email The confirmation of the new email address.
     * @return array An associative array containing the success status and a message.
     */
    public function changeEmail($email, $password, $new_email, $confirm_email) {
        $sql = "SELECT * FROM usuario WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (empty($user)) return ["success" => false, "message" => "No user found with that email."];
        // Verifica la contraseña actual con la almacenada
        if (password_verify($password, $user['password'])) {
            if ($new_email === $confirm_email) {
                // Verifica si el nuevo email ya está en uso
                $sql = "SELECT * FROM usuario WHERE email = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("s", $new_email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    return ["success" => false, "message" => "New email is already in use."];
                } else {
                    // Actualiza el email en la base de datos
                    $sql = "UPDATE usuario SET email = ? WHERE email = ?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("ss", $new_email, $email);

                    if ($stmt->execute()) {
                        return ["success" => true, "message" => "Email changed successfully."];
                    } else {
                        return ["success" => false, "message" => "Error changing email: " . $stmt->error];
                    }
                }
            } else {
                return ["success" => false, "message" => "New emails do not match."];
            }
        } else {
            return ["success" => false, "message" => "Password is incorrect."];
        }
    }
}