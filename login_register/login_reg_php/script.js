// Función para buscar peleadores
function buscarPeleador() {
    var input = document.getElementById('busqueda').value;
    if (input.trim() === "") {
        alert("Por favor, introduce un nombre de peleador.");
    } else {
        alert("Buscando peleador: " + input);
        // Aquí puedes añadir lógica para la búsqueda de peleadores en tu base de datos
    }
}

// Función para manejar el envío del formulario de filtros
document.getElementById('filtros-form').onsubmit = function(event) {
    event.preventDefault();
    alert("Aplicando filtros..."); // Aquí puedes añadir lógica para aplicar filtros en la búsqueda de peleas
};

// Función para cerrar el modal de advertencia
window.onload = function() {
    var modal = document.getElementById("warning-modal");
    // Mantén el modal visible hasta que el usuario haga clic en "Aceptar"
    modal.style.visibility = "visible";
};

function closeWarningModal() {
    var modal = document.getElementById("warning-modal");
    modal.classList.add("hidden"); // Oculta el modal
}

// Función para alternar el menú de navegación
function toggleMenu() {
    var menu = document.getElementById("menu");
    var icon = document.getElementById("menu-icon");

    // Alterna la visibilidad del menú
    menu.classList.toggle("open");
    
    if (menu.classList.contains("open")) {
        icon.innerHTML = "&#10005;";
    } else {
        icon.innerHTML = "&#9776;";
    }
}
