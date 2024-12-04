<?php
require_once dirname(__DIR__) . '/db_connect.php';
require 'apuestas.php'; 
require 'elo.php';
session_start();

class AfterFight{
   private $conn;
   public function __construct($dbConnection){
       $this->conn = $dbConnection;
   }
   public function afterFight($id_lucha , $email_Luchador_1 , $email_Luchador_2, $resultado){

    //Actualizar los puntos de los luchadores tras el combate
    $elo = new Elo($this->conn);
    $this->$elo->EloLuchadoresAfter($id_lucha , $email_Luchador_1 , $email_Luchador_2, $resultado);

    //Actualiza las apuestas realizadas para todos los usuarios
    $apuesta = new Apuestas($this->conn);
    $this->$elo->ActualizadorGeneralApuestas($id_lucha , $resultado);
    
   }
}