import subprocess
import sys


def instalar_paquete(paquete):
    try:
        #verificamos si el paquete esta instalado
        __import__(paquete)
    except ImportError:
        #si no esta instalado lo instalamos
        print("Instalando paquete {paquete}")
        subprocess.check_call([sys.executable, "-m", "pip", "install", f"{paquete}==4.0.0-rc1"])
        
instalar_paquete("googletrans")

