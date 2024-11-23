<?php
class Mostrar {
    public $db;

    public function __construct($dbConnection){
        $this->conn = $dbConnection;
    }

    public function mostrarRanking($criterio) {
  
        $criterios_validos = ['puntos', 'nombre', 'victorias', 'empates', 'derrotas'];
        if (!in_array($criterio, $criterios_validos)) {
            die("Criterio no válido");
        }

        $sql = "SELECT nombre, puntos, victorias, empates, derrotas 
            FROM luchador 
            ORDER BY $criterio DESC";

        $check_user = $this->conn->prepare($sql);
        if (!$check_user) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }
        $check_user->execute(); //Ejecutar
        $result = $check_user->get_result(); //Recoger

        if ($result->num_rows > 0) { //Mostrar
            while ($fila = $result->fetch_assoc()) {
                echo "Nombre: {$fila['nombre']}, Puntos: {$fila['puntos']}, Victorias: {$fila['victorias']}, Empates: {$fila['empates']}, Derrotas: {$fila['derrotas']}<br>";
            }
        } else {
            echo "No se encontraron resultados.";
        }
    }
}

// oBtenido del html, cuando el usuario selecciona una opción
if (isset($_GET['criterio'])) {
    $criterio = $_GET['criterio'];
    $mostrar = new Mostrar();
    $mostrar->mostrarRanking($criterio);
} else {
    echo "No se ha especificado un criterio.";
}
?>
