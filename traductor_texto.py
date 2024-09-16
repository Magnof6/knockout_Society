
import subprocess
import sys

'''
def instalar_paquete(paquete):
    try:
        # code inside the try block
    except:
        # code inside the except block
        #verificamos si el paquete esta instalado
        __import__(paquete)
    except ImportError:
        #si no esta instalado lo instalamos
        print("Instalando paquete {paquete}")
        subprocess.check_call([sys.executable, "-m", "pip", "install", f"{paquete}==4.0.0-rc1"])
        
instalar_paquete("googletrans")
'''
from googletrans import Translator
def translate_text(text, idioma_destino):
  
    translator = Translator()   
    traduccion= translator.translate(text, dest=idioma_destino)
        
        
        #mostramos la traduccion
    print("texto original : ", text)
    print("Traducido al texto traducido : ", traduccion.text)
   

#definimos los idiomas disponibles para el usuario
idiomas_disponibles = {
    "es":"español",
    "en":"ingles",
    "fr":"frances",
    "it":"italiano",
    "de":"aleman",
    "pt":"portugues"
}

#definimos los idiomas disponibles(sin mostrar al usuario)

#mostramos los idomas disponibles
print("Idiomas disponibles")
for idioma,codigo in idiomas_disponibles.items():
    print(f"{idioma} : {codigo}")
    
#pedimos al usuario que ingrese el texto a traducir
texto_a_traducir = input("Ingrese el texto a traducir : ")
idioma_elegido = input("Ingrese el idioma al que desea traducir : ")

#añadimos para que el usuarioa al no introducir el cogigo se cambie automaticamente
if idioma_elegido == "español":
    idioma_elegido = "es"
elif idioma_elegido == "ingles":
    idioma_elegido = "en"
elif idioma_elegido == "frances":
    idioma_elegido = "fr"
elif idioma_elegido == "italiano":
    idioma_elegido = "it"
elif idioma_elegido == "aleman":
    idioma_elegido = "de"
elif idioma_elegido == "portugues":
    idioma_elegido = "pt"

if idioma_elegido in idiomas_disponibles:
    translate_text(texto_a_traducir,idioma_elegido)
else:
    print("Codigo no valido, intentelo otra vez")