<?php
session_start();

// Verifica que la sesión esté activa
if (!isset($_SESSION['user_email']) || !isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirige al login si no hay sesión activa
    exit;
}

// Usa los datos de la sesión
$username = $_SESSION['username'];
$email = $_SESSION['user_email'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contáctanos</title>
        <link rel="stylesheet" href="styles.css">
        <style>
        /* Estilos específicos para el formulario de contacto */
            body {
                font-family: Arial, sans-serif;
                background: url('imagenes/nuestros_servicios.png') no-repeat center center fixed;
                background-size: cover;
                margin: 0;
                padding: 0;
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }
            .contact-form-container {
                max-width: 500px; /* Ajusta el ancho para que no sea demasiado grande */
                margin: 50px auto;
                padding: 20px;
                width: 90%; /* Asegura que el ancho sea del 90% del contenedor */
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Aumenta un poco la sombra para destacar */
                position: relative;
                z-index: 10; /* Asegúrate de que esté encima del fondo */
            }
            .contact-form-container h2 {
                text-align: center;
                color: #333;
                font-size: 24px; /* Ajusta el tamaño para mayor claridad */
            }
            .contact-form-container form {
                display: flex;
                flex-direction: column;
                gap: 15px; /* Espaciado uniforme entre los elementos */
            }
            .contact-form-container label {
                font-weight: bold;
                color: #555;
            }
            .contact-form-container input, 
            .contact-form-container textarea {
                padding: 10px;
                font-size: 16px;
                border: 1px solid #ccc;
                border-radius: 4px;
                width: 100%; /* Asegura que ocupe el ancho completo del contenedor */
                box-sizing: border-box; /* Incluye padding y border en el ancho total */
            }
            .contact-form-container textarea {
                resize: vertical;
                min-height: 100px; /* Define un tamaño mínimo para el textarea */
            }
            .contact-form-container button {
                margin-top: 10px;
                padding: 10px;
                font-size: 16px;
                color: #fff;
                background-color: #007BFF;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }
            .contact-form-container button:hover {
                background-color: #0056b3;
            }
            .confirmation {
                text-align: center;
                color: green;
                margin-top: 20px;
            }
        </style>

    </head>
<bodyclass="contacto-page">
        <div class="header">
                <div class="menu-container">
                    <div id="menu-icon" class="menu-icon" onclick="toggleMenu()">&#9776;</div>
                    <h1>Contacto</h1>
                </div>
                <div class="search-section">
                    <label for="search">Buscar perfiles:</label>
                    <input type="text" id="search" placeholder="Buscar...">
                </div>
                    <!-- Perfil desplegable en la esquina derecha -->
                    <div class="profile-dropdown">
                        <button class="profile-button">Perfil ▼</button>
                        <div class="profile-content">
                            <a href="profile_user.php">Ver Perfil</a>
                            <a href="#">Configuraciones</a>
                            <a href="logout.php">Cerrar sesión</a>
                        </div>
                    </div>
        </div>
        <div id="menu" class="menu">
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="Fight.php">Buscar Pelea</a></li>
                    <li><a href="Watch.php">Ver Peleas</a></li>
                    <li><a href="Ranking.php">Ranking</a></li>
                    <li><a href="apuestaHTML.php">Apuestas</a></li>
                </ul>
        </div>
        <div class="contact-form-container">
            <h2>Contáctanos</h2>
            <p>Hola, <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($email); ?>), ¡déjanos tu mensaje!</p>
            <form id="contactForm">
                <label for="message">Mensaje:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
                
                <button type="submit">Enviar</button>
            </form>
            <div class="confirmation" id="confirmationMessage"></div>
        </div>

        <script>
            document.getElementById('contactForm').addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData();
                formData.append('message', document.getElementById('message').value);

                const response = await fetch('contacto_submit.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.text();
                document.getElementById('confirmationMessage').textContent = result;
                document.getElementById('contactForm').reset();
            });
        </script>

        <?php include 'footer.php'; ?>
    </body>
</html>
