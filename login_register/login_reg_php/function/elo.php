<?php
/**
 * Ejemplo de como implementarlo:
 * 
 * require("function/elo.php");
 *
 *   $elo = new Elo();
 *
 *   // Parámetros: Elo actual de A, Elo actual de B, resultado de A, resultado de B, K-factor
 *   list($nuevo_elo_a, $nuevo_elo_b) = $elo->calculaNuevoElo(1200, 1300, 1, 0, 35);
 *   
 *   // Ahora podríamos actulizar las puntuaciones de cada peleador.
 * 
 * echo "Nuevo Elo de A: " . round($nuevo_elo_a, 2) . "\n";
 * echo "Nuevo Elo de B: " . round($nuevo_elo_b, 2) . "\n";
 * 
 */
class Elo{
    public function calculaNuevoElo($R_a , $R_b, $S_a, $S_b , $k = 40){
        $E_a = 1 / (1 + 10 ** (($R_b - $R_a)/400));
        $E_b = 1 - $E_a;

        $nuevo_elo_a = $R_a + $k * ($S_a - $E_a);
        $nuevo_elo_b = $R_b + $k * ($S_b - $E_b);

        return [$nuevo_elo_a , $nuevo_elo_b];
    }
}