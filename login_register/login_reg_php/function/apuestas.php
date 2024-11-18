<?php

/**
* Todavía en progreso, debo comprobar cual es la clave de la apuesta
*/

class Apuestas{
    public function algoritmo_apuestas($nombre_apuesta , $total_victoria , $total_empate, $total_derrota, $resultado){
        $total = $total_victoria + $total_derrota + $total_empate;
        
        // Si el resultado de la apuesta ha sido victoria
        if($resultado == 1){
            return [$nombre_apuesta , ($total_victoria / $total)];

        // Si el resultado de la apuesta ha sido derrota
        }elseif($resultado == 0){
            return [$nombre_apuesta , ($total_derrota / $total)];

        }else{
        // Si el resultado de la apuesta ha sido empate
            return [$nombre_apuesta , ($total_empate / $total)];
        }
    }



}