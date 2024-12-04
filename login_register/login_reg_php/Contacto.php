<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contáctanos</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos específicos para el formulario de contacto */
        .contact-form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .contact-form-container h2 {
            text-align: center;
            color: #333;
        }
        .contact-form-container form {
            display: flex;
            flex-direction: column;
        }
        .contact-form-container label {
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .contact-form-container input, 
        .contact-form-container textarea {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .contact-form-container textarea {
            resize: vertical;
        }
        .contact-form-container button {
            margin-top: 20px;
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
        <form id="contactForm">
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="message">Mensaje:</label>
            <textarea id="message" name="message" rows="5" required></textarea>
            
            <button type="submit">Enviar</button>
        </form>
        <div class="confirmation" id="confirmationMessage"></div>
    </div>

    <script>
        document.getElementById('contactForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            const response = await fetch('/knockout_society/login_register/login_reg_php/contacto_submit.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.text();
            document.getElementById('confirmationMessage').textContent = result;
            this.reset();
        });
    </script>
</body>
</html>
