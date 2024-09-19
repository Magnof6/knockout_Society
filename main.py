# Archivo: main ejecutable
from instalacion_librerias import instalar_paquete
import asyncio

class Main:
    def __init__(self):
        self.loop = asyncio.get_event_loop()
        self.loop.run_until_complete(self.instalar_dependencias())
        self.traducir()

    async def instalar_dependencias(self):
        instalar_paquete("googletrans", "4.0.0-rc1")
        instalar_paquete("pyaudio")
        instalar_paquete("SpeechRecognition")
        instalar_paquete("pytesseract")
        instalar_paquete("Pillow")

    def traducir(self):
        from traductor_texto import TraductorTexto
        self.traductor = TraductorTexto()
        self.traductor.ejecutar()

if __name__ == "__main__":
    main_app = Main()
    main_app.run()