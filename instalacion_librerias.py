import subprocess
import sys

def instalar_paquete(paquete, version=None):
    try:
        # Intentar importar el paquete
        __import__(paquete)
        print(f"El paquete {paquete} ya está instalado.")
    except ImportError:
        print(f"Instalando paquete {paquete}...")
        try:
            # Si se proporciona una versión, instálala; de lo contrario, instala la versión más reciente
            if version:
                subprocess.check_call([sys.executable, "-m", "pip", "install", f"{paquete}=={version}"])
            else:
                subprocess.check_call([sys.executable, "-m", "pip", "install", paquete])
            print(f"Paquete {paquete} instalado correctamente.")
        except subprocess.CalledProcessError as e:
            print(f"Ocurrió un error al instalar {paquete}: {e}")
    except Exception as e:
        print(f"Ocurrió un error al instalar {paquete}: {e}")
