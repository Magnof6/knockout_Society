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