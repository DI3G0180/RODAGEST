#!/usr/bin/env python3
import os
import re

proj = r'C:\RODAGEST-main'

# Archivos a corregir
files_to_fix = [
    r'resources/views/auth/login.blade.php',
    r'resources/views/dashboard.blade.php',
]

# Patrones a reemplazar
# El pattern busca: <form method="POST" action="{{ route('login') }}>
# y lo reemplaza por: <form method="POST" action="{{ route('login', [], false) }}">

patterns = [
    (r"\{\{ route\('login'\}\) \}", "{{ route('login', [], false) }}"),
    # Agregar más patrones aquí
]

print("Corrigiendo rutas relativas en formularios...")
print("=" * 60)

for filepath in files_to_fix:
    full_path = os.path.join(proj, filepath)
    if not os.path.exists(full_path):
        print(f"No existe: {full_path}")
        continue
    
    print(f"\nProcesando: {filepath}")
    with open(full_path, 'r', encoding='utf8') as f:
        content = f.read()
    
    original = content
    
    # Reemplazar el action del formulario de login
    # Pattern: el action del formulario de login (línea 188)
    # <form method="POST" action="{{ route('login') }}">
    
    # Reemplazo específico para el formulario de login
    login_form_old = "<form method=\"POST\" action=\"{{ route('login') }}\">"
    login_form_new = "<form method=\"POST\" action=\"{{ route('login', [], false) }}\">"
    
    if login_form_old in content:
        content = content.replace(login_form_old, login_form_new)
        print(f"  -> Formulario login reemplazado: {login_form_old[:30]}... -> {login_form_new[:30]}...")
    else:
        print(f"  -> Formulario login no encontrado (buscando variantes)...")
        # Try with single quotes
        login_form_old2 = '<form method="POST" action="{{ route(\'login\') }}">'
        if login_form_old2 in content:
            content = content.replace(login_form_old2, login_form_new)
            print(f"  -> Formulario login (variant) reemplazado")
    
    # Reemplazar otros formularios en el dashboard que usen route('...')
    # Pattern: route('inspecciones.index'), route('unidades.index'), etc.
    # Estos suelen tener: href="{{ route('nombre') }} o action="{{ route('nombre') }}
    
    # Reemplazar route('nombre') por route('nombre', [], false) en actions y hrefs
    # Solo si están sin el tercer parámetro false
    
    # Ejemplo: action="{{ route('inspecciones.create') }}"
    # -> action="{{ route('inspecciones.create', [], false) }}"
    
    # Patrón más general para route con 1 parámetro
    pattern_route = r'action="{{ route\(\'[^\']+\'\}}\)"'
    matches = re.findall(pattern_route, content)
    for m in matches:
        # Extraer el nombre de la ruta
        route_name = m.replace('action="{{ route(\"', '').replace('")', '').replace('"', '')
        # No tocar route('login') ya que ya lo manejamos
        if 'login' in route_name and 'route(\'login\'' not in m:
            continue
        # Reemplazar solo si no tiene el tercer parámetro
        # La ruta actual tiene: {{ route('login') }} -> ya lo cambiamos
        # Las demás rutas legacy
    
    # Escribir de vuelta
    with open(full_path, 'w', encoding='utf8') as f:
        f.write(content)
    
    # Contar cambios
    count = content.count("route('") + content.count('route("')
    print(f"  -> Total de route() en archivo: ~{count}")

print("\n" + "=" * 60)
print("Corrección de rutas relativas completada")