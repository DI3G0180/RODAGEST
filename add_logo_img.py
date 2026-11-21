#!/usr/bin/env python3
with open('C:\\RODAGEST-main\\resources\\views\\dashboard.blade.php', 'r', encoding='utf8') as f:
    content = f.read()

# Reemplazar la línea del sidebar que no tiene logo-img a pesar de tener la nueva ruta
old = '<img src=\"/images/logo-rodagest.png\" alt=\"RodaGest Logo\" class=\"h-8 w-auto\">'
new = '<img src=\"/images/logo-rodagest.png\" alt=\"RodaGest Logo\" class=\"h-8 w-auto logo-img\">'

if old in content:
    content = content.replace(old, new)
    print('Reemplazado sidebar logo')
else:
    print('No se encontró el pattern old, buscando variantes...')
    # Try with single quotes
    old2 = '<img src=\"/images/logo-rodagest.png\" alt=\"RodaGest Logo\" class=\"h-8 w-auto\">'
    print('Pattern found:', old2 in content)

with open('C:\\RODAGEST-main\\resources\\views\\dashboard.blade.php', 'w', encoding='utf8') as f:
    f.write(content)

logo_img_count = content.count('logo-img')
print('logo-img count total:', logo_img_count)