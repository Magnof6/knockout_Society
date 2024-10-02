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
    hideLoginForm();  // Asegúrate de que el formulario de inicio de sesión esté oculto
    var registerForm = document.getElementById("register-form");
    registerForm.style.display = "block";
}

function hideRegisterForm() {
    var registerForm = document.getElementById("register-form");
    registerForm.style.display = "none";
}

function showLoginForm() {
    hideRegisterForm();  // Asegúrate de que el formulario de registro esté oculto
    var loginForm = document.getElementById("login-form");
    loginForm.style.display = "block";
}

function hideLoginForm() {
    var loginForm = document.getElementById("login-form");
    loginForm.style.display = "none";
}
