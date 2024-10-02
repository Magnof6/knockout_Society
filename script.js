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

function showRegisterForm() {
    // Oculta otros elementos si es necesario y muestra el formulario de registro
    var registerForm = document.getElementById("register-form");
    registerForm.style.display = "block";
}

function hideRegisterForm() {
    // Oculta el formulario de registro y vuelve a la vista inicial
    var registerForm = document.getElementById("register-form");
    registerForm.style.display = "none";
}
