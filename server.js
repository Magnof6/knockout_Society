const { Pool } = require('pg'); // Import the pg module
require('dotenv').config();

// Create a new PostgreSQL pool with SSL and other configurations
const pool = new Pool({
    host: process.env.SUPABASE_DB_HOST,
    port: process.env.SUPABASE_DB_PORT,
    user: process.env.SUPABASE_DB_USER,
    password: process.env.SUPABASE_DB_PASSWORD,
    database: process.env.SUPABASE_DB_NAME,
    ssl: { rejectUnauthorized: false }, // Ensure SSL is enabled for Supabase
    max: 100, // Limit the number of clients in the pool
    idleTimeoutMillis: 30000, // Idle clients will be closed after 30 seconds
    connectionTimeoutMillis: 10000, // 10-second connection timeout
    keepAlive: true // Keep the connection alive to prevent unexpected termination
});

// Function to connect to the database and run a query
async function connectAndQuery() {
    try {
        // Establish the connection
        const client = await pool.connect();
        console.log('Connected to the database successfully.');

        // Example query (replace this with your actual query)
        const result = await client.query('SELECT * FROM usuario'); // Change 'users' to your table
        console.log('Query result:', result.rows); // Log the query result

        // Always release the client back to the pool
        client.release();
    } catch (error) {
        // Handle any errors that occur during the query or connection
        console.error('Error connecting to the database or executing query:', error.message || error);

        // Optionally log more detailed error info
        if (error.code) {
            console.error(`Error Code: ${error.code}`);
        }
        if (error.hint) {
            console.error(`Hint: ${error.hint}`);
        }
    }
}

// Run the connectAndQuery function
connectAndQuery();

// Handle uncaught exceptions
process.on('uncaughtException', (error) => {
    console.error('Uncaught Exception:', error.message || error);
});

// Handle process exit events to clean up the pool
process.on('exit', () => {
    console.log('Process exiting, shutting down connection pool.');
    pool.end();
});
