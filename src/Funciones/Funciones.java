package Funciones;

/**
 *
 * @author Alejandro Fernandez Munoz <alejandro.fernandezmunoz@usp.ceu.es>
 */
public class Funciones {

    public void CrearUsuarioNuevo(String usuario, String password, String correo) {
        /**
         * El ususario meterá su nombre de usuario, contraseña y correo
         * iniciaremos sesion en la bbdd con un admin que solo cree usuarios
         * acto seguido cerraremos sesion y pasaremos a la funcion inicio sesion
         */
    }

    public void InicioUsuario(String usuario, String password, String correo) {
        try {
            // Excepción si no se introduce usuario o correo
            if ((usuario == null || usuario.isEmpty()) && (correo == null || correo.isEmpty())) {
                throw new IllegalArgumentException("Error: Debes proporcionar al menos un usuario o correo.");
            }
            
            if (usuario != null && !usuario.isEmpty()) {
                // Si se proporciona un usuario válido, se usa para iniciar sesión
                System.out.println("Iniciando sesión con usuario y contraseña");
            } else {
                // Si no se proporciona usuario, se usa el correo para iniciar sesión
                System.out.println("Iniciando sesión con correo y contraseña");
            }

        } catch (IllegalArgumentException e) {
            System.out.println(e.getMessage());
            
        } catch (Exception e) { // Excepción genérica
            System.out.println("Error inesperado: " + e.getMessage());
        }
    }    
    
    /**
    public void InicioUsuario(String usuario, String password, String correo) {
        /**
         * Se iniciará sesion con el usuario, meterá el usuario o el correo y
         * contraseña Si mete el correo buscaremos en la bbdd el usuario que
         * tiene ese correo y se iniciará sesion
         
    }
    */

    public void ActualizarUsuario() {

        /**
         * Se le dara opciones, actualizar sus datos que no son null tipo el
         * nombre de usuario, contraseña o correo La segunda opcion será si
         * quiere pasar a ser peleador, acto seguido meterá los datos que
         * necesita un peleador
         */
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
    }
}
