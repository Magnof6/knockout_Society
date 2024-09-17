import subprocess
import sys
import os
import pyaudio
import wave
from googletrans import Translator

class TraductorTexto:
    def __init__(self):
        # Diccionarios para los idiomas disponibles
        self.idiomas_disponibles = {
            "es": "español",
            "en": "inglés",
            "fr": "francés",
            "it": "italiano",
            "de": "alemán",
            "pt": "portugués"
        }

        self.nombre_a_codigo = {
            "español": "es",
            "ingles": "en",
            "frances": "fr",
            "italiano": "it",
            "aleman": "de",
            "portugues": "pt"
        }
        
    def mostrar_idiomas_disponibles(self):
        """Muestra los idiomas disponibles."""
        print("Idiomas disponibles:")
        for codigo, idioma in self.idiomas_disponibles.items():
            print(f"{codigo}: {idioma}")

    def traducir_texto(self, text, idioma_destino):
        """Traduce un texto al idioma especificado."""
        translator = Translator()
        traduccion = translator.translate(text, dest=idioma_destino)
        
        # Mostrar la traducción
        print("Texto original:", text)
        print(f"Traducido al {self.idiomas_disponibles[idioma_destino]}: {traduccion.text}")
    
    def ejecutar(self):
        """Ejecuta el proceso de traducción interactivo."""
        while True:
            self.mostrar_idiomas_disponibles()
            
            # Pedir al usuario que ingrese el texto a traducir
            texto_a_traducir = input("Ingrese el texto a traducir (o 'salir' para terminar): ")
            if texto_a_traducir.lower() == 'salir':
                break
            
            idioma_elegido = input("Ingrese el idioma al que desea traducir: ").lower()
            
            # Convertir el nombre del idioma elegido a su código correspondiente
            idioma_elegido = self.nombre_a_codigo.get(idioma_elegido, idioma_elegido)
            
            # Verificar si el idioma elegido está en la lista de idiomas disponibles
            if idioma_elegido in self.idiomas_disponibles:
                self.traducir_texto(texto_a_traducir, idioma_elegido)
            else:
                print("Código no válido, inténtelo otra vez.")