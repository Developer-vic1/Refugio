# Fundamentos de interfaz

Rama: `feature/ui-foundation`. Se integra únicamente tras aprobación del usuario.

## Contratos

- MVC, rutas, controladores, PDO y configuración Laragon conservados. No se ejecutan migraciones ni escrituras de prueba en MySQL.
- `layouts/main.php` contiene el documento y selecciona `public.php`, `auth.php` o `admin.php`. Cabecera y pie públicos compartidos. Login usa layout de acceso sin pie público.
- `resources/css/app.css` importa tokens, base, componentes, layouts, animaciones y páginas. El build aplana imports locales en `public/assets/css/app.css`; Tailwind conserva su entrada y detecta vistas PHP. No editar assets generados.
- `resources/js/app.js` importa módulos de navegación, feedback, formularios, Hyperframes y comportamientos de página. El sincronizador conserva los módulos en `public/assets/js/`. No se añadió framework ni dependencia.
- Iconos SVG propios en `Support/Icons.php`: trazo 1.8, currentColor, decorativos y ocultos a tecnologías de asistencia. Los controles incluyen etiquetas.
- `component(nombre, props)` permite solamente componentes conocidos y escapa texto. `Ui::localHref` restringe acciones de componentes a rutas locales.
- Componentes disponibles: botón, badge, alerta, toast, estado vacío, campo, breadcrumbs, progreso, skeleton y confirmación. Uso real en navegación, acceso, catálogo, campañas, historias y páginas de estado.
- `flash(mensaje, tipo)` admite success/error/warning/info/progress. Los fallos existentes declaran error. `get_flash()` conserva compatibilidad con la confirmación pública. Los mensajes heredados se interpretan como información.
- Confirmaciones interceptan submit del formulario y del botón; cubren teclado. Cancelar y Escape no envían. Confirmar conserva submitter, validación nativa y CSRF. Sin soporte dialog se usa confirmación nativa.
- Formularios optan por `data-submit-state` para impedir envíos repetidos y anunciar envío. El acceso permite pegar contraseña y usar gestores. La contraseña nunca se repuebla.
- Menús cierran con Escape y restauran foco. Las subrutas indican aria-current. Las notificaciones no se ocultan automáticamente.

## Verificación reproducible

- `php scripts/check-ui.php`: escape, enlaces locales, estados ARIA, subrutas, compatibilidad flash y contrato de componentes.
- Sintaxis de PHP modificado; `node --check` de fuentes JS y sincronizador; `npm run build`; smoke de rutas sin datos privados.
- `php -S 127.0.0.1:8001 -t public tests/ui-router.php` sirve `tests/fixtures/ui.php` únicamente en un servidor loopback separado, fuera de las rutas de negocio. Usa los componentes reales sin acceder a registros ni ejecutar controladores de negocio.
- Capturas y archivos de herramientas quedan fuera de Git.

## Alcance pendiente

`feature/admin-shell` implementará jerarquía, breadcrumbs operativos y pendientes del panel. Tarjetas de dominio, DataTable y paginación SQL se implementarán con sus módulos; galería, carga múltiple, tabs, drawer y vista previa del tema también requieren sus flujos reales. La expansión de Hyperframes, cursor contextual y transiciones específicas pertenece a motion-engine. No se declara terminada esa biblioteca futura.

La aplicación sigue con publicación pública deshabilitada; no se habilitaron datos generados. No se aplicó a ciegas el parche V1.1 ni se modificaron archivos privados.

## Ajustes visuales solicitados durante la revisión

El usuario pidió mejorar inmediatamente la composición Inicio/Animales, campos, coherencia de iconos, modales centrados, transiciones y una ilustración propia. Este alcance visual se incorporó a la misma rama sin adelantar la integración de módulos posteriores.

- Catálogo con breadcrumb compacto integrado en la cabecera editorial; filtros en panel con etiquetas explícitas, búsqueda y especie; distribución de una columna en móvil y dos en tablet. Distingue ausencia de publicaciones de búsqueda sin resultados. Conserva el backend existente; aún no incorpora paginación SQL.
- PNG transparente original, rotulado como ilustración sin relación con expedientes. Prompt y archivo en `ILUSTRACION_CATALOGO.md`.
- Se reemplazaron signos decorativos de las tarjetas principales por el mismo sistema SVG. Se retiraron glow y clases magnetic/tilt sin contrato funcional. No se añadieron cursores que interfieran con los campos.
- Hyperframes es código propio basado en Web Animations API e IntersectionObserver: secuencias escalonadas, contenido siempre accesible sin JS y cancelación si se activa movimiento reducido. Tokens de duración, distancia, stagger e intensidad centralizados. View Transitions CSS para navegadores compatibles; las rutas siguen siendo documentos PHP normales.
- Diálogo nativo centrado con posición fija, límites de altura, foco inicial en Cancelar y restauración del foco.
