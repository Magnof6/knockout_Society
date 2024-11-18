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
class Elo{
    public function calculaNuevoElo($R_a , $R_b, $S_a , $k = 40){
        $E_a = 1 / (1 + 10 ** (($R_b - $R_a)/400));
        $E_b = 1 - $E_a;
        $S_b = 1 - $S_a;

        $nuevo_elo_a = $R_a + $k * ($S_a - $E_a);
        $nuevo_elo_b = $R_b + $k * ($S_b - $E_b);

        return [$nuevo_elo_a , $nuevo_elo_b];
    }
}