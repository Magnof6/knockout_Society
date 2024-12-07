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
        /* Aseguramos que el fondo permanezca */
        body {
            background-image: url('login_register/login_reg_php/Imagenes/nuestros_servicios.png'); /* Ruta a tu imagen de fondo */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh; /* Mantener el tamaño de la pantalla */
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .contact-form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            width: 90%;
            background: rgba(255, 255, 255, 0.8); /* Fondo semitransparente */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .contact-form-container h2 {
            text-align: center;
            color: #333;
        }

        .contact-form-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .contact-form-container label {
            font-weight: bold;
            color: #555;
        }

        .contact-form-container textarea {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            resize: vertical;
            min-height: 100px;
        }

        .contact-form-container button {
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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
<body>
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
</body>
</html>
