#!/usr/bin/env python3
import os
import re

proj = r'C:\RODAGEST-main'

# Archivos a actualizar
files = [
    r'resources/views/dashboard.blade.php',
    r'resources/views/dashboard/admin.blade.php',
    r'resources/views/dashboard/inspector.blade.php',
    r'resources/views/dashboard/operador.blade.php',
    r'resources/views/dashboard/limpieza.blade.php',
]

# Patterns de route que aparecen en el dashboard y que necesitamos corregir
# Usaremos un enfoque: reemplazar route('X') por route('X', [], false) 
# solo cuando route tiene exactamente un parámetro (el nombre de la ruta)

for filepath in files:
    full_path = os.path.join(proj, filepath)
    if not os.path.exists(full_path):
        print(f"No existe: {full_path}")
        continue
    
    print(f"\nProcesando: {filepath}")
    with open(full_path, 'r', encoding='utf8') as f:
        content = f.read()
    
    original = content
    
    # Strategy: replace every occurrence of route('NAME') 
    # with route('NAME', [], false)
    # But only if there's exactly one argument (the route name)
    
    # Pattern: route('something') where we capture the name
    # We need to be careful: don't break other route() calls with parameters
    
    # Usaremos una expresión regular que coincida route('CONTENTO_SIMPLE')
    # y lo reemplace añadiendo , [], false
    
    # First, let's find all route( patterns
    # pattern_route = r"route\(['\"]([^'\"]+)['\"]\)"
    # matches = re.findall(pattern_route, content)
    # print(f"  Found {len(matches)} route() calls")
    
    # Better: just add , [], false after every route('..." pattern that doesn't already have it
    # We'll match: route('something')  where something doesn't contain a comma
    
    pattern = r"route\(['\"]([^'\"]+)['\"]\)"
    
    def replace_route(match):
        route_name = match.group(1)
        # Check if already has the third parameter by looking at the full match
        full = match.group(0)
        # If it's just route('name'), add the parameters
        return f"route('{route_name}', [], false)"
    
    new_content = re.sub(pattern, replace_route, content)
    
    if new_content != content:
        with open(full_path, 'w', encoding='utf8') as f:
            f.write(new_content)
        print(f"  -> Reemplazados route() en {filepath}")
    else:
        print(f"  -> No se encontraron route() simples para reemplazar")
    
    # Count route occurrences after
    count = len(re.findall(pattern, new_content))
    print(f"  -> Total route() después: {count}")

print("\nCorrección de rutas relativas en dashboards completada")