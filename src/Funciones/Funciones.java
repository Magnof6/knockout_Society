package Funciones;

import conexion_bbdd.MySQLConnection;

/**
 *
 * @author Alejandro Fernandez Munoz <alejandro.fernandezmunoz@usp.ceu.es>
 */
public class Funciones {

    public static void CrearUsuarioNuevo(String usuario, String password, String correo, String nombre, String apellidos, String edad, String sexo) { //En sexo se introducira M o F
        /**
         * El ususario meterá su nombre de usuario, contraseña y correo
         * iniciaremos sesion en la bbdd con un admin que solo cree usuarios
         * acto seguido cerraremos sesion y pasaremos a la funcion inicio sesion
         */
        MySQLConnection.establecerConexion("creador", "PeleaDown666$");
        String Consulta = "CREATE USER " + usuario + " IDENTIFIED BY " + password;
        MySQLConnection.ejecutarConsulta(Consulta);
        String insertUsuario = "INSERT INTO usuarios (correo, nombre, sexo, apellidos,edad,user) VALUES ('" + correo + "', '" + nombre + "', '" + sexo + "', '" + apellidos + "', '" + edad + "', '" + usuario + ");";
        MySQLConnection.ejecutarConsulta(insertUsuario);
        String permiso = "GRANT SELECT on "
                + "El nombre de la base de datos"
                + " TO " + usuario;
        MySQLConnection.cerrarConexion();
        MySQLConnection.establecerConexion(usuario, password);
    }

    public static void InicioUsuario(String usuario, String password) {
        try {
            // Excepción si no se introduce usuario o correo
            if ((usuario == null || usuario.isEmpty()) /*&& (correo == null || correo.isEmpty())*/) {
                throw new IllegalArgumentException("Error: Debes proporcionar al menos un usuario o correo.");
            }

            if (usuario != null && !usuario.isEmpty()) {
                // Si se proporciona un usuario válido, se usa para iniciar sesión
                System.out.println("Iniciando sesión con usuario y contraseña");
                MySQLConnection.establecerConexion(usuario, password);
            } else {
                // Si no se proporciona usuario, se usa el correo para iniciar sesión
                System.out.println("Iniciando sesión con correo y contraseña");
                /**
                 * Habrá que buscar el usuario que tiene ese correo y que nos lo
                 * devuelva e iniciar sesion con ese usuario
                 */
            }

        } catch (IllegalArgumentException e) {
            System.out.println(e.getMessage());

        } catch (Exception e) { // Excepción genérica
            System.out.println("Error inesperado: " + e.getMessage());
        }
    }

    /**
     * public void InicioUsuario(String usuario, String password, String correo)
     * { /** Se iniciará sesion con el usuario, meterá el usuario o el correo y
     * contraseña Si mete el correo buscaremos en la bbdd el usuario que tiene
     * ese correo y se iniciará sesion
     *
     * }
     */
    public void ActualizarUsuario() {

        /**
         * Se le dara opciones, actualizar sus datos que no son null tipo el
         * nombre de usuario, contraseña o correo La segunda opcion será si
         * quiere pasar a ser peleador, acto seguido meterá los datos que
         * necesita un peleador
         */
    }

    public static void clasificarPeso(String sexo, double peso) {
        if (sexo.equalsIgnoreCase("hombre")) {
            if (peso < 52.2) {
                System.out.println("Peso Mosca (Hombre)");
            } else if (peso >= 52.2 && peso < 56.7) {
                System.out.println("Peso Gallo (Hombre)");
            } else if (peso >= 56.7 && peso < 61.2) {
                System.out.println("Peso Pluma (Hombre)");
            } else if (peso >= 61.2 && peso < 66.7) {
                System.out.println("Peso Ligero (Hombre)");
            } else if (peso >= 66.7 && peso < 72.6) {
                System.out.println("Peso Welter (Hombre)");
            } else if (peso >= 72.6 && peso < 79.4) {
                System.out.println("Peso Mediano (Hombre)");
            } else if (peso >= 79.4 && peso < 88.5) {
                System.out.println("Peso Semipesado (Hombre)");
            } else {
                System.out.println("Peso Pesado (Hombre)");
            }
        } else if (sexo.equalsIgnoreCase("mujer")) {
            if (peso < 45.4) {
                System.out.println("Peso Mosca (Mujer)");
            } else if (peso >= 45.4 && peso < 49.0) {
                System.out.println("Peso Gallo (Mujer)");
            } else if (peso >= 49.0 && peso < 53.5) {
                System.out.println("Peso Pluma (Mujer)");
            } else if (peso >= 53.5 && peso < 57.2) {
                System.out.println("Peso Ligero (Mujer)");
            } else if (peso >= 57.2 && peso < 66.7) {
                System.out.println("Peso Welter (Mujer)");
            } else {
                System.out.println("Peso Pesado (Mujer)");
            }
        } else {
            System.out.println("Sexo no válido. Por favor ingresa 'hombre' o 'mujer'.");
        }
    }

    public void EliminarUsuario() {
        /**
         * Comando para eliminar usuario
         */
    }

    public void SalirDeBBDD() {
        /**
         * Se cerrará la sesion y salatrá a la pagina de inicio de sesion La
         * pagina de inicio de sesion tendrá tambien una opcion para salir del
         * programa
         */
        MySQLConnection.cerrarConexion();
    }
}
