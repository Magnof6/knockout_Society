# Archivo: main ejecutable
from traductor_texto import TraductorTexto
from instalacion_librerias import instalar_paquete , instalar_paquete_audio
class Main:
    def __init__(self):
        self.traductor = TraductorTexto()
        instalar_paquete("googletrans")
        instalar_paquete_audio("pyaudio")
        instalar_paquete_audio("SpeechRecognition")
        
    
    def run(self):
        """Inicia el programa principal."""
        print("Bienvenido al Traductor de Textos")
        self.traductor.ejecutar()

if __name__ == "__main__":
    main_app = Main()
    main_app.run()
