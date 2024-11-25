<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CSS adicional para centrar los botones */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Ocupa toda la altura de la ventana */
            background: url('imagenes/FondoPantalla.webp') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .header {
            display: none; /* Ocultar la cabecera en la página de inicio */
        }

        .center-container {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8); /* Fondo semi-transparente */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .center-container h1 {
            color: black;
            margin-bottom: 20px;
        }

        .header-button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
            font-size: 18px;
            text-decoration: none;
            display: inline-block;
        }

        .header-button:hover {
            background-color: #0056b3;
        }

        .footer {
            position: absolute;
            bottom: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <div class="center-container">
        <h1>Knockout Society</h1>
        <a href="login.php" class="header-button">Login</a>
        <a href="register.php" class="header-button">Register</a>
    </div>

    <?php include 'footer.php'; ?>

    <script src="script.js"></script>
</body>
</html>