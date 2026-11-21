#!/usr/bin/env python3
import sys

with open('C:\\RODAGEST-main\\resources\\views\\auth\\login.blade.php', 'r', encoding='utf8') as f:
    content = f.read()

# Las cadenas exactas tal como aparecen en el archivo Blade
old1 = "<img src=\"{{ asset('images/logo-rodagest.png') }}\" alt=\"RODAGEST\" style=\"width: 195px; height: auto;\">"
new1 = "<img src=\"/images/logo-rodagest.png\" alt=\"RODAGEST\" style=\"width: 195px; height: auto;\">"

old2 = "<img src=\"{{ asset('images/logo-rodagest.png') }}\" alt=\"RODAGEST\" class=\"logo\">"
new2 = "<img src=\"/images/logo-rodagest.png\" alt=\"RODAGEST\" class=\"logo-img\">"

print("old1 match:", old1 in content)
print("old2 match:", old2 in content)

if old1 not in content:
    # Try without escaped quotes
    old1a = "<img src={{ asset('images/logo-rodagest.png') }} alt=\"RODAGEST\" style=\"width: 195px; height: auto;\">"
    print("Trying old1a...")
    
if old2 not in content:
    old2a = "<img src={{ asset('images/logo-rodagest.png') }} alt=\"RODAGEST\" class=\"logo\">"
    print("Trying old2a...")

content = content.replace(old1, new1)
content = content.replace(old2, new2)

with open('C:\\RODAGEST-main\\resources\\views\\auth\\login.blade.php', 'w', encoding='utf8') as f:
    f.write(content)

print('Actualizado OK')
total_old = content.count('asset') + content.count('logo-img')
print('logo-img count:', content.count('logo-img'))
print('asset count:', content.count('asset'))