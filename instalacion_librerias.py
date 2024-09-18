import subprocess
import sys


def instalar_paquete(paquete):
    try:
        __import__(paquete)
        print(f"El paquete {paquete} ya está instalado.")
    except ImportError:
        print(f"Instalando paquete {paquete}...")
        subprocess.check_call([sys.executable, "-m", "pip", "install", f"{paquete}==4.0.0-rc1"])
    except Exception as e:
        print(f"Ocurrió un error: {e}")
        
def instalar_paquete_audio(paquete):
    try:
        __import__(paquete)
        print(f"El paquete {paquete} ya está instalado.")
    except ImportError:
        print(f"Instalando paquete {paquete}...")
        subprocess.check_call([sys.executable, "-m", "pip", "install", paquete])
    except Exception as e:
        print(f"Ocurrió un error: {e}")



