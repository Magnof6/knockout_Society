
knockoutsociety.linkpc.net

# Proyecto-Ingeniería-de-Software 

## Requisitos para la demo del 09/12/24

### Hosting Server

  * Desplegar la aplicacion web en un servidor para que sea usable desde cualquier navegador.

  * Servidor propio.

### Buscando Pelea, extras:

  * Debe aparecer una señal que permita al usuario seleccionar si quiere o no alternar su estado de Buscando Pelea

  * Este estado pordrá ser visualizado por una señal de color verde (Si Buscando Pelea) o rojo (No Buscando Pelea).

### Modificación del correo:

  * El usuario debe poder modificar su correo tras usar su nombre de usuario y contraseña

### Contáctanos 

  * Cuando el usuario hace clic en la sección "Contáctanos", debe abrirse una página o formulario para enviar consultas. 

  * Cuando el usuario completa el formulario, debe recibir una confirmación de envío. 

 

### Ranking 

  * Cuando el usuario accede al ranking, debe visualizar una lista ordenada por puntuación o categoría. 

  * Se mostrarán los 10 mejores del ranking (que más puntos tienen) 

 

### Watch.php (Visualización de videos) 

  * Cuando el usuario accede a esta página, debe poder reproducir los videos disponibles sin interrupciones. 

  * Cuando un video no se encuentra disponible, debe mostrarse un mensaje claro indicando el error. 

  * Cuando el usuario selecciona un video, este debe cargarse en un reproductor intuitivo y funcional. 

 

### Fight.php (Matchmaking) 

  * Cuando el usuario hace clic en el botón "Buscando pelea", su estado debe actualizarse en la base de datos correctamente. 

  * Cuando se realiza el matchmaking, el sistema debe emparejar al usuario con otro luchador compatible según los criterios definidos. 

  * Cuando el administrador accede, debe tener control para gestionar el matchmaking de manera manual si es necesario. 

 

### Mejorar la interfaz de change_password.php 

  * Cuando el usuario accede a esta página, debe ver un diseño claro e intuitivo que facilite el cambio de contraseña. 

  * Cuando el usuario completa el formulario, debe recibir mensajes inmediatos sobre el éxito o fallos al actualizar su contraseña. 

 

### Tests 

  * Cuando los tests se ejecutan, deben cubrir los requisitos clave definidos en el sistema. 

  * Cuando se detecta un error durante los tests, debe registrarse con suficiente información para facilitar su resolución. 

  * Cuando los tests finalizan, deben generar un reporte indicando el estado de cada funcionalidad probada. 

 

### Sección para apostar 

  * Cuando el usuario accede a la sección de apuestas, debe ver las peleas disponibles para apostar. 

  * Cuando el usuario realiza una apuesta, debe confirmarse visualmente que esta fue registrada con éxito. 

  * Lista usuario (Búsqueda de perfiles) 

  * Cuando el usuario busca a una persona, debe poder encontrar su perfil individual si existe en la base de datos. 

  * Cuando no se encuentra a un usuario, debe mostrarse un mensaje claro indicando que no existe. 

  * Cuando se accede al perfil de un usuario, debe mostrarse su información básica y opciones según el rol del visitante. 











## Requisitos para demo del 25/10/2024
* Menú sándwich accesible desde la pagina principal con acceso a varias otras paginas de la pagina web
* Página de usuario, accesible desde la página principal una vez iniciada sesión. Visible como botón arriba en la esquina derecha. 
* Pagina de usuario que contiene la opción de:
	* Alterar contraseña
	* Ver historial de peleas (si el usuario es luchador)
	* La opción de si no es luchador de registrarse como luchador
* Pagina de peleas donde se pueden ver las peleas que han ocurrido (sacada de la db)
* Registro de usuario no permite hacerlo si 'nombre de usuario' o 'correo' ya existe
* Inicio de sesión avisa si no encuentra usuario/contraseña.
#### Criterios de validacion:
* Una vez iniciado sesión, en la página principal habrá un menú en el lado izquierdo superior que al pinchar sobre él desplegará siete opciones distintas: Perfiles de otras personas, Inicio, Acerca de, Servicios, Buscar pelea, Ver peleas y Ranking.
* Botón en la esquina derecha, al pinchar sobre él se podrá ver 'Ver Perfil', 'Configuraciones' y 'Cerrar sesión'.
* Dentro de ver perfil se puede apretar sobre el botón cambiar contraseñan que te llevará a un formulario para cambiar la contraseña funcional. En caso de ser un usuario que no es luchador, se tiene que poder registrar el usuario ya existente como luchador. Y, ver el registro de peleas donde el usuario ha sido luchador.
* Al pinchar sobre el menú sándwich y concretamente sobre 'Ver peleas', saldrá una tabla con todas las peleas que se han registrado en el sistema. La tabla contendrá los siguientes campos: Luchador 1, Luchador 2, Categoria, Ganador, Número de rondas, Fecha, Hora de inicio, Hora Final, Estado y Ubicación.
* Cuando una persona intenta registraste con el correo o usuario de una persona que ya se ha registrado, le saltará por pantalla el siguiente mensaje. 'User with that email already exists" o "User with that username already exists".
* Si se inician con una contraseña errónea entonces saltará el mensaje de 'Invalid password'. En cambio, si se intenta iniciar sesión con el correo erróneo entonces saltará el mensaje de 'User not found'.



# Pantalla de inicio
Arriba a la derecha tiene Registrarse o iniciar sesión, si no tenés cuenta
Menú que se abre desde la izquierda
En el centro 
Fondo de pantalla
Que tenga el logo arriba a la derecha
Que tenga un logo de Kick que te lleve a la página oficial de Kick.

## Iniciar sesión
Un botón arriba a la derecha para iniciar sesión que aparece solo si no haz iniciado la sesión. Te manda a la página de iniciar sesión

## Registrarse
Un botón al lado de iniciar sesión que te lleve al menú de registración

## Página de iniciar sesión
Te pide nombre de usuario o correo y contraseña. Tiene un botón para iniciar sesión y otro botón que te mande a la página de registración.

## Página de registro
Te pide nombre de usuario, correo, contraseña. Te pregunta si quieres ser luchador y si sí te manda al menú de registración de luchador

## Página de registro luchador/Requisitos de luchador
Para validar un luchador debe subir:
* Sexo biológico
* Peso
* Modalidad preferida
* Nivel (Principiante, Intermedio, Avanzado)
* Fotografía
* Grupo sanguíneo
* Ubicación
* Lateralidad (zurdo, diestro)

## Menú de la izquierda
Buscar pelea
Mirar peleas
Rankings



-----
_REQUISITOS_ 

  

-El usuario ha de ser capaz de crear una cuenta, introduciendo su nombre, contraseña, correo, edad, nombre, sexo y apellidos. 

-El usuario puede participar en combates upgradeando su cuenta a "luchador", donde se deberás de rellenar los siguientes campos: fotografía, grupo sanguíneo, ubicación (privado), altura, peso y si es diestro o zurdo. 

-El usuario luchador podrá activar o desactivar la opción de buscando pelea.  

-Las cuentas de los usuarios luchadores incluirán sus estadísticas en cuanto a victorias, derrotas, puntos, y su rango actual.  

-Todos los usuarios podrán utilizar la herramienta de apuestas internas. 

-Todos los usuarios deben tener la opción de eliminar sus cuentas.   

-Los pesos podrán verse también por categoría, como peso pluma, peso pesado, etc...   

-Un ranking global que engloba todos los luchadores, distinguiéndolos por rangos según los puntos del usuario luchador.   

-Una tabla que tenga toda la información de los combates a nivel histórico y futuro, pudiendo observar el estado de la pelea (Esperando, en progreso, finalizado), hora de inicio y final, número de rondas que duró el combate y la categoría del combate.   

-Implementación de un sistema de búsqueda de contrincantes según la zona y puntuación del usuario luchador. 

-Elaboración de unos términos y condiciones. 
  
-Un chat entre usuarios. 

-Diferentes modalidades de peleas como: Boxeo, MMA, Sin reglas, Disfraces y Bofetones. De momento. 

-Sistema para subir videos de los mejores momentos de las peleas. 

-Elaborar un sistema para que los usuarios puedan subir sus propios videos y clips. 

-Elaborar un sistema de bans por incumpliendo de los términos y condiciones. 

-Crear una aplicación Web. 

-Crear y utilizar una cuenta de Kick para trasmitir los combates de rangos importantes. 

-Encriptar la cuenta y contraseña de usuario. 

-Mapa de zonas activas 

-Buscador de videos y clips 


# Client Story

## Pantalla de inicio
Una aplicacion web en la que pueda iniciar sesión un usuario con ciertos parámetros.
Opción de sign in y registrarte, abajo te aparecen cosas
* Una vez iniciado que muestre tu perfil
* Botón de ver peleas
* Ver los rankings
* Botón Mirar peleas
## Botón Pelear
"Buscar Pelea" En el menú de la izquierda, inicialmente está greyed out
Si cliquea sin estar logueado, te pide crear una cuenta, si cliquea siendo usuario normal te pide rellenar información adicional para habilitarte para luchador y luego se habilita
Que te permita modificar tu ubicación
Te muestra tus datos
* Nombre, Rango y datos de luchador
* Ubicación
* Botón Buscar Pelea "Buscar"
Te tienen que aparecer opciones ordenadas por un criterio, 
Criterio
1. Sexo biológico
2. Modalidad
3. Cercanía (Si está lejos no va a pasar)
4. Categoría de peso
5. Edad
6. Puntos

// tinder de peleas??
## Buscar peleas de otros
* Te muestra las peleas a futuro
* Por defecto lo más cercano al presente
* Un buscador para buscar nombres
* Peleas aparecen en formato "Nombre vs Nombre"

| Nombre vs Nombre | Fecha de inicio | Estado (Finalizado/En progreso/En espera) | [+] Notificar |
| ---------------- | --------------- | ----------------------------------------- | ------------- |

## Registrarse
Nombre
o
Correo
Contraseña
Luego te manda al inicio de nuevo.
Segundo paso, te pregunta si vas a ser peleador, y ahi te pide la informacion

## una vez iniciada la sesión
Se cambia el botón de iniciar
Aparece tu nombre y tu imagen
Se 

## Sin usuario
* Podés buscar, y ver peleas normalmente
* 
## Usuario
Poder seguir a luchadores y sus peleas (Te notifica sobre peleas por venir y actuales)
* Podés marcar una pelea que está por realizarse para que te notifique cuando empiece.
Tener un historial de las peleas que ya viste para poder volver a verla
## Luchador
Tras crear la cuenta, para poder luchar hay que rellenar información adicional, o sino está bloqueada la opción de luchar. Una vez que te validás como luchador, cambia la interfaz, arriba a la derecha al estar logueado aparece tu ranking. 
Podés poner tu banner para tu coso de perfil.

## Perfil (Propio)
* Nombre de usuario y foto
* Atributos menos contraseña (Sexo, edad, peso, etc. lo que ya tenemos)
* Historial de peleas participadas (si aplica)
* Historial de peleas visualizadas en el coso esew
* Botón cambio de contraseña, cambio de correo esas cosas
## Perfil ajeno
* Nombre de usuario y foto
* Atributos públicos (Los que interesan para pelear)
* Historial de peleas participadas
	* También peleas en espera, o peleas en progreso.
* Botón para mensaje
* Botón para seguir
* Botón para bloquear

## Bloquear
* Deshabilita chat
* No te encuentra pelea con esa persona

## Review
* Por datos fraudulentos
* Reportar por 

## Validación
* Tras juntarse se sacan una foto para validar el combate

## Pelea (al hacer clic en una pelea en una búsqueda)
- Nombre vs nombre
- Fecha
- Estado
* Foto de validación (privada para reportar)
* Link al video
* Review 


## Grabacion de combates
* Todos los combates se graban
## Subida de rango
Sistema Elo
