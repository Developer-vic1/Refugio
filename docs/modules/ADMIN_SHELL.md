# Panel administrativo

Rama: `feature/admin-shell`. Integración autorizada por el usuario tras cada verificación, sin autorización para main.

- Sidebar agrupada con details nativos, SVG, subruta activa, búsqueda de módulos sin acentos, contadores y preferencias locales de colapsado. Los enlaces corresponden a rutas reales protegidas; no se añade una ruta ficticia de seguridad.
- Menú móvil con overlay, cierre por Escape, foco contenido en el panel, fondo inert y restauración de foco. Sin JS, el menú administrativo permanece disponible en el documento.
- Breadcrumbs de módulo y subruta, acceso a pendientes y perfil. Layout y colores por tokens compartidos.
- Panel y `/admin/pendientes` calculan valores por consultas SQL de lectura. Resumen compartido cacheado solo dentro de la petición; sin N+1 por enlace. Agenda abierta desde la fecha actual, cupos aprobados, seis solicitudes recientes sin contactos y alertas de inventario desde la vista existente.
- El total de pendientes suma registros por proceso; no representa personas o animales únicos. Las listas de agenda y recientes son resúmenes limitados, con enlaces al módulo correspondiente.
- No modifica esquema, datos, roles, publicación pública o configuración Laragon.

Verificación: `php scripts/check-admin-workspace.php`, sintaxis PHP y JS afectados, build, rutas protegidas y navegador integrado. `tests/ui-router.php` ofrece `/admin-shell-check` en servidor aislado para revisar layout y lecturas sin crear sesión administrativa. Esta vista de prueba no acredita autenticación real ni operaciones CRUD.

La gestión completa, búsqueda SQL y paginación de cada módulo continúan en sus features. No se afirman formalizaciones, movimientos o conciliaciones terminadas por añadir accesos al panel.

## Resultado de verificación

- Sintaxis correcta en 13 PHP afectados; repetida en layout tras mejorar el nombre accesible de los contadores.
- Contratos de consultas, resumen sin datos personales, rutas protegidas y breadcrumbs comprobados con check-admin-workspace.php.
- JS afectado comprobado con node --check; npm run build correcto.
- /admin, /admin/pendientes y /admin/animales devuelven 303 sin sesión.
- Navegador integrado: búsqueda configuracion encuentra Configuración sin depender de acentos; colapsado reduce sidebar a 88px; móvil usa overlay, foco inicial en cierre, main inert y Escape restaura el botón de apertura.
- Medidas de móvil y tablet: clientWidth/scrollWidth 375/375 y 753/753; campos y título dentro del main. En desktop la sidebar queda fija y el contenido distribuido. Se corrigió el escalado de captura mediante deviceScaleFactor 1 en la emulación; las capturas quedan fuera de Git.
- Solo se verificó visualmente la fixture aislada con lectura local de contadores. No se inició sesión ni se ejecutaron operaciones administrativas.
