#!/usr/bin/env python3
import os

proj = r'C:\RODAGEST-main'
print('=== RESUMEN DE CORRECCIONES ===')
print()

# 1. Logotipo
logo_png = os.path.exists(os.path.join(proj, 'public', 'images', 'logo-rodagest.png'))
logo_backup = os.path.exists(os.path.join(proj, 'public', 'logo-rodagest.png'))
print('1. Logotipo:')
print('   Logo PNG exists: ' + ('OK' if logo_png else 'FALTA'))
print('   Backup exists: ' + ('OK' if logo_backup else 'FALTA'))

# 2. Rutas relativas en login
with open(os.path.join(proj, 'resources', 'views', 'auth', 'login.blade.php'), 'r') as f:
    login_content = f.read()
login_form = '[]], false)' in login_content
login_img = '/images/logo-rodagest.png' in login_content
print('2. Rutas relativas login:')
print('   Form login route: ' + ('OK' if login_form else 'FALTA'))
print('   Imagen ruta relativa: ' + ('OK' if login_img else 'FALTA'))

# 3. Dashboard routes
with open(os.path.join(proj, 'resources', 'views', 'dashboard.blade.php'), 'r') as f:
    dash_content = f.read()
route_relativo = '[]], false)' in dash_content
print('3. Dashboard routes relativas: ' + ('OK' if route_relativo else 'FALTA'))

# 4. .env config
with open(os.path.join(proj, '.env'), 'r') as f:
    env_content = f.read()
sd = 'SESSION_DRIVER=file' in env_content
sdm = 'SESSION_DOMAIN=null' in env_content
csf = 'CACHE_STORE=file' in env_content
ap = 'APP_URL=http://localhost:8000' in env_content
print('4. .env configuracion:')
print('   APP_URL: ' + ('OK' if ap else 'FALTA'))
print('   SESSION_DRIVER: ' + ('OK' if sd else 'FALTA'))
print('   SESSION_DOMAIN: ' + ('OK' if sdm else 'FALTA'))
print('   CACHE_STORE: ' + ('OK' if csf else 'FALTA'))

# 5. docker-compose.yml
with open(os.path.join(proj, 'docker-compose.yml'), 'r') as f:
    comp_content = f.read()
vv = 'laravel_vendor:/app/vendor' in comp_content
vd = 'laravel_vendor:' in comp_content
print('5. docker-compose.yml:')
print('   Volumen vendor: ' + ('OK' if vv else 'FALTA'))
print('   Volumes declared: ' + ('OK' if vd else 'FALTA'))

print()
print('=== RESUMEN GENERAL ===')
all_ok = all([logo_png, login_form, login_img, route_relativo, sd, sdm, csf, ap, vv, vd])
print('Todas las correcciones aplicadas: ' + ('SI' if all_ok else 'PARCIAL'))