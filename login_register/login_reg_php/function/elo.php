<?php
/**
 * Ejemplo de como implementarlo:
 * 
 * require("function/elo.php");
 *
 *   resultado de A es 1(Victoria) , 1/2(Empate) y 0(Derrota) de ahi sacamos el de B
 *   
 *   // Parámetros: 
 *      Elo actual de A = $R_a,
 *      Elo actual de B = $R_b, 
 *      resultado de A = $S_a, 
 *      K-factor = $k / default $k = 40
 *   
 * Ejemplo de como extraer los resultados e implementarlo:
 *
 * require("function/elo.php");
 * 
 *      $elo = new Elo();
 *      list($nuevo_elo_a, $nuevo_elo_b) = $elo->calculaNuevoElo(1200, 1300, 1, 0, 35);
 *   
 *   // Ahora podríamos actulizar las puntuaciones de cada peleador, en este caso
 *   // solo muestro un ejemplo de como visualizarlos, pero la idea es actualizar 
 *   // los perfiles de los dos luchadores.
 * 
 *      echo "Nuevo Elo de A: " . round($nuevo_elo_a, 2) . "\n";
 *      echo "Nuevo Elo de B: " . round($nuevo_elo_b, 2) . "\n";
 * 
 */

 require_once dirname(__DIR__) . '/db_connect.php';
 if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class Elo{
    public $conn;

    public function __construct($dbConnection){
        $this->conn = $dbConnection;
    }
    public function calculaNuevoElo($R_a , $R_b, $S_a , $k = 40){
        $E_a = 1 / (1 + 10 ** (($R_b - $R_a)/400));
        $E_b = 1 - $E_a;
        $S_b = 1 - $S_a;

        $nuevo_elo_a = $R_a + $k * ($S_a - $E_a);
        $nuevo_elo_b = $R_b + $k * ($S_b - $E_b);

        return [$nuevo_elo_a , $nuevo_elo_b];
    }
    public function extraerEloLuchadoresBefore($email_Luchador_1 , $email_Luchador_2){
        $sql = "SELECT puntos FROM usuario WHERE email = :email_Luchador_1";
        $stmt = $this->conn->prepare($sql);
        $stmt -> bindParam(':email_Luchador_1' , $email_Luchador_1, PDO::PARAM_STR);
        $stmt->execute();
        $puntosL_1 = $stmt->fetch(PDO::FETCH_ASSOC);
        $puntosL_1 = $puntosL_1['puntos'];

        $sql = "SELECT puntos FROM usuario WHERE email = :email_Luchador_2";
        $stmt = $this->conn->prepare($sql);
        $stmt -> bindParam(':email_Luchador_2' , $email_Luchador_2, PDO::PARAM_STR);
        $stmt->execute();
        $puntosL_2 = $stmt->fetch(PDO::FETCH_ASSOC);
        $puntosL_2 = $puntosL_2['puntos'];

        $puntos = [$puntosL_1 , $puntosL_2];

        return $puntos;

    }

    public function actualizarLuchadores($email_Luchador_1 , $email_Luchador_2, $resultado, $puntosLn_1 , $puntosLn_2){
        try{
            $sql = "SELECT puntos FROM luchador WHERE email = :email_Luchador_1";
                $stmt = $this->conn->prepare($sql);
                $stmt -> bindParam(':email_Luchador_1' , $email_Luchador_1, PDO::PARAM_STR);
                $stmt->execute();

                $sql = "SELECT puntos FROM luchador WHERE email = :email_Luchador_2";
                $stmt = $this->conn->prepare($sql);
                $stmt -> bindParam(':email_Luchador_2' , $email_Luchador_2, PDO::PARAM_STR);
                $stmt->execute();

            if($resultado == 1){
                //Luchador 1
                $sql = "UPDATE luchador SET puntos = :puntosLn_1 , victorias = victorias + 1 WHERE email = :email_Luchador_1";
                $stmt = $this->conn->prepare($sql);
                $stmt -> bindParam(':email_Luchador_1' , $email_Luchador_1, PDO::PARAM_STR);
                $stmt -> bindParam(':puntosLn_1' , $puntosLn_1, PDO::PARAM_INT);
                $stmt->execute();
                
                //Luchador 2
                $sql = "UPDATE luchador SET puntos = :puntosLn_2 , derrotas = derrotas + 1 WHERE email = :email_Luchador_2";
                $stmt = $this->conn->prepare($sql);
                $stmt -> bindParam(':email_Luchador_2' , $email_Luchador_2, PDO::PARAM_STR);
                $stmt -> bindParam(':puntosLn_2' , $puntosLn_2, PDO::PARAM_INT);
                $stmt->execute();
            }elseif($resultado == 0){
                //Luchador 1
                $sql = "UPDATE luchador SET puntos = :puntosLn_1 , derrotas = derrotas + 1 WHERE email = :email_Luchador_1";
                $stmt = $this->conn->prepare($sql);
                $stmt -> bindParam(':email_Luchador_1' , $email_Luchador_1, PDO::PARAM_STR);
                $stmt -> bindParam(':puntosLn_1' , $puntosLn_1, PDO::PARAM_INT);
                $stmt->execute();

                //Luchador 2
                $sql = "UPDATE luchador SET puntos = :puntosLn_2 , victorias = victorias + 1 WHERE email = :email_Luchador_2";
                $stmt = $this->conn->prepare($sql);
                $stmt -> bindParam(':email_Luchador_2' , $email_Luchador_2, PDO::PARAM_STR);
                $stmt -> bindParam(':puntosLn_2' , $puntosLn_2, PDO::PARAM_INT);
                $stmt->execute();

            }else{
                //Luchador 1
                $sql = "UPDATE luchador SET puntos = :puntosLn_1 , empates = empates + 1 WHERE email = :email_Luchador_1";
                $stmt = $this->conn->prepare($sql);
                $stmt -> bindParam(':email_Luchador_1' , $email_Luchador_1, PDO::PARAM_STR);
                $stmt -> bindParam(':puntosLn_1' , $puntosLn_1, PDO::PARAM_INT);
                $stmt->execute();

                //Luchador 2
                $sql = "UPDATE luchador SET puntos = :puntosLn_2 , empates = empates + 1 WHERE email = :email_Luchador_2";
                $stmt = $this->conn->prepare($sql);
                $stmt -> bindParam(':email_Luchador_2' , $email_Luchador_2, PDO::PARAM_STR);
                $stmt -> bindParam(':puntosLn_2' , $puntosLn_2, PDO::PARAM_INT);
                $stmt->execute();
            }

            if ($stmt->execute()) {
                return ["success" => true, "message" => "Cartera actualizada exitosamente."];
            } else {
                return ["success" => false, "message" => "No se pudo actualizar la cartera."];
            }   
                
        }catch(Exception){
            echo "Error";
            return false;
        }
    }

    public function EloLuchadoresAfter($id_lucha , $email_Luchador_1 , $email_Luchador_2, $resultado){
        
        $puntos = $this -> extraerEloLuchadoresBefore($email_Luchador_1 , $email_Luchador_2);
        if($puntos){
            $R_a = $puntos[0];
            $R_b = $puntos[1];
            $puntos_f = $this->calculaNuevoElo($R_a , $R_b, $resultado, $k = 40);
        }
        if($puntos_f){
            $puntosLn_1 = $puntos_f[0];
            $puntosLn_2 = $puntos_f[1];

            $this->actualizarLuchadores($email_Luchador_1 , $email_Luchador_2, $resultado, $puntosLn_1 , $puntosLn_2);
        }


        



    }
}
