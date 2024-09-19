import subprocess
import sys
import os

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
                subprocess.check_call([sys.executable, "-m", "pip", "install", f"{paquete}=={version}", "--user"])
            else:
                subprocess.check_call([sys.executable, "-m", "pip", "install", paquete])
            print(f"Paquete {paquete} instalado correctamente.")

            # Verificar si el paquete se importó correctamente después de la instalación
            try:
                __import__(paquete)
                print(f"{paquete} importado correctamente después de la instalación.")
            except ImportError:
                print(f"No se pudo importar {paquete} después de la instalación.")

        except subprocess.CalledProcessError as e:
            print(f"Ocurrió un error al instalar {paquete}: {e}")
    except Exception as e:
        print(f"Ocurrió un error al instalar {paquete}: {e}")
