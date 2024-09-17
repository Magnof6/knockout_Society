import subprocess
import sys
import os

# Instalador de paquetes google traductor
def instalar_paquete(paquete):
    try:
        __import__(paquete)
        print(f"El paquete {paquete} ya está instalado.")
    except ImportError:
        print(f"Instalando paquete {paquete}...")
        subprocess.check_call([sys.executable, "-m", "pip", "install", f"{paquete}==4.0.0-rc1"])
    except Exception as e:
        print(f"Ocurrió un error: {e}")

# Ejemplo de uso:
instalar_paquete("googletrans")

from googletrans import Translator
def translate_text(text, idioma_destino):
  
    translator = Translator()   
    traduccion= translator.translate(text, dest=idioma_destino)
        
        
    # Mostramos la traduccion
    print("texto original : ", text)
    print("Traducido al texto traducido : ", traduccion.text)
   
# Definimos los idiomas disponibles para el usuario
idiomas_disponibles = {
    "es":"español",
    "en":"ingles",
    "fr":"frances",
    "it":"italiano",
    "de":"aleman",
    "pt":"portugues"
}

# Función para mostrar los idomas disponibles
def mostrar_idiomas_disponibles():
    print("Idiomas disponibles:")
    for codigo, idioma in idiomas_disponibles.items():
        print(f"{codigo}: {idioma}")

#Diccionario para convertir el nombre del idioma a su código correspondiente
nombre_a_codigo = {
    "español": "es",
    "ingles": "en",
    "frances": "fr",
    "italiano": "it",
    "aleman": "de",
    "portugues": "pt"
}
    
def main():
    while True:
        mostrar_idiomas_disponibles()
        
        # Pedimos al usuario que ingrese el texto a traducir
        texto_a_traducir = input("Ingrese el texto a traducir (o 'salir' para terminar): ")
        if texto_a_traducir.lower() == 'salir':
            break
        
        idioma_elegido = input("Ingrese el idioma al que desea traducir: ").lower()
        
        # Convertimos el nombre del idioma elegido a su código correspondiente
        idioma_elegido = nombre_a_codigo.get(idioma_elegido, idioma_elegido)
        
        # Verificamos si el idioma elegido esta en la lista de idiomas disponibles
        # Si esta en la lista de idiomas disponibles, traducimos el texto
        if idioma_elegido in idiomas_disponibles:
            translate_text(texto_a_traducir, idioma_elegido)
        else:
            print("Código no válido, inténtelo otra vez")

if __name__ == "__main__":
    main()