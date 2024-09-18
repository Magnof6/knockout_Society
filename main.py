# Archivo: main ejecutable
from instalacion_librerias import instalar_paquete
from traductor_texto import TraductorTexto
class Main:
    def __init__(self):
        instalar_paquete("googletrans" , "4.0.0-rc1")
        instalar_paquete("pyaudio")
        instalar_paquete("SpeechRecognition")
        instalar_paquete("pytesseract")
        instalar_paquete("Pillow")
        self.traductor = TraductorTexto()
        
    
    def run(self):
        """Inicia el programa principal."""
        print("Bienvenido al Traductor de Textos")
        self.traductor.ejecutar()

if __name__ == "__main__":
    main_app = Main()
    main_app.run()
