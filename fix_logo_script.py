from PIL import Image
import numpy as np

img = Image.open('public/images/logo-rodagest.jpg')
print('Resolucion original JPG:', img.size, 'modo:', img.mode)

img_rgba = img.convert('RGBA')
data = np.array(img_rgba)
r = data[:,:,0]
g = data[:,:,1]
b = data[:,:,2]
a = data[:,:,3]

light_mask = (r > 230) & (g > 230) & (b > 230)
a[light_mask] = 127

img_rgba.save('public/images/logo-rodagest.png', format='PNG')
img2 = Image.open('public/images/logo-rodagest.png')
print('Logotipo regenerado:', img2.size, 'modo:', img2.mode)
alpha = img2.getchannel('a')
print('Alpha - min:', alpha.getextrema()[0], 'max:', alpha.getextrema()[1])
print('Logotipo guardado correctamente con canal alfa')