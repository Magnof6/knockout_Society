import subprocess

# Comando a ejecutar (ajustado para Windows)
command = 'login_register\\installs\\vendor\\bin\\phpunit login_register\\tests\\MathTest.php --colors=always'

# Ejecutar el comando y capturar la salida
process = subprocess.Popen(command, shell=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
stdout, stderr = process.communicate()

# Mostrar la salida del comando
if stdout:
    print(stdout.decode())
if stderr:
    print(stderr.decode())
