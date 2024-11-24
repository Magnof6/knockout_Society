<?php

use phpDocumentor\Reflection\Types\Null_;
use PhpParser\Node\Stmt;
use PHPUnit\TextUI\TestSuiteMapper;

/**
* Todavía en progreso, debo comprobar cual es la clave de la apuesta
*/

require_once dirname(__DIR__) . '/db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Apuestas{

    public $conn;

    public function __construct($dbConnection){
        $this->conn = $dbConnection;
    }

    public function obtenerValoresT($id_lucha){
        try{

            if (empty($id_lucha)) {
                return ["success" => false, "message" => "Invalid ID_lucha provided."];
            }

            // Para el total de dinero apostado a W => w_t
            $sql = "SELECT SUM(w) AS w_tt , SUM(l) AS l_tt, SUM(d) AS d_tt FROM apuesta WHERE id_lucha = :id_lucha";
            $stmt = $this->conn->prepare($sql);
            $stmt -> bindParam(':id_lucha' , $id_lucha, PDO::PARAM_INT);
            $stmt->execute();
            $t_st = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($t_st ['w_tt'] !== null && $t_st ['l_tt'] !== null && $t_st['d_tt'] !== null) {

                return [$t_st ['w_tt'] , $t_st  ['l_tt'] , $t_st  ['d_tt']]; 

            } else {

                return [0 , 0 , 0]; 
            }

        }catch(Exception){
            echo "Error";
            return false;
        }
    }

    public function apostadoPorUsuario($id_apuesta , $email){
        try{

            if (empty($id_apuesta) || empty($email)) {
                return ["success" => false, "message" => "Invalid ID_apuesta o email_usuario provided."];
            }

            // Para extraer lo que apostó el usuario
            $sql = "SELECT w , l , d FROM apuesta WHERE id_apuesta = :id_apuesta AND email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt -> bindParam(':id_apuesta' , $id_apuesta, PDO::PARAM_INT);
            $stmt -> bindParam(':email' , $email, PDO::PARAM_STR);
            $stmt->execute();
            $apostado = $stmt->fetch(PDO::FETCH_ASSOC);

            if($apostado && isset($apostado['w']) && isset($apostado['l']) && isset($apostado['d'])){
                return [$apostado['w'], $apostado['l'], $apostado['d']];
            }else{
                return [0 , 0 , 0];
            }
    }catch(Exception){
        echo "Error";
        return false;
    }
}
    

    public function algoritmo_apuestas($w_tt , $w_t, $l_tt , $l_t , $d_tt, $d_t, $t_tt, $resultado, $comision){
        $prob_w = 1 + (1 - $w_tt / $t_tt);
        $prob_l = 1 + (1 - $l_tt / $t_tt);
        $prob_d = 1 + (1 - $d_tt / $t_tt);

        $sumador_w = $w_t * $prob_w * $comision;
        $sumador_l = $l_t * $prob_l * $comision;
        $sumador_d = $d_t * $prob_d * $comision;
        $ganancias = $sumador_w + $sumador_l + $sumador_d;

        return $ganancias;
    }

    public function actualizarUsuarioApuesta($ganancias , $email){
        try{

            if (($ganancias) != Null || empty($email)){
                return ["success" => false, "message" => "Invalid ganancias provided."];
            }
        
            $sql = "SELECT cartera FROM usuario WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt -> bindParam(':email' , $email, PDO::PARAM_STR);
            $stmt->execute();
            $cartera = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cartera) {
                return ["success" => false, "message" => "Usuario no encontrado."];
            }

            $cartera = $cartera['cartera'];
            $nueva_cartera = $cartera + $ganancias;

            $sql = "UPDATE usuario SET cartera = :nueva_cartera WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt -> bindParam(':email' , $email, PDO::PARAM_STR);
            $stmt -> bindParam(':nueva_cartera' , $nueva_cartera, PDO::PARAM_INT);
            //$stmt->execute();

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

    public function apuesta_actualizar_usuario($id_lucha, $id_apuesta, $email_usuario , $resultado, $comision = 0.9){
        
        $totales = $this->obtenerValoresT($id_lucha);
        if($totales){
            $w_tt = $totales[0];
            $l_tt = $totales[1];
            $d_tt = $totales[2];
            $t_tt = $w_tt + $l_tt + $d_tt;
        }
        
        $totales_apostados = $this->apostadoPorUsuario($id_apuesta , $email_usuario);
        if($totales_apostados){
            $w_t = $totales_apostados[0];
            $l_t = $totales_apostados[1];
            $d_t = $totales_apostados[2];
        }

        $ganancias = $this->algoritmo_apuestas($w_tt , $w_t, $l_tt , $l_t , $d_tt, $d_t, $t_tt, $resultado, $comision);
        
        if($ganancias != Null){
            $this->actualizarUsuarioApuesta($ganancias, $email_usuario);
        }

    }
}
