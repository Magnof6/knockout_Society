package conexion_bbdd;

import java.sql.*;

public class MySQLConnection {

    public static Connection establecerConexion() {
        String url = "";
        String user = "";
        String password = "";
        Connection connection = null;
        try {
            connection = DriverManager.getConnection(url, user, password);
            if (connection != null) {
                System.out.println("Conexion establecida a la bbdd");
            }
        } catch (SQLException e) {
            System.out.println("Conexion fallida con la bbbdd: " + e.getMessage());
        }

        return connection;
    }

    public static void cerrarConexion(Connection connection) {

        if (connection != null) {
            try {
                connection.close();
                System.out.println("Conexion cerrada");
            } catch (SQLException e) {
                System.out.println("Error al cerrar la sesion: " + e.getMessage());
            }
        }
    }
}
