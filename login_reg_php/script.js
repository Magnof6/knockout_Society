window.onload = function() {
    var modal = document.getElementById("warning-modal");
    // Mantén el modal visible hasta que el usuario haga clic en "Aceptar"
    modal.style.visibility = "visible";
};

function closeWarningModal() {
    var modal = document.getElementById("warning-modal");
    modal.classList.add("hidden"); // Oculta el modal
}


function toggleMenu() {
    var menu = document.getElementById("menu");
    var icon = document.getElementById("menu-icon");

    // Alterna la visibilidad del menú
    menu.classList.toggle("open");
    
    // Alterna el icono entre el menú hamburguesa (&#9776;) y la "X" (&#10005;)
    if (menu.classList.contains("open")) {
        icon.innerHTML = "&#10005;";  // Cambia el icono a "X"
    } else {
        icon.innerHTML = "&#9776;";  // Cambia el icono a hamburguesa
    }
}


