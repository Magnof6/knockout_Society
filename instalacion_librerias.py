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
        
def instalar_paquete_pyaudio(paquete):
    try:
        __import__(paquete)
        print(f"El paquete {paquete} ya está instalado.")
    except ImportError:
        print(f"Instalando paquete {paquete}...")
        subprocess.check_call([sys.executable, "-m", "pip", "install", f"{paquete}"])
    except Exception as e:
        print(f"Ocurrió un error: {e}")

instalar_paquete("googletrans")
instalar_paquete_pyaudio("pyaudio SpeechRecognition")

