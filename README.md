# RODAGEST

Sistema web de **control operativo para unidades de transporte**. Permite llevar el registro de cada unidad en patio/terminal y dar seguimiento a su inspección, limpieza y quejas, con un dashboard distinto para cada rol.

**En producción:** https://rodagest.duckdns.org/

## Funcionalidades

- **Unidades** — Alta y administración de unidades: número económico, placa, tipo de unidad y área (con captura manual de ECO y área). Incluye **hoja de vida** por unidad y **mantenimiento preventivo** (próximo servicio por fecha u odómetro).
- **Inspecciones** — Reportes de daño por unidad con tipo de daño, prioridad, estado y descripción, con carga de **evidencias** (archivos/fotos) y actualización de estado. Incluye detalle de inspección diaria.
- **Aseos** — Registro de limpieza por unidad: intendente responsable, área de terminal, fecha y hora, y comentarios. La limpieza se completa en **4 etapas** (barrido, pasamanos y asientos, cabina y exterior, nebulización) con foto de evidencia; la unidad solo se libera como operativa cuando las 4 están completas.
- **Quejas** — Registro y seguimiento de quejas ligadas a una unidad y su operador, con subida de evidencia y cambio de estado. Incluye **queja pública** con QR y seguimiento por folio, sin necesidad de iniciar sesión.
- **Turnos** — Apertura y cierre de turnos por operador, ligados a su unidad asignada.
- **Reportes** — Reportes y KPIs de la operación (flota, inspecciones, aseos y quejas abiertas).
- **Autenticación y roles** — Login con splash screen y dashboards por rol. El dashboard muestra KPIs, estado de la flota, alertas por rol, servicios preventivos próximos y si la unidad del operador está liberada (inspección aprobada hoy + aseo completado hoy). Incluye **segundo factor (2FA) con TOTP**. El restablecimiento público de contraseña está desactivado: lo realiza el administrador desde el módulo Personal.

## API móvil

La aplicación expone una **API REST v1** (`/api/v1`) para el cliente móvil, autenticada con **Laravel Sanctum** (tokens Bearer, vigencia de 30 días):

| Método | Endpoint | Acceso | Uso |
| --- | --- | --- | --- |
| POST | `/login` | Público (límite 5/min) | Correo, contraseña, dispositivo y código TOTP si 2FA está activo. Devuelve el token. |
| POST | `/logout` | Bearer | Revoca el token actual. |
| GET | `/unidades` | Todos los roles | Lista paginada; el operador solo ve sus unidades. |
| POST | `/inspecciones` | Inspector | Registra inspección, checklist y foto. |
| GET/POST | `/reportes` | Operador | Lista los propios reportes / crea reporte de su ECO asignado. |
| GET/POST | `/aseos` | Limpieza | Lista paginada / registra aseo con foto opcional. |

El detalle completo de la API, la arquitectura y el despliegue está en [`DOCUMENTACION_TECNICA.md`](DOCUMENTACION_TECNICA.md).

## Roles de usuario

| Rol | Descripción |
| --- | --- |
| `admin` | Administrador — acceso a todos los módulos |
| `inspector` | Inspector de patio — inspecciones y quejas |
| `operador` | Operador / chófer — quejas de su unidad |
| `limpieza` | Personal de limpieza / intendente — aseos |

## Stack

- **Backend:** Laravel 12 · PHP 8.2 · Laravel Breeze (autenticación) · Laravel Sanctum (API móvil)
- **Base de datos:** MySQL 8
- **Frontend:** Blade · Tailwind CSS · Alpine.js · Vite
- **Entorno:** Docker Compose (app + MySQL); en producción, Dockerfile con PHP-FPM y Caddy (HTTPS) en AWS Lightsail
- **Seguridad:** segundo factor TOTP, política de contraseñas, middleware de cabeceras de seguridad, verificación de rol y de usuario activo; sesiones cifradas (`SESSION_ENCRYPT=true`) y `APP_DEBUG=false` en producción

## Requisitos

- PHP 8.2+ y Composer
- Node.js y npm
- MySQL 8 — o Docker, si usas el entorno incluido

## Instalación

### Con Docker

```bash
git clone https://github.com/DI3G0180/RODAGEST.git
cd RODAGEST
cp .env.example .env
docker compose up -d
```

### En local

```bash
git clone https://github.com/DI3G0180/RODAGEST.git
cd RODAGEST
composer setup        # instala dependencias, genera la key y corre migraciones
php artisan migrate --seed
composer run dev      # servidor + Vite + logs
```

La aplicación queda disponible en `http://localhost:8000`.

### Producción (AWS Lightsail)

Se usa `docker-compose.prod.yml` (no el de desarrollo), con dominio propio apuntando al servidor:

```bash
docker compose -f docker-compose.prod.yml up -d --build
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
```

Los pasos completos están en `DOCUMENTACION_TECNICA.md`.

## Usuarios de prueba

Al correr los seeders (`php artisan db:seed`) se crean estos usuarios, uno por rol. La contraseña es la definida en `database/seeders/UserSeeder.php`.

| Usuario | Correo | Rol |
| --- | --- | --- |
| Administrador RodaGest | admin@rodagest.com | `admin` |
| Inspector de Patio | inspector@rodagest.com | `inspector` |
| Operador / Chófer | operador@rodagest.com | `operador` |
| Personal de Limpieza | limpieza@rodagest.com | `limpieza` |

## Estructura del proyecto

```
app/
  Http/Controllers/   Controladores de cada módulo (Unidad, Inspeccion, Aseo, Queja, QuejaPublica, Turno, HojaVida, Reporte, Dashboard)
  Http/Controllers/Api/  API móvil v1 (autenticación y operaciones)
  Http/Middleware/    Cabeceras de seguridad, verificación de rol y de usuario activo
  Http/Requests/      Validación de formularios por módulo
  Models/             Unidad, Inspeccion, Aseo, Queja, Evidencia, Turno, User
database/
  migrations/         Esquema de unidades, inspecciones, aseos, quejas, evidencias, turnos, mantenimiento preventivo, 2FA y tokens de API
  seeders/            Usuarios de prueba por rol
public/images/        Logo de RODAGEST
resources/views/      Vistas Blade y dashboards por rol, queja pública (QR y seguimiento) y reportes
routes/web.php        Rutas web (protegidas por autenticación, salvo la queja pública)
routes/api.php        Rutas de la API móvil v1 (Sanctum)
```

## Pruebas

La suite cubre autenticación, acceso de los cuatro roles al dashboard, segundo factor TOTP, validación de contraseñas y restablecimiento.

```bash
php artisan test
```
