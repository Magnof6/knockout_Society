<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Peleas y Ranking</title>
        <link rel="stylesheet" href="styles.css">
        <script>
            function toggleMenu() {
                var menu = document.getElementById("menu");
                menu.style.display = menu.style.display === "block" ? "none" : "block";
            }

            function buscarPeleador() {
                // Función para realizar la búsqueda de peleadores
                var busqueda = document.getElementById("busqueda").value;
                alert("Buscando peleador: " + busqueda);
            }
        </script>
    </head>
    <body>
        <!-- Header -->
        <div class="header">
            <div class="menu-container">
                <div id="menu-icon" class="menu-icon" onclick="toggleMenu()">&#9776;</div>
                <h1>Peleas</h1>
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

        <!-- Menú desplegable -->
        <div id="menu" class="menu">
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="">Acerca de</a></li>
                <li><a href="Contacto.php">Contacto</a></li>
                <li><a href="Fight.php">Buscar Pelea</a></li>
                <li><a href="Ranking.php">Ranking</a></li>
            </ul>
        </div>

        <!-- Filtros de búsqueda de peleas -->
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

        <!-- Buscador de peleadores -->
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

        <!-- Footer con botón flotante de Kick -->
        <div class="footer">
            <a href="https://kick.com/knockoutsociety" target="_blank" id="Kick-floating-button">
                <img src="imagenes/kickkk.png" alt="imagen-kick-Icono Flotante">
            </a>
        </div>

        <script src="script.js"></script>
    </body>
</html>
