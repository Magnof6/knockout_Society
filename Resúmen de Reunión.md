# Pantalla de inicio. Interfaz.
## Menú de la izquierda

### Botón Pelear
Contiene el texto "Pelear". Si no está logueado, te lleva al **menú de creación de usuario**. Si está logueado, pero no cumple los **requisitos de luchador**, le lleva al menú de **registrarse como luchador**, y si está logueado y cumple los **requisitos de luchador**, lo lleva a la página de **Encontrar Pelea**

### Botón Mirar Pelea
Contiene el texto "Mirar Pelea". Al cliquear te envía a la **página de mirar pelea.**
### Botón Rankings
Contiene el texto "Rankings". Al cliquear te envía a la **página de Rankings.**
## Botones de iniciar sesión y registrarse
Si el usuario no está logueado, aparecen arriba a la derecha. Si el usuario está logueado, son reemplazados por el **botón de perfil del usuario**. 
El botón de iniciar sesión abre el **menú de inicio de sesión**. El botón de registrarse abre el **menú de creación de usuario**.

## Menú de inicio de sesión
Contiene el texto Iniciar Sesión; un campo para Usuario, Correo y otro campo para Contraseña; un botón de iniciar sesión y otro de cancelar.

## Menú de creación de usuario
Contiene le texto "Formulario de Registro" y los campos Nombre, Apellidos, Usuario, Sexo (con un dropdown con Masculino y Femenino), Edad, Correo y Contraseña. Luego dos botones: Registrarse y Cancelar.

Te pregunta si vas a ser luchador. Si sí, te manda al **menú de registrarse como luchador**.

## Menú registrarse como luchador
Debe pedirte Peso, Altura, Grupo sanguíneo, Ubicación y Lateralidad para ser luchador. 

### Requisitos de luchador
Ciertas partes de la aplicación deberían ser inaccesibles si el usuario no ha ingresado los datos de luchador. Éstos son el Peso, la Altura, el Grupo sanguíneo, la Ubicación y Lateralidad.
## Botón de perfil del usuario
Aparece sólo cuando el usuario está logueado. Toma el lugar de los botones de Iniciar Sesión y Registrarse. Muestra el nombre de usuario y a su izquierda la foto de perfil. Al darle click te lleva a tu **página de pérfil**

## Logo de Kick
El logo de Kick abajo a la derecha. Te lleva a la página oficial de Kick.

## Logo de la página
Arriba a la izquierda. Te lleva a la pantalla de inicio al ser cliqueado.

## Diseño de la página
Cómo se va a ver visualmente la página, pero no está decidido. Color de la UI.

# Página de Encontrar Pelea

No se puede acceder sin estar logueado como luchador.

Muestra Nombre, **Rango** y **Datos de luchador**
Muestra Ubicación, permitiendo cambiarla.
Botón de buscar pelea, contiene el texto "Buscar". Al clickearlo se pone a buscar un contrincante de acuerdo al **Criterio de búsqueda de pelea**

## Criterio de búsqueda de pelea
En orden de importancia el criterio debe considerar:
0. Estado de **Bloqueo**.
1. Sexo biológico
2. Modalidad de pelea
3. Cercanía
4. Categoría de peso
5. Edad
6. **Puntos**

## Puntos
Al ganar una pelea, un luchador gana puntos. Al perder, los pierde. Los puntos permiten comparar la habilidad relativa entre luchadores.

# Página de Mirar Pelea

Debe tener un buscador que permita buscar el nombre de luchadores. 

Debe mostrar las **peleas** en el siguiente formato


| Nombre vs Nombre | Fecha de inicio | Estado (Finalizado/En progreso/En espera) | [+] Notificar |
| ---------------- | --------------- | ----------------------------------------- | ------------- |

* Nombre vs Nombre: Los nombres de cada peleador.
* Fecha de inicio: Fecha de inicio de la pelea.
* Estado: Finalizado, en Progreso y en Espera (todavía no se ha realizado).
* **Notificar**
Al hacer clic en una pelea te manda a la **página de la pelea**.
## Notificar
Si el usuario no está logueado hacer click lo lleva al **Menú de inicio de sesión**. Si el usuario está logueado, el \[+] cambia a un tick y le llegará un notificación por algún medio antes del comienzo de la pelea.

# Página de perfil
## Perfil propio
Se accede haciendo clic en el **Botón de perfil**. El perfil debe de tener:
- Nombre de usuario,
- Foto de perfil, 
- Debería mostrar los **datos de luchador** para ser editados, 
- Debería mostrar historial de peleas en las que participó, 
- Y historial de peleas que miró

## Perfil Ajeno
Debe contener:
* Nombre de usuario y foto
* **Datos de luchador**
* Historial de peleas en las que participó
* **Botón para mensaje**
* **Botón para seguir**
* **Botón para Bloquear**
## Datos de luchador
Deben aparecer los siguientes datos del luchador en su perfil: 
- Peso,
- la Altura,
- el Grupo sanguíneo,
- la Ubicación 
- Lateralidad.
### Mensaje
La aplicación debería permitir enviar mensajes entre luchadores.
### Seguir
Un usuario debería poder seguir otro para recibir **Notificaciones** sobre sus futuras peleas.
### Bloquear
Debería poder bloquearse un usuario. Esto significa que deshabilita el **Mensaje** entre esos usuarios y además previene que esos usuarios les encuentre una pelea.

# Página de Rankings
Debería mostrar una lista de todos los luchadores, mostrando los que tienen mayor puntuación.
// Poca información

# Página de la Pelea
Al darle clic a una pelea en el buscador, debe verse una página con la información de esa pelea:
* Participantes de la pelea
* Fecha de inicio
* Estado (Finalizado/En Progreso/En Espera)
* Link al video
* **Review**
* Resultado
## Review
Los participantes de una pelea pueden dejar un comentario sobre la pelea o la actitud de su contrincante.
