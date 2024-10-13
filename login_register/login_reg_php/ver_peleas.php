<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Peleas</title>
    <link rel="stylesheet" href="styles.css"> <!-- Asegúrate de enlazar correctamente tu archivo de estilos -->
</head>
<body>
    <!-- Barra de navegación -->
    <div class="navbar">
        <a href="index.php">Inicio</a>
        <a href="services.php">Servicios</a>
        <a href="contact.php">Contacto</a>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="filtros-container">
        <form id="filtros-form">
            <label for="modalidad">Modalidad:</label>
            <select id="modalidad" name="modalidad">
                <option value="todas">Todas</option>
                <option value="mma">MMA</option>
                <option value="boxeo">Boxeo</option>
            </select>

            <label for="fecha">Fecha:</label>
            <select id="fecha" name="fecha">
                <option value="reciente">Recientes</option>
                <option value="semana">Hace una semana</option>
                <option value="antiguos">Antiguos</option>
            </select>

            <label for="vistos">Más vistos:</label>
            <select id="vistos" name="vistos">
                <option value="todos">Todos</option>
                <option value="mas_vistos">Más vistos</option>
            </select>

            <label for="categoria">Categoría (Peso):</label>
            <select id="categoria" name="categoria">
                <option value="todas">Todas</option>
                <option value="ligero">Peso Ligero</option>
                <option value="medio">Peso Medio</option>
                <option value="pesado">Peso Pesado</option>
            </select>

            <button type="submit">Filtrar</button>
        </form>
    </div>

    <!-- Barra de búsqueda para peleadores -->
    <div class="buscador-container">
        <input type="text" id="busqueda" name="busqueda" placeholder="Buscar peleadores...">
        <button type="button" onclick="buscarPeleador()">Buscar</button>
    </div>

    <!-- Sección de videos -->
    <div class="videos-container">
        <h2>Videos de Peleas</h2>
        <div class="video-item">
            <h3>Título del video 1</h3>
            <video controls>
                <source src="ruta/del/video1.mp4" type="video/mp4">
                Tu navegador no soporta la reproducción de videos.
            </video>
        </div>
        <div class="video-item">
            <h3>Título del video 2</h3>
            <video controls>
                <source src="ruta/del/video2.mp4" type="video/mp4">
                Tu navegador no soporta la reproducción de videos.
            </video>
        </div>
        <!-- Añadir más videos según sea necesario -->
    </div>

    <!-- Botón flotante de Kick -->
    <div id="kick-floating-button">
        <img src="imagenes/kick-icon.png" alt="Kick">
    </div>

    <script src="script.js"></script>
</body>
</html>
