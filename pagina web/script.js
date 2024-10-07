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

function showRegisterForm() {
    hideLoginForm();  // Asegúrate de que el formulario de inicio de sesión esté oculto
    var registerForm = document.getElementById("register-form");
    registerForm.style.display = "block";
}

function hideRegisterForm() {
    var registerForm = document.getElementById("register-form");
    registerForm.style.display = "none";
}

function showFighterRegistrationForm() {
    var fighterForm = document.getElementById("fighter-registration-form");
    fighterForm.style.display = "block";
}

function hideFighterRegistrationForm() {
    var fighterForm = document.getElementById("fighter-registration-form");
    fighterForm.style.display = "none";
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

function handleRegister(event) {
    event.preventDefault(); // Previene el envío del formulario
    // Muestra la notificación de registro de luchador
    if (confirm("¿Quieres registrarte como luchador?")) {
        hideRegisterForm(); // Oculta el formulario de registro
        showFighterRegistrationForm(); // Muestra el formulario de luchador
    }
}


function handleRegister(event) {
    event.preventDefault(); // Previene el envío del formulario
    // Muestra la notificación de registro de luchador
    if (confirm("¿Quieres registrarte como luchador?")) {
        hideRegisterForm(); // Oculta el formulario de registro
        showFighterRegistrationForm(); // Muestra el formulario de luchador
    } else {
        // Aquí puedes agregar la lógica para registrar al usuario normal
        alert("Usuario registrado como normal."); // Notificación de registro normal
        hideRegisterForm(); // Oculta el formulario de registro
        // Aquí puedes hacer cualquier otra lógica que necesites para el registro normal
    }
}
