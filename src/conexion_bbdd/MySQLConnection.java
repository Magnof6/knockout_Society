package conexion_bbdd;

import java.sql.*;

public class MySQLConnection {

    public static Connection connection;

    public static Connection establecerConexion(String user, String password) {
        String url = "jdbc:postgresql://aws-0-eu-west-3.pooler.supabase.com:6543/postgres?";
        // String user = "postgres.guoehkemwuuvfxnkzvfq";
        //user = "admin1.guoehkemwuuvfxnkzvfq";
        //password = "PeleaDown666$";
        user = user + ".guoehkemwuuvfxnkzvfq";
        connection = null;
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

    public static void cerrarConexion() {

        if (connection != null) {
            try {
                connection.close();
                System.out.println("Conexion cerrada");
            } catch (SQLException e) {
                System.out.println("Error al cerrar la sesion: " + e.getMessage());
            }
        }
    }

    public static ResultSet ejecutarConsulta(String consulta) {
        // = establecerConexion();
        ResultSet resultSet = null;

        try {
            Statement statement = connection.createStatement();
            resultSet = statement.executeQuery(consulta);

        } catch (SQLException e) {
            System.out.println("Error al ejecutar la consulta: " + e.getMessage());
        }
        return resultSet;
    }

    public static void procesarResultados(ResultSet resultSet) {
        try {
            // Suponiendo que no conoces los nombres de las columnas de antemano:
            ResultSetMetaData metaData = resultSet.getMetaData();
            int columnCount = metaData.getColumnCount();

            while (resultSet.next()) {
                for (int i = 1; i <= columnCount; i++) {
                    System.out.print(metaData.getColumnName(i) + ": " + resultSet.getString(i) + "  ");
                }
                System.out.println(); // Nueva línea después de cada fila
            }
        } catch (SQLException e) {
            System.out.println("Error al procesar los resultados: " + e.getMessage());
        }
    }

    public static void main(String args[]) {
        establecerConexion("postgres", "PeleaDown666$");
        String consulta = "SELECT * FROM pg_user;";  // Reemplaza por la consulta SQL que quieras ejecutar

        // Ejecutar la consulta
        ResultSet resultados = ejecutarConsulta(consulta);

        // Procesar e imprimir los resultados
        procesarResultados(resultados);

        // Cerrar la conexión
        cerrarConexion();
    }

}
