'''
Esto que encapsule obtener el texto de una imagen. Puede que lo usemos en muchas partes del programa
Encargado:

'''

import pytesseract as tess

tess.pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
from PIL import Image

img = Image.open('media\imagen_texto.png')

text = tess.image_to_string(img)
print(text)
