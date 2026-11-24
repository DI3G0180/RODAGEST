# Guía de diagramas de RODAGEST: contenido y ubicación

Los diagramas ya están incorporados en `DOCUMENTO_FINAL_UPTEX_RODAGEST.md` y se ofrecen por separado en `docs/diagramas_rodagest.html` y `docs/diagramas_mmd/`. En la memoria académica, conserva la numeración y el orden de esta tabla. Cada diagrama debe ir inmediatamente después del párrafo que lo introduce; el pie de figura está incluido debajo de cada bloque Mermaid.

| Figura | Diagrama | Ubicación exacta en la memoria | Qué explica |
|---|---|---|---|
| 1 | Entidad–relación (ERD) | Capítulo 2, § 2.4 «Modelo de datos», después del subtítulo «Diagrama Entidad–Relación» y antes del diccionario de datos | Tablas reales, PK/FK y cardinalidades del esquema. Las tablas de soporte de Laravel/Sanctum se describen en texto cuando no tienen relación Eloquent de negocio. |
| 2 | Navegación web por autenticación y rol | Capítulo 2, § 2.5 «Flujos de navegación», al inicio de la sección | Login, comprobación de 2FA y envío a la zona autorizada. |
| 3 | Ciclo Scrum | Capítulo 3, § 3.1 «Metodología Scrum», después de explicar roles, artefactos y eventos | Flujo de planificación, ejecución, revisión y retrospectiva de cada Sprint. |
| 4 | Cronograma Gantt por Sprint | Capítulo 3, § 3.2 «Fases, actividades y cronograma», después de la tabla de Sprints | Calendario académico propuesto del 10/07/2026 al 25/11/2026. Es una planificación, no una bitácora histórica. |
| 5 | Arquitectura de aplicación y despliegue | Capítulo 3, § 3.5 «Diagramas del sistema», subsección «Arquitectura de aplicación y despliegue» | Navegador, Caddy/HTTPS, contenedor Laravel, MySQL, almacenamiento y actores/API. |
| 6 | Casos de uso por rol | Capítulo 3, § 3.5, subsección «Casos de uso general por rol», inmediatamente después de la figura 5 | Acciones de Administrador, Inspector, Operador, Limpieza y usuario público, de acuerdo con los permisos implementados. |
| 7 | Secuencia de acceso, contraseña temporal y TOTP | Capítulo 3, § 3.5, subsección «Secuencia A» | Autenticación, validación del estado de la cuenta, segundo factor y acceso al dashboard. |
| 8 | Secuencia de operaciones con evidencia | Capítulo 3, § 3.5, subsección «Secuencia B» | Captura web/API de inspección, aseo o reporte, almacenamiento de fotografía y persistencia. |
| 9 | Baja lógica y restauración | Capítulo 3, § 3.5, subsección «Secuencia C» | Baja conservando historial, papelera y restauración por Administración. |
| 10 | Estados de unidad | Capítulo 3, § 3.5, después de la secuencia C | Cambios de estado por inspecciones, aseos y gestión administrativa. La baja lógica se modela por separado. |
| 11 | Estados de inspecciones y quejas | Capítulo 3, § 3.5, después de la figura 10 | Estados `pendiente`, `en_proceso` y `resuelto` y quién puede actualizarlos. |
| 12 | Estados de aseo | Capítulo 3, § 3.5, al cierre de la sección | Etapas del aseo y condición necesaria para liberar la unidad. |

## Cómo incorporarlos en Word o PDF

1. Abre `DOCUMENTO_FINAL_UPTEX_RODAGEST.md` para consultar la memoria completa y sus pies de figura. Mermaid se conserva como código en editores que no lo renderizan; no pegues ese código como si fuera una imagen.
2. Para insertar imágenes, abre `docs/diagramas_rodagest.html` en Edge o Chrome, localiza cada título y descarga el SVG o PNG. Inserta cada archivo en la ubicación de la tabla y conserva el pie de figura que ya está en la memoria.
3. Si el navegador no descarga desde el visor, usa el archivo correspondiente de `docs/diagramas_mmd/` como fuente editable Mermaid; `docs/diagramas_rodagest_fuentes.zip` reúne todas las fuentes. Los `.mmd` son diagramas editables, no imágenes.
4. En Word, centra la imagen, ajusta el ancho a los márgenes de página y deja el pie de figura debajo. Mantén juntas la imagen y su leyenda; evita dividir un diagrama entre páginas. Para el ERD apaisado, usa una página horizontal o ajusta a una página de ancho.
5. Actualiza la tabla de contenido después de insertar los diagramas y exporta a PDF para revisar que no se corten etiquetas ni conectores.

## Precisión documental

El ERD y las secuencias representan el esquema y flujos encontrados en el repositorio. El ciclo Scrum y el Gantt son una propuesta de organización académica documentada, no evidencia de que se hayan celebrado esos eventos en las fechas indicadas. La arquitectura de despliegue describe archivos de configuración versionados; no certifica por sí sola la disponibilidad de AWS, DNS, SMTP o HTTPS en producción.
