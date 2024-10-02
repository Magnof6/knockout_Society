package conexion_bbdd;

import java.sql.*;
import org.junit.After;
import org.junit.AfterClass;
import static org.junit.Assert.*;
import org.junit.Before;
import org.junit.BeforeClass;
import org.junit.Test;

/**
 *
 * @author Alejandro Fernandez Munoz <alejandro.fernandezmunoz@usp.ceu.es>
 */
public class MySQLConnectionJunitTest {

    private Connection connection;

    public boolean testConexionExitosa() {
        connection = MySQLConnection.establecerConexion();
        boolean resultado = false;

        if (connection != null) {
            try {
                if (!connection.isClosed()) {
                    resultado = true;
                }
            } catch (SQLException e) {
                System.out.println("Error al verificar la conexion: " + e.getMessage());
            }

        }
        return resultado;
    }

    @BeforeClass
    public static void setUpClass() {
    }

    @AfterClass
    public static void tearDownClass() {
    }

    @Before
    public void setUp() {
        connection = MySQLConnection.establecerConexion();
    }

    @After
    public void tearDown() {
        MySQLConnection.cerrarConexion(connection);
    }

    /**
     * Test of establecerConexion method, of class MySQLConnection.
     */
    @Test
    public void testEstablecerConexion() {
        /**
         * System.out.println("establecerConexion"); Connection expResult =
         * null; Connection result = MySQLConnection.establecerConexion();
         * assertEquals(expResult, result); // TODO review the generated test
         * code and remove the default call to fail. fail("The test case is a
         * prototype.");
         */
        boolean conexionExitosa = testConexionExitosa();
        assertTrue("La conextion se ha establecido correctamente", conexionExitosa);
    }

    /**
     * Test of cerrarConexion method, of class MySQLConnection.
     */
    @Test
    public void testCerrarConexion() {
        System.out.println("cerrarConexion");
        Connection connection = null;
        MySQLConnection.cerrarConexion(connection);
        // TODO review the generated test code and remove the default call to fail.
        fail("The test case is a prototype.");
    }

}
