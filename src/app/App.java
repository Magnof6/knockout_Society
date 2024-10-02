package app;

import Funciones.Funciones;
import conexion_bbdd.MySQLConnection;
import java.sql.*;
import java.util.Scanner;

/**
 *
 * @author Alejandro Fernandez Munoz <alejandro.fernandezmunoz@usp.ceu.es>
 */
public class App {

    public static String usuario, correo, password, apellidos, sexo, nombre;
    public static int edad;

    public static void main(String args[]) {
        Scanner sc = new Scanner(System.in);
        System.out.println("1.Iniciar sesion\n"
                + "2.Registrarse\n"
                + "3.Cerrar sesion/salir\n");
        int n = sc.nextInt();
        switch (n) {
            case 1:
                System.out.println("Introduzca:");
                System.out.println("usuario");
                usuario = sc.next();
                System.out.println("\ncontraseña");
                password = sc.next();
                //String correo = sc.next();
                Funciones.InicioUsuario(usuario, password);
                System.out.println("Introduzca una consulta:");
                String consulta = sc.next();
                //String consulta = "SELECT * FROM pg_user;";
                ResultSet resultado = MySQLConnection.ejecutarConsulta(consulta);
                MySQLConnection.procesarResultados(resultado);
                break;
            case 2:
                usuario = sc.next();
                password = sc.next();
                //correo = sc.next();
                nombre = sc.next();
                apellidos = sc.next();
                edad = sc.nextInt();
                sexo = sc.next();
                Funciones.CrearUsuarioNuevo(usuario, password, correo, nombre, apellidos, String.valueOf(edad), sexo);
                System.out.println("Introduzca una consulta:");
                consulta = sc.next();
                resultado = MySQLConnection.ejecutarConsulta(consulta);
                MySQLConnection.procesarResultados(resultado);
                break;
            case 3:
                MySQLConnection.cerrarConexion();
                break;

        }

    }

}
