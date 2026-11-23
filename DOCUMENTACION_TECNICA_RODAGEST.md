# RODAGEST — Documentación técnica y funcional

**Tipo de documento:** memoria técnica del sistema  
**Versión documentada:** código fuente disponible en este repositorio  
**Fecha de revisión:** 23 de noviembre de 2026  
**Producto:** aplicación web de gestión de flotilla

> Esta documentación describe lo que declara el repositorio: rutas, migraciones, modelos, controladores, vistas y configuración de despliegue. Las columnas heredadas se documentan aunque ya no se expongan en un formulario. La existencia de una tabla o un controlador no implica que exista una ruta activa para todas sus operaciones.

## 1. Portada ejecutiva y descripción general

### 1.1 Propósito

RODAGEST es una aplicación web para registrar y consultar operaciones de una flotilla de transporte. Centraliza la información de unidades y personal y conserva trazabilidad de inspecciones, aseos, turnos e incidencias. Ofrece un formulario público de reporte accesible mediante QR y un mecanismo de seguimiento mediante código.

La problemática que atiende es la dispersión de registros operativos entre distintos medios: la aplicación conserva los datos en una base relacional MySQL, aplica permisos por rol y permite consultar la actividad desde paneles web. La disponibilidad en tiempo real depende del servidor, la conectividad y la base de datos desplegados; no se afirma funcionamiento offline.

### 1.2 Alcance funcional observado

- Autenticación de personal, sesiones web y desafío TOTP opcional.
- Paneles diferenciados para administración, inspección, operación y limpieza.
- Gestión administrativa de empleados, unidades y perfiles laborales.
- Inspecciones con checklist, dictamen, estado y archivos de evidencia.
- Registro de aseos, etapas, productos y evidencia; actualización del estado de la unidad.
- Turnos de operadores con odómetro y combustible de inicio/cierre.
- Quejas o incidencias internas y públicas, adjuntos, seguimiento y actualización de estado.
- Consulta de reportes operativos y exportación XLSX.
- API versionada para autenticación móvil y algunas operaciones por rol.

## 2. Stack tecnológico y arquitectura

### 2.1 Versiones y componentes detectados

| Componente | Versión o requisito del repositorio | Observación |
|---|---|---|
| PHP de aplicación | `^8.2` en Composer; imagen `php:8.3-fpm-bookworm` | La imagen de producción fija la serie 8.3. |
| Laravel Framework | `^12.0`; `v12.64.0` en `composer.lock` | Framework MVC y componentes HTTP, ORM, sesiones y validación. |
| MySQL | `mysql:8.0` | Servicio de la composición de producción. |
| Sanctum | `^4.0`; `v4.5.0` bloqueada | Tokens personales para la API. |
| Google 2FA | `pragmarx/google2fa-laravel ^2.3`; `v2.3.1` bloqueada | TOTP para segundo factor. |
| Bacon QR Code | `^3.0`; `v3.1.1` bloqueada | Generación local del QR de vinculación. |
| Tailwind CSS | `^3.1`; `3.4.19` en `package-lock.json` | Hay vistas que cargan Tailwind por CDN. |
| Vite | `^7.0.7`; `7.3.6` bloqueada | Configurado con `laravel-vite-plugin`. |
| Alpine.js | `^3.4.2`; `3.15.12` bloqueada | Interacciones ligeras en el cliente; algunas vistas usan CDN. |
| Laravel Breeze | `^2.4`; `2.4.2` bloqueada | Base de autenticación. |
| PHPUnit | `^11.5.50`; `11.5.56` bloqueada | Ejecución de pruebas. |
| Docker | Dockerfile y Compose | Versiones del motor no fijadas en el repositorio. |
| Docker Compose | archivo Compose v2 (`docker compose`) | No hay versión de Compose fijada en el archivo. |
| Caddy | `caddy:2-alpine` | Etiqueta móvil dentro de la serie 2; no fija parche. |
| Node.js | No fijado | Requerido por Vite para compilar assets fuera de la imagen PHP. |

`@tailwindcss/vite` aparece como dependencia en `package.json`, pero `vite.config.js` no la registra: Vite configura `laravel-vite-plugin`. La imagen de producción tampoco contiene una etapa Node ni ejecuta `npm run build`; las vistas principales incluyen estilos/scripts de CDN. Por tanto, no debe interpretarse la mera presencia de esa dependencia como integración activa del plugin.

### 2.2 Patrón MVC y flujo de petición

Laravel implementa el patrón Modelo–Vista–Controlador (MVC):

1. **Rutas** en `routes/web.php`, `routes/auth.php` y `routes/api.php` asignan método HTTP y URI a un controlador o acción.
2. **Middlewares** verifican sesión/autenticación, estado activo, rol, límites de solicitudes y, para formularios web, protección CSRF.
3. **Controladores** validan la entrada, aplican reglas de negocio, persisten mediante Eloquent y preparan datos de respuesta.
4. **Modelos Eloquent** representan tablas, casts y relaciones declaradas.
5. **Vistas Blade** muestran formularios, paneles y listados HTML; la API responde JSON.
6. **MySQL y almacenamiento** conservan filas y archivos. Los archivos se guardan en el disco configurado; producción usa el volumen `rodagest_storage`.

Flujo resumido: `HTTP → Router → Middleware → Controller → Eloquent/Storage → Blade o JSON → HTTP`.

### 2.3 Despliegue Docker en AWS Lightsail

`docker-compose.prod.yml` declara tres servicios en una red interna:

| Servicio | Responsabilidad | Exposición/persistencia |
|---|---|---|
| `app` | PHP-FPM 8.3 y aplicación Laravel | Puerto FastCGI 9000 solo dentro de la red; volumen `rodagest_storage`. |
| `db` | MySQL 8.0 | Sin puerto público; volumen `rodagest_db`; healthcheck `mysqladmin ping`. |
| `caddy` | Servidor estático y proxy inverso FastCGI | Publica 80, 443 TCP y 443 UDP; persiste certificados/configuración en `caddy_data` y `caddy_config`. |

Caddy lee `APP_DOMAIN`, sirve `public/`, envía peticiones PHP a `app:9000` y sirve `/storage/*` desde el volumen de archivos públicos. Caddy gestiona HTTPS automáticamente cuando el DNS del dominio apunta al servidor y los puertos 80/443 son accesibles desde Internet. El archivo Compose exige definir `APP_DOMAIN`; el dominio `rodagest.duckdns.org` es el indicado para el despliegue, pero no está fijado directamente en el Caddyfile.

El Compose local (`docker-compose.yml`) es distinto: publica el puerto web local 8000 y el puerto de MySQL, y sus valores sirven para desarrollo. No reutilizar sus credenciales de ejemplo en producción. En Lightsail deben configurarse reglas de red entrantes para 80/443 y un registro DNS A para el dominio.

## 3. Requerimientos funcionales y no funcionales

### 3.1 Requerimientos funcionales extraídos del código

| ID | Requerimiento implementado |
|---|---|
| RF-01 | El personal puede iniciar/cerrar sesión con credenciales; el inicio aplica limitación de intentos. |
| RF-02 | Un usuario puede activar TOTP desde su perfil, vincular un autenticador mediante QR/clave y confirmar con código; puede desactivarlo con reautenticación. |
| RF-03 | El sistema presenta un dashboard cuyo contenido y métricas dependen de datos operativos y del rol. |
| RF-04 | El administrador puede listar, crear, editar y desactivar/eliminar personal, asignar roles de empleado y mantener datos de perfil. |
| RF-05 | El administrador puede consultar y actualizar hoja de vida laboral de un empleado; un operador puede consultar su propia hoja de vida. |
| RF-06 | El administrador puede registrar, consultar, editar y eliminar unidades y asignarles operador. |
| RF-07 | El inspector puede registrar inspecciones de una unidad, checklist, dictamen, prioridad, descripción y evidencia; puede consultar y eliminar registros. |
| RF-08 | El inspector puede cambiar el estado de una inspección; el controlador sincroniza el estado operativo de la unidad según el dictamen. |
| RF-09 | Personal autorizado puede consultar quejas; inspector, operador y limpieza pueden registrar quejas internas según las rutas. |
| RF-10 | Cualquier visitante puede abrir el formulario público de reporte, enviar una queja con ECO/área y adjunto opcional y obtener un código de seguimiento. |
| RF-11 | Un visitante puede consultar el seguimiento y evidencia disponible usando el código. |
| RF-12 | Administrador e inspector pueden actualizar estado de quejas; limpieza puede guardar una acción de atención. |
| RF-13 | El operador puede abrir y cerrar turnos, registrando unidad, odómetro y combustible. |
| RF-14 | Limpieza puede crear, listar, actualizar estado y eliminar registros de aseo, con campos de etapas y evidencia. |
| RF-15 | El administrador puede consultar reportes agregados por intervalo y exportarlos como XLSX. |
| RF-16 | La API móvil permite autenticar, cerrar sesión, consultar unidades y operar inspecciones, reportes y aseos con token y rol. |
| RF-17 | El sistema puede enviar avisos operativos por correo a administradores en casos internos críticos, notificar cambios de estado y enviar bienvenida al crear personal; los fallos se registran en log y no siempre interrumpen la operación. |
| RF-18 | Se proporciona endpoint de salud `/up` de Laravel. |

### 3.2 Requerimientos no funcionales

| ID | Requerimiento/propiedad | Evidencia y límite |
|---|---|---|
| RNF-01 | Contraseñas almacenadas mediante hashing de Laravel. | El modelo `User` define cast `hashed`; el alta de personal valida complejidad. |
| RNF-02 | Autorización por rol y cuenta activa. | Middlewares `role` y `active`; deniegan con 403. |
| RNF-03 | Segundo factor TOTP opcional. | Campos de usuario, controlador y rutas `/profile/two-factor` y `/2fa/verify`. |
| RNF-04 | Protección de formularios web y sesión. | Middleware CSRF estándar de Laravel y autenticación por sesión. |
| RNF-05 | Protección contra abuso de rutas sensibles. | Límites `throttle` en login, 2FA, formularios públicos y API. |
| RNF-06 | Cabeceras de seguridad. | Middleware global agrega `nosniff`, `DENY`, política de referrer y HSTS si la petición se considera HTTPS. |
| RNF-07 | Confidencialidad de parte del expediente. | Varios campos personales de `employee_profiles` usan cast cifrado de Laravel; archivos de evidencia no se cifran por esta configuración. |
| RNF-08 | Persistencia relacional y ficheros duraderos en producción. | MySQL y volúmenes nombrados Docker. |
| RNF-09 | Diseño adaptable. | Las vistas usan rejillas/clases responsivas y contenedores con desbordamiento para tablas; el código por sí solo no certifica cada tamaño de pantalla. |
| RNF-10 | Despliegue con HTTPS. | Caddy automatiza TLS; requiere DNS y puertos públicos correctamente configurados. |
| RNF-11 | Operación mantenible. | Laravel 12, Composer lock, migraciones, seeders, PHPUnit y configuración Docker. |

No hay evidencia en este repositorio de modo offline-first, cifrado integral de fotografías, alta disponibilidad multi-nodo, auditoría inmutable ni recuperación de contraseña pública activa.

## 4. Roles de usuario y matriz de permisos

### 4.1 Roles

- **`admin`:** supervisión, panel administrativo, CRUD de personal y unidades, reportes/XLSX, código QR público y gestión de estados/seguimiento de quejas. Las rutas activas no permiten al admin crear reportes internos ni registrar inspecciones/aseos.
- **`inspector`:** panel y operaciones de inspección; puede consultar quejas, registrar reportes internos y actualizar el estado de quejas.
- **`operador`:** consulta de unidad, turnos, reportes propios y hoja de vida; puede crear reportes internos y eliminar aquellos que autorice el controlador (con alcance de propiedad).
- **`limpieza`:** aseos, acciones de limpieza sobre quejas y consulta de quejas filtrada; también está incluido en el middleware de creación de reporte interno.
- **Visitante público:** no es un rol de usuario autenticado. Accede solamente a `/reportar` y a las páginas de seguimiento/evidencia protegidas por código y limitación de solicitudes.

Existe un control de aplicación para impedir crear más de un administrador mediante el alta administrativa; no hay restricción `UNIQUE` en la columna `users.role`, por lo que la unicidad no es una garantía de la base de datos ante escrituras externas.

### 4.2 Matriz de acceso declarada por las rutas

`C`: consultar/listar; `A`: alta; `M`: modificar/estado; `E`: eliminar; `—`: sin ruta para ese rol. La lógica adicional de propiedad y validación permanece en los controladores.

| Módulo/acción | Admin | Inspector | Operador | Limpieza | Público |
|---|:---:|:---:|:---:|:---:|:---:|
| Dashboard | C | C | C | C | — |
| Personal (CRUD administrativo) | C/A/M/E | — | — | — | — |
| Hoja de vida de empleados | C/M | — | — | — | — |
| Hoja de vida propia | — | — | C | — | — |
| Unidades (CRUD) | C/A/M/E | — | — | — | — |
| Detalle de unidad | C | C | C | C | — |
| Inspecciones (index/create/store/destroy) | — | C/A/E | — | — | — |
| Estado de inspección | — | M | — | — | — |
| Aseos (CRUD/estado) | — | — | — | C/A/M/E | — |
| Quejas (consulta) | C | C | C | C | — |
| Quejas internas (alta) | — | A | A | A | — |
| Quejas (estado/seguimiento) | M | M | — | — | — |
| Acción de limpieza en queja | — | — | — | M | — |
| Eliminar queja | — | — | E | — | — |
| Turnos | — | — | A/M (cierre) | — | — |
| Reportes agregados y XLSX | C | — | — | — | — |
| Formulario y seguimiento público | — | — | — | — | A/C |
| API de unidades | C | C | C | C | — |
| API de inspecciones | — | A | — | — | — |
| API de reportes | — | — | C/A | — | — |
| API de aseos | — | — | — | C/A | — |
| Perfil y 2FA propios | C/M | C/M | C/M | C/M | — |

**Nota de alcance:** la declaración de rutas de inspecciones aplica `role:inspector`, aunque un comentario en `web.php` menciona “Admin e Inspector”. Para autorización efectiva prevalece el middleware de la ruta: admin no tiene acceso a esas rutas en el estado documentado.

## 5. Diseño de base de datos

### 5.1 Modelo entidad–relación

El esquema incluye tablas de negocio, tablas de autenticación/infraestructura de Laravel y tokens Sanctum. `sessions.user_id` no declara FK en la migración. Las tablas de cache/cola no tienen relaciones de dominio.

```mermaid
erDiagram
    USERS ||--o{ UNIDADES : asigna_operador
    USERS ||--o{ INSPECCIONES : inspecciona
    USERS o|--o{ INSPECCIONES : opera
    UNIDADES ||--o{ INSPECCIONES : recibe
    INSPECCIONES ||--o{ EVIDENCIAS : adjunta
    UNIDADES ||--o{ ASEO : recibe
    USERS ||--o{ QUEJAS : reporta_como_operador
    UNIDADES ||--o{ QUEJAS : recibe
    USERS ||--o{ TURNOS : realiza
    UNIDADES ||--o{ TURNOS : utiliza
    USERS ||--o| EMPLOYEE_PROFILES : tiene
    USERS ||--o{ PERSONAL_ACCESS_TOKENS : autentica

    USERS {
      bigint id PK
      string name
      string email UK
      string role
      boolean activo
      date fecha_ingreso
      text google2fa_secret
    }
    UNIDADES {
      bigint id PK
      string economico UK
      bigint operador_id FK
      string estado
      unsignedInteger odometro
    }
    INSPECCIONES {
      bigint id PK
      bigint unidad_id FK
      bigint operador_id FK
      bigint inspector_id FK
      string estado
      datetime fecha_reporte
    }
    EVIDENCIAS {
      bigint id PK
      bigint inspeccion_id FK
      string ruta_archivo
      string tipo_media
    }
    ASEO {
      bigint id PK
      bigint unidad_id FK
      string intendente_responsable
      datetime fecha_hora
      string estado
    }
    QUEJAS {
      bigint id PK
      bigint unidad_id FK
      bigint operador_id FK
      string eco
      string area
      string estado
      string codigo_seguimiento UK
    }
    TURNOS {
      bigint id PK
      bigint operador_id FK
      bigint unidad_id FK
      datetime inicio
      datetime fin
      string estado
    }
    EMPLOYEE_PROFILES {
      bigint id PK
      bigint user_id FK_UK
      text curp
      text rfc
      date fecha_reingreso
    }
    PERSONAL_ACCESS_TOKENS {
      bigint id PK
      string tokenable_type
      bigint tokenable_id
      string token UK
      datetime expires_at
    }
```

La relación `USERS—UNIDADES` se representa como uno-a-muchos porque `unidades.operador_id` es FK no única. El modelo ofrece además una relación `hasOne` para la unidad asignada que refleja la intención funcional, pero no impone unicidad en SQL. Los tokens Sanctum usan relación polimórfica y no una FK simple a `users`.

### 5.2 Diccionario de datos

Los tipos se expresan según las definiciones Laravel de las migraciones (su tipo SQL físico puede variar ligeramente por motor). `timestamps` representa `created_at` y `updated_at`. Las columnas opcionales se marcan `NULL`; salvo indicación, las columnas declaradas son obligatorias. El esquema reúne la migración inicial y sus migraciones incrementales.

#### `users`

| Campo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | bigint | PK, autoincrement | Identificador de cuenta. |
| name | varchar(255) | requerido | Nombre mostrado. |
| email | varchar(255) | UNIQUE, requerido | Identificador de inicio de sesión. |
| email_verified_at | timestamp | NULL | Verificación de correo. |
| password | varchar(255) | requerido | Hash de contraseña. |
| role | enum/string | DEFAULT `operador`; valores iniciales admin/inspector/operador/limpieza | Rol de autorización. |
| remember_token | varchar(100) | NULL | Persistencia opcional de sesión. |
| foto | varchar(255) | NULL | Ruta de fotografía de perfil. |
| fecha_ingreso | date | NULL | Fecha de alta laboral. |
| puesto | varchar(255) | NULL | Puesto del trabajador. |
| telefono | varchar(255) | NULL | Teléfono de contacto. |
| unidad_asignada | varchar(255) | NULL | Dato heredado de asignación; la FK activa está en `unidades.operador_id`. |
| activo | boolean | DEFAULT true | Estado de acceso. |
| google2fa_secret | text | NULL | Secreto TOTP; cast cifrado en el modelo. |
| two_factor_enabled | boolean | DEFAULT false | Habilita desafío 2FA. |
| created_at, updated_at | timestamp | NULL según timestamps de Laravel | Auditoría temporal básica. |

#### `unidades`

| Campo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | bigint | PK | Identificador interno. |
| economico | string | UNIQUE, requerido | Código ECO de la unidad. |
| placa | string | NULL | Columna singular heredada de esquema. |
| placas | string | NULL | Columna plural heredada, agregada por migración posterior. |
| tipo_unidad | string | NULL | Clasificación de unidad. |
| modelo | string | NULL | Modelo/año descriptivo. |
| area | string | NULL | Área o terminal. |
| linea | string | NULL | Línea asociada, si aplica. |
| operador_id | foreignId | NULL, FK `users.id`, `nullOnDelete` | Operador asignado. |
| ruta | string | NULL | Ruta operativa histórica. |
| odometro | unsigned integer | NULL | Lectura de odómetro. |
| estado | string | DEFAULT `operativa` en el esquema normalizado | Estado operacional. |
| proximo_mantenimiento_fecha | date | NULL | Fecha prevista del siguiente servicio. |
| proximo_mantenimiento_odometro | unsigned integer | NULL | Odómetro objetivo para mantenimiento. |
| created_at, updated_at | timestamp | nullable | Timestamps. |

Las columnas de placa/tipo/línea/ruta se conservan en BD por compatibilidad con migraciones/datos; su existencia no significa que estén presentes en los formularios actuales.

#### `inspecciones`

| Campo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | bigint | PK | Identificador de inspección. |
| unidad_id | foreignId | FK `unidades.id`, cascade delete | Unidad revisada. |
| operador_id | foreignId | NULL, FK `users.id`, nullOnDelete | Operador relacionado. |
| inspector_id | foreignId | NULL, FK `users.id`, nullOnDelete | Cuenta inspectora. |
| inspector_responsable | string | requerido | Nombre/texto del responsable registrado. |
| persona_reporta | string(150) | NULL | Persona que detectó el evento. |
| ubicacion_dano | string(180) | NULL | Ubicación física del daño. |
| municipio | string(120) | NULL | Municipio del evento. |
| direccion | string(255) | NULL | Dirección o lugar. |
| categoria_evento | string(80) | NULL | Categoría del evento. |
| tipo_dano | string | requerido | Tipo de daño/hallazgo. |
| prioridad | enum baja/media/alta/critica | DEFAULT media | Prioridad. |
| estado | string | DEFAULT pendiente | Estado de seguimiento; migraciones normalizan valores previos. |
| descripcion | text | requerido en migración inicial | Descripción. |
| fecha_reporte | datetime | requerido | Momento de inspección. |
| evidencia_foto | string | NULL | Ruta de evidencia principal. |
| checklist | JSON | NULL | Respuestas de revisión. |
| dictamen | string | DEFAULT `operativa` | Dictamen operativo asociado. |
| intendente_responsable | foreignId | NULL | Campo legado; migración no declara FK explícita. |
| created_at, updated_at | timestamp | nullable | Timestamps. |

#### `evidencias`

| Campo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | bigint | PK | Identificador. |
| inspeccion_id | foreignId | FK `inspecciones.id`, cascade delete | Inspección propietaria. |
| ruta_archivo | string | requerido | Ruta en almacenamiento. |
| tipo_media | string(12) | DEFAULT `imagen` | Tipo de medio. |
| nombre_original | string | NULL | Nombre original del archivo. |
| created_at, updated_at | timestamp | nullable | Timestamps. |

#### `aseos`

| Campo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | bigint | PK | Identificador del aseo. |
| unidad_id | foreignId | FK `unidades.id`, cascade delete | Unidad atendida. |
| intendente_responsable | string | requerido en creación inicial | Responsable de limpieza. |
| area_terminal | string | requerido en creación inicial | Área/terminal. |
| fecha_hora | datetime | requerido | Momento del servicio. |
| comentarios | text | NULL | Comentario de registro. |
| tipo_limpieza | string | NULL | Tipo de limpieza/desinfección. |
| observaciones | text | NULL | Observaciones adicionales. |
| estado | string | DEFAULT `completado` | Estado de la acción. |
| etapas | JSON | NULL | Etapas o tareas completadas. |
| producto_desinfectante | string | NULL | Producto reportado. |
| evidencia_foto | string | NULL | Ruta de evidencia. |
| created_at, updated_at | timestamp | nullable | Timestamps. |

La migración inicial declara responsable y área como requeridos; migraciones seguras agregan otras columnas, pero el resultado puede depender de si la instalación ya tenía la columna y del motor al aplicar cambios condicionales.

#### `quejas`

| Campo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | bigint | PK | Identificador del reporte. |
| unidad_id | foreignId | FK `unidades.id`, cascade delete | Unidad reportada. |
| operador_id | foreignId | NULL, FK `users.id`, nullOnDelete | Operador relacionado. |
| eco | string(30) | NULL | ECO capturado para el reporte. |
| area | string(80) | NULL | Área capturada. |
| tipo_queja | string | requerido | Tipo de queja/incidencia. |
| estado | string | DEFAULT pendiente | Estado de seguimiento. |
| descripcion | text | requerido | Descripción del reporte. |
| comentarios | text | NULL | Comentario de seguimiento. |
| urgencia | string | DEFAULT normal | Nivel reportado. |
| evidencia_foto | string | NULL | Ruta de imagen adjunta. |
| nombre_reportante | string(120) | NULL | Nombre proporcionado por visitante. |
| medio_reporte | string(20) | DEFAULT interno | Canal (público/QR o interno, según flujo). |
| area_reportada | string(80) | NULL | Campo de área alterno/histórico. |
| ruta_reportada | string(120) | NULL | Ruta/lugar opcional. |
| codigo_seguimiento | string(32) | NULL, UNIQUE | Código público de seguimiento. |
| accion_atencion | text | NULL | Acción de atención/limpieza. |
| evidencia_atencion | string | NULL | Ruta de evidencia de atención. |
| created_at, updated_at | timestamp | nullable | Timestamps. |

#### `turnos`

| Campo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | bigint | PK | Identificador de turno. |
| operador_id | foreignId | FK `users.id`, cascade delete | Operador. |
| unidad_id | foreignId | FK `unidades.id`, cascade delete | Unidad utilizada. |
| odometro_inicial | unsigned integer | requerido | Lectura de apertura. |
| odometro_final | unsigned integer | NULL | Lectura de cierre. |
| combustible_inicial | string(10) | requerido | Nivel al inicio. |
| combustible_final | string(10) | NULL | Nivel al cierre. |
| inicio | datetime | requerido | Inicio del turno. |
| fin | datetime | NULL | Cierre del turno. |
| estado | string | DEFAULT abierto | Estado del turno. |
| created_at, updated_at | timestamp | nullable | Timestamps. |
| índices | índice compuesto | `(operador_id, estado)`, `(unidad_id, inicio)` | Consultas de turnos abiertos e historial. |

#### `employee_profiles`

| Campo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | bigint | PK | Identificador del expediente. |
| user_id | foreignId | FK + UNIQUE a `users.id`, cascade delete | Cuenta asociada; un expediente por usuario. |
| curp, rfc, nss | longtext | NULL; cast cifrado en modelo | Identificadores personales/fiscales. |
| fecha_nacimiento | longtext | NULL; cast cifrado | Fecha de nacimiento almacenada cifrada. |
| lugar_nacimiento, estado_civil | longtext | NULL; cast cifrado | Datos personales cifrados. |
| nombre_padre, nombre_madre, nombre_conyuge | longtext | NULL; cast cifrado | Datos familiares cifrados. |
| identificacion | longtext | NULL; cast cifrado | Identificación oficial cifrada. |
| escolaridad | varchar(120) | NULL | Escolaridad. |
| calle, numero, colonia, municipio, estado, codigo_postal | longtext | NULL; cast cifrado | Domicilio cifrado. |
| telefono_alterno | longtext | NULL; cast cifrado | Teléfono alterno cifrado. |
| estatus_imss | varchar(80) | NULL | Estatus IMSS. |
| tipo_ingreso | varchar(80) | NULL | Tipo de ingreso. |
| fecha_reingreso | date | NULL | Fecha de reingreso. |
| licencia, tarjeton | varchar(80) | NULL | Datos de licencia/tarjetón. |
| created_at, updated_at | timestamp | nullable | Timestamps. |

#### Tablas de infraestructura Laravel/Sanctum

| Tabla/campos | Tipo y restricciones | Uso |
|---|---|---|
| `password_reset_tokens`: `email`, `token`, `created_at` | `email` string PK; token string; fecha nullable | Esquema residual de Laravel. En las rutas activas no están registradas las rutas públicas de olvido/restablecimiento. |
| `sessions`: `id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity` | id string PK; user_id nullable/index; IP string(45) nullable; agente text nullable; payload longText; actividad int/index | Sesiones cuando el driver seleccionado es database; producción selecciona file. No hay FK declarada en esta migración. |
| `cache`: `key`, `value`, `expiration` | key string PK; value mediumText; expiration int/index | Cache Laravel. |
| `cache_locks`: `key`, `owner`, `expiration` | key string PK; owner string; expiration int/index | Bloqueos de cache. |
| `jobs`: `id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at` | PK id; queue index; payload longText; intentos unsignedTinyInt; timestamps Unix unsigned int, `reserved_at` nullable | Cola de trabajos. |
| `job_batches`: `id`, `name`, `total_jobs`, `pending_jobs`, `failed_jobs`, `failed_job_ids`, `options`, `cancelled_at`, `created_at`, `finished_at` | id string PK; contadores int; textos longText/mediumText; fechas Unix; algunas nullable | Lotes de cola. |
| `failed_jobs`: `id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at` | id PK; uuid UNIQUE; connection/queue text; payload/exception longText; failed_at timestamp actual | Registro de trabajos fallidos. |
| `personal_access_tokens`: `id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, timestamps | PK id; morph index; token char(64) UNIQUE; abilities/fechas nullable; expires_at index | Tokens Sanctum para clientes/API. `tokenable` es relación polimórfica sin FK directa a users. |

Las migraciones contienen evoluciones y normalizaciones de nombres/estados. En particular, nombres de archivos iniciales como `create_unidads_table` no determinan el nombre final, que es `unidades`.

## 6. Catálogo de rutas y endpoints

Las rutas se listan según la definición fuente. El prefijo `/api` de las rutas de `routes/api.php` lo aplica Laravel desde `bootstrap/app.php`. `auth` exige usuario autenticado; `active` exige cuenta activa; `role:...` restringe los roles indicados. Las rutas web de formularios usan sesión y CSRF de Laravel.

### 6.1 Web pública y autenticación (`routes/web.php`, `routes/auth.php`)

| Método | URI | Nombre | Controlador/acción | Middleware | Descripción |
|---|---|---|---|---|---|
| GET | `/` | — | Closure | — | Presenta login o redirige a dashboard según sesión. |
| GET | `/reportar` | `quejas.publica` | `QuejaPublicaController@create` | `throttle:30,1` | Formulario público por QR. |
| POST | `/reportar` | `quejas.publica.store` | `QuejaPublicaController@store` | `throttle:5,1` | Registra reporte público. |
| GET | `/seguimiento/{codigo}` | `quejas.seguimiento` | `QuejaPublicaController@seguimiento` | `whereAlphaNumeric`, `throttle:30,1` | Consulta de seguimiento por código. |
| GET | `/seguimiento/{codigo}/evidencia/{tipo}` | `quejas.seguimiento.evidencia` | `QuejaPublicaController@evidencia` | código alfanumérico, tipo `reporte/atencion`, throttle | Acceso a evidencia del reporte/atención. |
| GET | `/login` | `login` | `AuthenticatedSessionController@create` | `guest` | Muestra inicio de sesión. |
| POST | `/login` | — | `AuthenticatedSessionController@store` | `guest` | Autentica; el método decide desafío 2FA o acceso. |
| GET | `/2fa/verify` | `2fa.challenge` | `TwoFactorController@challenge` | `throttle:10,1` | Formulario de desafío TOTP. |
| POST | `/2fa/verify` | `2fa.verify` | `TwoFactorController@verify` | `throttle:5,1` | Verifica código TOTP. |
| GET | `/verify-email` | `verification.notice` | `EmailVerificationPromptController` | `auth` | Aviso de verificación. |
| GET | `/verify-email/{id}/{hash}` | `verification.verify` | `VerifyEmailController` | `auth`, `signed`, `throttle:6,1` | Confirma correo con URL firmada. |
| POST | `/email/verification-notification` | `verification.send` | `EmailVerificationNotificationController@store` | `auth`, `throttle:6,1` | Reenvía verificación. |
| GET | `/confirm-password` | `password.confirm` | `ConfirmablePasswordController@show` | `auth` | Confirma contraseña. |
| POST | `/confirm-password` | — | `ConfirmablePasswordController@store` | `auth` | Procesa confirmación. |
| PUT | `/password` | `password.update` | `PasswordController@update` | `auth` | Cambia contraseña según flujo de cuenta autenticada. |
| POST | `/logout` | `logout` | `AuthenticatedSessionController@destroy` | `auth` | Cierra sesión. |

No se registran rutas públicas `forgot-password`, `reset-password` ni registro público de usuario. `password_reset_tokens` permanece como tabla, pero no constituye por sí sola un flujo habilitado.

### 6.2 Operaciones autenticadas

Todas estas rutas están dentro de `auth` + `active`, salvo donde se indique el rol adicional.

| Método | URI | Nombre | Controlador@acción | Middleware adicional | Descripción |
|---|---|---|---|---|---|
| GET | `/dashboard` | `dashboard` | `DashboardController@index` | — | Panel según usuario/rol. |
| GET | `/quejas/qr-publico` | `quejas.qr-publico` | `QuejaPublicaController@qr` | `role:admin` | Vista/código QR de reporte público. |
| GET | `/reportes` | `reportes.index` | `ReporteController@index` | `role:admin` | Agregados por periodo. |
| GET | `/reportes/exportar` | `reportes.export` | `ReporteController@export` | `role:admin` | Exportación XLSX. |
| GET | `/unidades/{unidad}` | `unidades.show` | `UnidadController@show` | `role:admin,inspector,operador,limpieza`; unidad numérica | Detalle de unidad. |
| GET | `/empleados` | `empleados.index` | `EmpleadoController@index` | `role:admin` | Lista personal. |
| POST | `/empleados` | `empleados.store` | `EmpleadoController@store` | `role:admin` | Alta de trabajador. |
| PATCH | `/empleados/{empleado}` | `empleados.update` | `EmpleadoController@update` | `role:admin` | Actualiza datos/estado/contraseña. |
| DELETE | `/empleados/{empleado}` | `empleados.destroy` | `EmpleadoController@destroy` | `role:admin` | Elimina/desactiva según reglas del controlador. |
| GET | `/empleados/{empleado}/hoja-de-vida` | `empleados.hoja-vida` | `HojaVidaController@showEmployee` | `role:admin` | Consulta expediente. |
| PATCH | `/empleados/{empleado}/hoja-de-vida` | `empleados.hoja-vida.update` | `HojaVidaController@update` | `role:admin` | Actualiza expediente. |
| GET | `/mi-hoja-de-vida` | `empleados.hoja-vida.own` | `HojaVidaController@own` | `role:operador` | Consulta expediente propio. |
| GET | `/unidades` | `unidades.index` | `UnidadController@index` | `role:admin` | Listado de flota. |
| GET | `/unidades/create` | `unidades.create` | `UnidadController@create` | `role:admin` | Formulario de alta. |
| POST | `/unidades` | `unidades.store` | `UnidadController@store` | `role:admin` | Registra unidad. |
| GET | `/unidades/{unidad}/edit` | `unidades.edit` | `UnidadController@edit` | `role:admin` | Formulario de edición. |
| PUT/PATCH | `/unidades/{unidad}` | `unidades.update` | `UnidadController@update` | `role:admin` | Actualiza unidad. |
| DELETE | `/unidades/{unidad}` | `unidades.destroy` | `UnidadController@destroy` | `role:admin` | Elimina unidad. |
| GET | `/inspecciones` | `inspecciones.index` | `InspeccionController@index` | `role:inspector` | Lista/filtra inspecciones. |
| GET | `/inspecciones/create` | `inspecciones.create` | `InspeccionController@create` | `role:inspector` | Formulario de inspección. |
| POST | `/inspecciones` | `inspecciones.store` | `InspeccionController@store` | `role:inspector` | Guarda inspección y evidencia. |
| DELETE | `/inspecciones/{inspeccion}` | `inspecciones.destroy` | `InspeccionController@destroy` | `role:inspector` | Elimina inspección. |
| POST | `/inspecciones/{inspeccion}/actualizar-estado` | `inspecciones.actualizar-estado` | `InspeccionController@updateEstado` | `role:inspector` | Actualiza estado. |
| POST | `/inspecciones/{inspeccion}/cambiar-estado` | `inspecciones.cambiar-estado` | `InspeccionController@cambiarEstado` | `role:inspector` | Acción adicional de estado. |
| GET | `/quejas` | `quejas.index` | `QuejaController@index` | los cuatro roles | Lista reportes con filtros de rol. |
| POST | `/quejas` | `quejas.store` | `QuejaController@store` | inspector, operador, limpieza | Alta de queja interna. |
| DELETE | `/quejas/{queja}` | `quejas.destroy` | `QuejaController@destroy` | `role:operador` | Elimina según alcance del controlador. |
| PUT | `/quejas/{queja}/actualizar-estado` | `quejas.actualizar-estado` | `QuejaController@updateEstado` | admin, inspector | Cambia estado. |
| POST | `/quejas/{queja}/accion-limpieza` | `quejas.accion-limpieza` | `QuejaController@accionLimpieza` | `role:limpieza` | Registra atención de limpieza. |
| POST | `/turnos` | `turnos.store` | `TurnoController@store` | `role:operador` | Abre turno. |
| PATCH | `/turnos/{turno}/cerrar` | `turnos.close` | `TurnoController@close` | `role:operador` | Cierra turno. |
| GET | `/aseos` | `aseos.index` | `AseoController@index` | `role:limpieza` | Historial de aseos. |
| GET | `/aseos/create` | `aseos.create` | `AseoController@create` | `role:limpieza` | Formulario de aseo. |
| POST | `/aseos` | `aseos.store` | `AseoController@store` | `role:limpieza` | Registra aseo. |
| DELETE | `/aseos/{aseo}` | `aseos.destroy` | `AseoController@destroy` | `role:limpieza` | Elimina registro. |
| PATCH | `/aseos/{aseo}` | `aseos.update` | `AseoController@update` | `role:limpieza` | Actualiza registro. |
| POST | `/aseos/{aseo}/actualizar-estado` | `aseos.actualizar-estado` | `AseoController@updateEstado` | `role:limpieza` | Cambia estado. |
| GET | `/profile` | `profile.edit` | `ProfileController@edit` | usuario activo | Consulta perfil. |
| PATCH | `/profile` | `profile.update` | `ProfileController@update` | usuario activo | Actualiza perfil. |
| DELETE | `/profile` | `profile.destroy` | `ProfileController@destroy` | usuario activo | Elimina cuenta según lógica del perfil. |
| GET | `/profile/two-factor` | `2fa.setup` | `TwoFactorController@setup` | usuario activo | Prepara QR/clave TOTP. |
| POST | `/profile/two-factor/enable` | `2fa.enable` | `TwoFactorController@enable` | throttle 5/min | Confirma y activa TOTP. |
| DELETE | `/profile/two-factor` | `2fa.disable` | `TwoFactorController@disable` | throttle 5/min | Desactiva TOTP. |

### 6.3 API móvil (`/api/v1`)

Las rutas API responden JSON y están limitadas a 10 solicitudes/minuto en el grupo; login añade 5/minuto. Las rutas privadas requieren `auth:sanctum` y `active`.

| Método | URI | Controlador@acción | Middleware | Descripción |
|---|---|---|---|---|
| POST | `/api/v1/login` | `Api\MobileAuthController@login` | throttle 10/min y 5/min | Autenticación y emisión de token. |
| POST | `/api/v1/logout` | `Api\MobileAuthController@logout` | Sanctum, active | Revoca token/sesión API actual. |
| GET | `/api/v1/unidades` | `Api\MobileOperationsController@units` | Sanctum, active, cualquiera de cuatro roles | Consulta unidades. |
| POST | `/api/v1/inspecciones` | `Api\MobileOperationsController@inspections` | Sanctum, active, inspector | Registra inspección. |
| GET | `/api/v1/reportes` | `Api\MobileOperationsController@reports` | Sanctum, active, operador | Consulta reportes del operador. |
| POST | `/api/v1/reportes` | `Api\MobileOperationsController@storeReport` | Sanctum, active, operador | Registra reporte. |
| GET | `/api/v1/aseos` | `Api\MobileOperationsController@aseos` | Sanctum, active, limpieza | Consulta aseos. |
| POST | `/api/v1/aseos` | `Api\MobileOperationsController@storeAseo` | Sanctum, active, limpieza | Registra aseo. |

Los controladores y las rutas verifican validación/campos concretos. No se documentan aquí contratos JSON detallados porque cambiarían con la implementación; consultar `app/Http/Controllers/Api/` para payloads y envolturas de respuesta exactas.

## 7. Seguridad e identidad visual

### 7.1 Flujo de autenticación y controles

1. El usuario envía correo y contraseña por `POST /login`; Laravel valida credenciales y limita intentos.
2. Si la cuenta está inactiva, `active` impide el acceso a rutas protegidas.
3. Si la cuenta tiene `two_factor_enabled`, se conserva el estado pendiente de autenticación y se solicita código TOTP en `/2fa/verify`; tras la comprobación se permite continuar.
4. La contraseña se almacena mediante hashing. El alta/edición administrativa aplica la regla de contraseña robusta (`min:8`, mayúsculas, minúsculas, números y símbolos) cuando se proporciona una contraseña.
5. Los roles se comparan con middleware `role`; los formularios web usan CSRF y la API privada usa Sanctum.
6. La clave TOTP se almacena mediante cast cifrado en el modelo `User`; algunos campos del expediente también usan cifrado de atributos.

El alta pública está deshabilitada en rutas. Aunque `password_reset_tokens` existe, en el archivo activo `routes/auth.php` no hay rutas de recuperación/restablecimiento de contraseña; el flujo documentado para restablecer a empleados es administrativo. `.env.example` deja SMTP con valores vacíos de usuario/contraseña y placeholders, por lo que el envío real requiere credenciales configuradas.

`AddSecurityHeaders` establece `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY` y `Referrer-Policy: strict-origin-when-cross-origin`; añade HSTS si Laravel considera segura la solicitud. `bootstrap/app.php` confía en cabeceras reenviadas de cualquier proxy (`at: '*'`), configuración que debe revisarse si la topología de proxy cambia.

### 7.2 Identidad visual y accesibilidad adaptable

| Uso | Color |
|---|---|
| Primario | `#018FC7` |
| Secundario | `#83C0DF` |
| Bordes | `#B1D0E2` |
| Fondo suave | `#DBE7F3` |
| Fondo crema | `#FBF1E5` |

La configuración Tailwind define Figtree como tipografía sans principal. El logotipo oficial está en `public/images/logo-rodagest.png`; las vistas y estilos usan escalado responsivo y tratamiento visual sin fondo cuando corresponde. Las plantillas aplican utilidades responsivas para paneles, formularios y tablas desplazables en móvil. La revisión de código no equivale a una certificación visual automatizada en todos los navegadores y anchos.

## 8. Instalación, despliegue y pruebas

### 8.1 Desarrollo local con MySQL

Requisitos: PHP 8.2 o 8.3 y extensiones requeridas por Laravel/MySQL, Composer, Node.js/npm compatibles con Vite, MySQL 8 y Git. Para usar el MySQL de `docker-compose.yml` desde el host:

```powershell
docker compose up -d db
composer install
Copy-Item .env.example .env
```

Configurar `.env` para desarrollo local (valores de ejemplo; la composición local actual define sus propias variables):

```dotenv
APP_NAME=RODAGEST
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rodagest_db
DB_USERNAME=root
DB_PASSWORD=secret
```

El `.env.example` parte con `DB_CONNECTION=sqlite`; cambiarlo a `mysql` para seguir esta guía. Esos valores corresponden solo al Compose local incluido (`root`/`secret`, puerto publicado en loopback); no reutilizarlos en producción. Si se utiliza una instancia MySQL distinta, sustituirlos por el usuario, secreto, host y base de datos creados allí.

```powershell
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Abrir `http://127.0.0.1:8000/login`. El seeder crea los cuatro usuarios descritos abajo. Esas credenciales son únicamente de demostración/desarrollo: cambiarlas antes de cualquier exposición pública. En desarrollo también se puede usar `npm run dev` en otra terminal.

### 8.2 Producción en AWS Lightsail (Docker + Caddy)

1. Instalar Docker Engine con Compose v2, clonar el repositorio en el servidor y configurar DNS A de `rodagest.duckdns.org` hacia la IP pública/estática de Lightsail.
2. Permitir tráfico entrante TCP 80/443 y, opcionalmente, UDP 443 en firewall de Lightsail y firewall del sistema.
3. Crear `.env` a partir de `.env.example` y completar valores únicos. Variables críticas:

```dotenv
APP_NAME=RODAGEST
APP_ENV=production
APP_DEBUG=false
APP_DOMAIN=rodagest.duckdns.org
APP_URL=https://rodagest.duckdns.org
APP_KEY=<clave-base64-generada-por-Laravel>
DB_DATABASE=rodagest_db
DB_USERNAME=<usuario_no_root>
DB_PASSWORD=<secreto_largo_aleatorio>
MYSQL_ROOT_PASSWORD=<otro_secreto_largo>
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=<cuenta_smtp>
MAIL_PASSWORD=<contraseña_de_aplicacion_o_secreto_smtp>
MAIL_FROM_ADDRESS=<direccion_verificada>
MAIL_FROM_NAME=RODAGEST
```

No reutilizar la clave de ejemplo ni guardar secretos en Git. Generar `APP_KEY` con Laravel, conservarla de forma segura y no cambiarla tras almacenar datos cifrados (por ejemplo secretos TOTP/perfiles), pues cambiarla puede impedir descifrarlos.

4. Construir e iniciar servicios; luego aplicar migraciones:

```bash
docker compose -f docker-compose.prod.yml up -d --build
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec -T app php artisan optimize:clear
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache
```

No ejecutar el seeder de demostración en producción: asigna `password123` a cuentas conocidas. Crear el administrador inicial en un proceso controlado y asignar credenciales robustas. La imagen prepara el enlace simbólico de almacenamiento durante el build; verificar permisos del volumen persistente. Caddy solicitará certificados al validar DNS y conectividad. Revisar `docker compose -f docker-compose.prod.yml logs -f app caddy db` después del despliegue.

### 8.3 Usuarios de semilla

`DatabaseSeeder` registra estos usuarios y las unidades iniciales `ECO-104`, `ECO-108`, `ECO-112`, `ECO-115`:

| Correo | Rol | Contraseña de desarrollo |
|---|---|---|
| `admin@rodagest.com` | admin | `password123` |
| `inspector@rodagest.com` | inspector | `password123` |
| `operador@rodagest.com` | operador | `password123` |
| `limpieza@rodagest.com` | limpieza | `password123` |

Estas claves conocidas son inseguras para producción y el seeder no debe ejecutarse allí.

### 8.4 Pruebas automatizadas

Las pruebas están en `tests/Feature` y `tests/Unit`. Incluyen autenticación, verificación de correo, confirmación/actualización de contraseña, perfil, 2FA, reportes públicos e internos, reglas de seguridad de empleados, unicidad administrativa y página principal. Algunos nombres de pruebas de Breeze corresponden a funcionalidades estándar; consultar sus aserciones para detectar rutas que el proyecto deshabilitó.

Ejecutar localmente:

```bash
php artisan test
```

O en el contenedor productivo/dev que incluya dependencias de desarrollo:

```bash
docker compose exec -T laravel php artisan test
```

La imagen de producción se construye con `composer install --no-dev`, por lo que PHPUnit no está disponible en ella. En ese caso ejecutar pruebas en el entorno local o crear una imagen de pruebas con dependencias dev. Después de actualizar código/configuración se puede limpiar caché con `php artisan optimize:clear`.

## 9. Manual de usuario rápido

### 9.1 Administrador

1. Iniciar sesión con cuenta `admin` habilitada; si configuró 2FA, introducir el código temporal.
2. Desde el dashboard revisar indicadores, alertas y estado de flota.
3. Abrir **Personal** para crear trabajadores con rol permitido, datos de contacto, fecha de ingreso y contraseña robusta; editar información o estado y consultar hoja de vida.
4. Abrir **Unidades** para registrar ECO, datos operativos disponibles, estado inicial, mantenimiento y operador asignado.
5. En **Quejas/Incidencias**, consultar reportes recibidos, actualizar estado/seguimiento y revisar evidencias. El administrador no registra nuevos reportes desde la ruta interna.
6. Abrir **Reportes** para seleccionar un periodo y exportar el resumen XLSX.
7. Abrir el QR público para difundir el formulario de reportes. El visitante podrá consultar el avance con el código entregado.
8. Activar 2FA desde perfil → ver QR/clave en Google Authenticator → confirmar con código de seis dígitos.

### 9.2 Personal operativo

**Inspector:** iniciar sesión, ir a Inspecciones, seleccionar unidad y completar hallazgos, prioridad, dictamen/checklist y evidencia. Consultar registros y cambiar su estado desde las acciones autorizadas. Puede registrar reportes internos y dar seguimiento a su estado.

**Operador:** consultar su unidad, abrir turno indicando odómetro y combustible inicial, reportar incidencia con ECO/área/descripcion/evidencia disponible y cerrar turno con lecturas finales. Consultar reportes propios y su hoja de vida.

**Limpieza:** abrir Aseos, elegir unidad y registrar responsable, tipo/etapas, fecha, observaciones y evidencia; actualizar estado del servicio. En quejas de limpieza puede registrar la acción de atención y evidencia.

**Reporte público:** el visitante escanea el QR, completa ECO/área, tipo, urgencia, descripción y foto opcional, envía el formulario y conserva el código de seguimiento para consultar el estado. El administrador y los roles internos autorizados revisan el reporte según sus permisos.

## Anexo A. Inventario de código relacionado

- **Modelos:** `User`, `Unidad`, `Inspeccion`, `Evidencia`, `Aseo`, `Queja`, `Turno`, `EmployeeProfile`.
- **Controladores web:** `DashboardController`, `EmpleadoController`, `HojaVidaController`, `UnidadController`, `InspeccionController`, `AseoController`, `QuejaController`, `QuejaPublicaController`, `ReporteController`, `TurnoController`, `ProfileController`, `TwoFactorController` y controladores Breeze.
- **Controladores API:** `Api\MobileAuthController`, `Api\MobileOperationsController`.
- **Middlewares:** `EnsureUserHasRole`, `EnsureUserIsActive`, `AddSecurityHeaders`.
- **Vistas:** `resources/views/` contiene autenticación, paneles por rol, operaciones y reporte público/seguimiento.
- **Pruebas:** `tests/Feature/` y `tests/Unit/`.
- **Despliegue:** `Dockerfile`, `docker-compose.yml`, `docker-compose.prod.yml`, `Caddyfile`.

