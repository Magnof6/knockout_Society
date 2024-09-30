package main_demo;
import Funciones.Funciones;

public class Main_Demo {
    public static void main(String[] args) {
        Funciones funciones = new Funciones();
        
        // Prueba de crear un usuario nuevo
        System.out.println("Creando un nuevo usuario...");
        funciones.CrearUsuarioNuevo("usuario_prueba", "pass123", "usuario@correo.com", "Nombre", "Apellidos", "25", "M");

        // Prueba de iniciar sesión
        System.out.println("\nIniciando sesión...");
        funciones.InicioUsuario("usuario_prueba", "pass123", "");

        // Prueba de clasificación de peso
        System.out.println("\nClasificación de peso...");
        Funciones.clasificarPeso("hombre", 70.0);
        Funciones.clasificarPeso("mujer", 50.0);

        // Prueba de cerrar sesión en la base de datos
        System.out.println("\nCerrando sesión en la base de datos...");
        funciones.SalirDeBBDD();
    }
}


