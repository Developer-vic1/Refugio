# PROMPT MAESTRO PARA CODEX — REFUGIO VIDA ANIMAL V2

## 0. Rol, contexto y resultado esperado
Actúa como **arquitecto de software PHP**, **ingeniero full-stack**, **diseñador de sistemas UI/UX**, **especialista en accesibilidad, seguridad web y GitFlow**. Trabajarás sobre el código REAL que existe en la computadora del usuario; no sustituyas el proyecto por una maqueta ni inventes que las funciones están terminadas. Antes de programar inspecciona la estructura y verifica cada afirmación de este documento contra los archivos actuales. Si encuentras diferencias, conserva los cambios existentes y documenta la corrección del plan.

**Proyecto:** Refugio Vida Animal (refugio de animales rescatados).
**Ruta local prevista:** `C:\laragon\www\Refugio` (buscarla en el entorno si no existe exactamente).
**Repositorio remoto:** `https://github.com/Developer-vic1/Refugio.git`.
**Estado remoto observado al redactar este prompt:** rama `main` con `.gitignore`; NO se había creado `develop`. Vuelve a consultar GitHub y NO supongas que sigue igual.
**Tecnologías existentes:** PHP 8.2+, MySQL en Laragon, patrón MVC, PDO, HTML, CSS/Tailwind 4, JavaScript sin necesidad de SPA; `public/index.php` como front controller; `resources/css/app.css` y `resources/js/app.js` como entradas centrales; `storage/` fuera de la raíz pública.
**Base de datos:** `refugio`, 22 tablas y 3 vistas previstas. NO la recrees, no la borres, ni cambies esquema o datos sin una migración revisada y aprobación.
**Accesos:** solo un tipo de rol autenticado (administrador) con varias cuentas; usuarios públicos sin registro obligatorio.
**Identidad de marca:** verde bosque `#17463D`, crema `#FFF9F2`, salvia `#C5D9C5`, miel `#F3B95D`, terracota `#A94935`, texto `#222F2A`; tipografía editorial Fraunces y de interfaz Manrope. Mantener identidad, pero rediseñar distribución y componentes. Las paletas alternativas solo mediante tokens del sistema.
**Inspiración visual:** `https://dogcat.com.ua/en` por su carácter emotivo, fotografías y narración dinámica. Inspirarse en principios, no copiar código, marca o composición exacta.

**Objetivo inmediato obligatorio:** publicar el código actual local como línea base de **V1** en la rama remota `develop` con un commit EXACTAMENTE llamado **`Version 1 Refugio Oficial`**. No corregir ni rediseñar nada dentro de ese commit. Después implementar la V2 mediante ramas `feature/`, corregir defectos mediante `fix/`, integrar por PR en `develop` y NO tocar `main` hasta que el usuario lo apruebe explícitamente.

## 1. Primera tarea: inventario real y preparación Git SEGURA
1. Examina el repositorio local: `pwd`, `git status --short`, `git branch -avv`, `git remote -v`, `git log --oneline --decorate -10`, `git fetch origin --prune` cuando el remoto esté configurado. Lista archivos y rutas reales. Averigua si ya es repositorio Git, si hay cambios sin confirmar y si `origin` apunta al remoto correcto.
2. Consulta el estado remoto de `main`, `develop` y cualquier rama existente. Si `develop` existe, NO la vuelvas a crear ni sobrescribas su historia. Compara local/remoto; conserva todos los commits ajenos.
3. Si el directorio local no es un repositorio, incorpora Git sin destruir ni sobreescribir los archivos locales. Como remoto `main` puede contener un `.gitignore`, usa una reconciliación segura; si hay conflicto de archivos sin seguimiento, prefiere clonar el repositorio en un directorio hermano y copiar SOLO código permitido de la instalación local (manteniendo respaldos). No ejecutes `git init` + `git checkout -f` ni otros pasos destructivos sobre archivos existentes.
4. Audita `.gitignore` y lo que se va a subir: excluir `.env`, credenciales, claves, sesiones, archivos privados, logs, cargas personales, dumps SQL, respaldos y `node_modules`. No subas cuentas de usuarios, hashes volcados desde la BD, teléfonos, direcciones personales ni comprobantes. Conserva `.env.example`, código fuente y assets públicos legítimos. Usa `git status --short`, `git diff --cached --name-only` y `git diff --cached --check` antes de confirmar.
5. Asegura que `main` permanezca intacta. Crea `develop` a partir de la base remota correcta (o comprueba la existente) y agrega el proyecto local sin perder historial. No hagas `git push --force`, `reset --hard`, `clean -fd`, `rebase` sobre ramas compartidas ni sobrescribas modificaciones sin consentimiento.
6. Ejecuta **solo una comprobación básica de línea base**: sintaxis PHP, compilación `npm run build` si están las dependencias, y una ruta pública si el servidor está disponible. Si falla, informa y documenta; no conviertas la línea base en un rediseño para “arreglar” la V1 antes de versionarla. Si hay secretos o archivos peligrosos, exclúyelos y registra el motivo.
7. Crea un commit que incluya el proyecto actual con el mensaje textual EXACTO: `Version 1 Refugio Oficial`. Este commit debe estar en `develop`. Haz `git push -u origin develop` cuando la rama sea correcta. Si resulta imposible por conflictos, permisos o archivos no autorizados, detente en este hito con un diagnóstico y comandos seguros; no simules el push ni crees un commit vacío para fingir que se incorporó la aplicación.
8. Reporta: rama actual, SHA del commit, listado de archivos incorporados, archivos excluidos y URL de la rama remota. En este punto NO modifiques `main` y NO publiques cambios de rediseño.

Comandos orientativos (ADÁPTALOS al estado real, no los ejecutes ciegamente):
```powershell
git status --short
git remote -v
git fetch origin --prune
git branch -avv
# Crear o situarse en develop según la situación real
git switch -c develop origin/main
# Revisar qué archivos se incorporarán (no hacer git add indiscriminado con secretos)
git add app routes resources public bootstrap.php package.json package-lock.json scripts docs README.md .env.example .gitignore
# Ajustar la lista a los archivos realmente existentes, inspeccionar staging
git diff --cached --name-only
git diff --cached --check
git commit -m "Version 1 Refugio Oficial"
git push -u origin develop
```
Si algunos paths no existen, no consideres eso un error fatal: usa rutas reales. Si local ya está versionado, NO asumas `origin/main` como ancestro sin compararlo.

## 2. Estrategia GitFlow obligatoria
- `main`: únicamente versiones estables; protegida, sin push directo. **Prohibido modificar o fusionar aquí sin autorización explícita del usuario.**
- `develop`: rama permanente de integración; todos los módulos aprobados llegan aquí.
- `feature/<modulo-o-tarea>`: se crea desde `develop` actualizado. Un módulo o tarea cohesiva por rama.
- `fix/<incidencia>`: un defecto encontrado en una `feature` se corrige primero en la propia feature o mediante rama `fix/...` basada en esa feature y se integra de nuevo a ella; si el defecto ya está en `develop`, la rama `fix/...` parte de `develop` y regresa a `develop` mediante PR.
- `hotfix/<incidencia>`: usar SOLAMENTE para incidencias urgentes de una versión que ya esté publicada en `main`; nace de `main` y, tras autorización, se integra a `main` y se propaga también a `develop`.
- `release/vX.Y.Z`: preparar candidato de versión desde `develop`; no publicar en `main` hasta aprobación explícita.

**Flujo por módulo:** actualizar `develop` con `git fetch origin` + `git pull --ff-only origin develop` cuando corresponda → crear feature → implementar módulo → probar UNA VEZ al finalizar el módulo (con repetición solo si falla o hay cambios después del fallo) → revisar diff → commits de alcance claro → `git push -u origin feature/...` → crear PR hacia `develop` → entregar al usuario capturas/evidencia y descripción → ESPERAR validación antes de fusionar. Después de aprobación, merge del PR a `develop`, `git switch develop`, `git pull --ff-only origin develop`, y partir desde ahí para la siguiente feature.

**Commits:** la línea base usa EXACTAMENTE `Version 1 Refugio Oficial`; los siguientes preferentemente `feat(ui): ...`, `feat(admin): ...`, `fix(media): ...`, `refactor(core): ...`, `style(public): ...`, `docs: ...`, `test: ...`. Commits atómicos que describan cambios verificables. No mezcles módulos inconexos. No uses `git push --force`.

**Protección sugerida en GitHub:** para `main` exigir PR y restringir borrado/force push; para `develop` exigir PR e inspección de cambios, considerando las opciones disponibles en el repositorio. No habilites reglas que impidan el commit de línea base sin revisar antes la situación. No cambies el branch default sin consulta.

## 3. Auditoría V1.1: defectos y brechas que DEBES verificar y resolver
Audita directamente cada fichero citado; son hallazgos del ZIP V1.1 y el código local podría haber cambiado:

### 3.1 Arquitectura y mantenibilidad
- `app/Views/layouts/main.php`: un solo layout mezcla sitio público, login, admin, menú, footer y notificaciones; extraer layouts y componentes reutilizables.
- Menú admin con ~14 enlaces planos, iconos hechos con caracteres Unicode y sin grupos/submenús/contadores ni señalización fiable de subrutas.
- `app/Controllers/PublicController.php`: `/nosotros`, `/adopciones`, `/donaciones` y `/privacidad` comparten `app/Views/pages/text.php`, con experiencia genérica; crear vistas y composiciones específicas.
- `resources/css/app.css`: demasiado monolítico y compacto. Centralizar tokens aquí como punto de entrada, pero organizar internamente `tokens/`, `base/`, `components/`, `layouts/`, `animations/`, `pages/`; **no dispersar colores HEX por las páginas PHP**.
- `resources/js/app.js`: centralizar bootstrapping aquí e importar módulos `core/`, `components/`, `interactions/`, `animations/`, `pages/`. No duplicar reglas ni tener eventos globales incontrolados.
- Verificar que el build Tailwind v4 realmente detecte y compile clases utilizadas; preservar `resources/css/tailwind-input.css` y el proceso `npm run build`. No asumir que la salida CSS es correcta solo porque npm devuelve exit code 0.
- Las rutas públicas y admin están en `routes/web.php` y el acceso pasa por `public/index.php`. Organizar `routes/web.php` y opcionalmente `routes/admin.php` sin romper URLs. No acceder directamente a controladores ni saltar middleware.
- El proyecto debe seguir funcionando desde Laragon, `http://refugio.test`, con Apache `DocumentRoot .../public`, nunca apuntando a la raíz.

### 3.2 Diseño UI/UX e iconos
- La experiencia actual es técnica, repetitiva y demasiado uniforme (tarjetas iguales, títulos y formularios similares). Proponer jerarquía visual y diseños diferentes por ruta sin romper identidad.
- Sustituir signos Unicode/emoji como iconos del admin por iconografía SVG consistente, por ejemplo Lucide versionado localmente, usando un componente/helper común; tamaños y stroke coherentes. Revisar contraste, estados hover/focus/active/disabled y `aria-hidden`/etiquetas accesibles.
- Rediseñar cabecera, menú móvil, breadcrumbs, estado activo por subruta, buscador y navegación del administrador.
- Construir una biblioteca de componentes: `AppShell`, `PublicHeader`, `AdminSidebar`, `Breadcrumbs`, `Button`, `Badge`, `AnimalCard`, `CampaignCard`, `EmptyState`, `Skeleton`, `Alert`, `Toast`, `ConfirmDialog`, `Drawer`, `Tabs`, `FormField`, `Progress`, `DataTable`, `Pagination`, `MediaGallery`, `UploadZone`, `ThemePreview`.
- Formularios con etiquetas reales, descripciones, errores por campo, ayuda contextual, contador de caracteres, progreso para formularios largos, estados de envío, bloqueo de doble envío y recuperación de entradas no guardadas.
- No inventar estadísticas, campañas financiadas, adopciones ni testimonios públicos. Las cifras visibles deben calcularse de datos verificados y autorizados.

### 3.3 Motion design, cursores y “hyperframes”
- `resources/js/app.js` implementa IntersectionObserver, barra de scroll, glow, tilt y algunas transiciones, pero no un sistema completo de animación.
- `.pointer-glow` no es un cursor contextual; crear un sistema opcional de estados (normal, enlace, click, zoom, drag), con fallback a cursor nativo; desactivarlo en dispositivos táctiles, en campos de texto y con `prefers-reduced-motion`.
- Los elementos con clase `.magnetic` deben tener comportamiento real o eliminar la clase. No usar efectos simulados sin funcionamiento.
- Implementar un motor de secuencias de animación **propio y liviano**, denominado internamente `Hyperframes` si se desea: timeline para hero, texto, fotografías, capas, scroll y transiciones; no lo presentes como una librería ya instalada. Usar Web Animations API/CSS/IntersectionObserver y View Transitions cuando estén disponibles, con progressive enhancement.
- Motion tokens en un solo lugar: duraciones, easings, stagger, intensidades (serena, equilibrada, expresiva), parallax moderado y exit animations.
- Animar `transform`/`opacity` preferentemente; no bloquear la interacción con overlays; sin jank ni scroll-jacking; accesibilidad de teclado, movimiento reducido y rendimiento en móvil.
- Diseñar transiciones entre rutas sin convertir el backend en SPA y sin romper navegación, formularios, historial o recarga.

### 3.4 Alertas, confirmaciones y estados
- `app/Views/layouts/main.php`: toast global siempre usa un símbolo de éxito `✓`, incluso en errores; crear tipado `success|error|warning|info|progress` con semántica ARIA correcta.
- `app/Views/admin/animal-form.php` y `resources/js/app.js`: confirmar la ubicación del atributo `data-confirm`; el JS busca un botón y accede a `button.form`, mientras el marcado anterior aplicaba la confirmación al formulario. Corregir para **impedir archivados sin confirmación**.
- Crear diálogo para archivar, rechazar, aprobar, cerrar campaña, descartar cambios, confirmar aporte (solo tras conciliación) y cerrar sesión donde proceda.
- Estado de carga, vacío, sin resultados, error de red, sin permisos, operación completada; validaciones visibles y mensajes claros en español.
- Distinguir errores de usuario de fallos del servidor; nunca revelar stacktrace, SQL o secretos en producción.

### 3.5 Datos públicos y contenido
- `ContentVisibility` y `.env`: mantener revisión local separada de publicación real. Mostrar registros generados SOLO en modo local y rotulados como pendientes de verificación; no habilitar `PUBLIC_CONTENT_ENABLED=true` globalmente para fingir que son reales.
- `AnimalRepository`: filtros/paginación SQL y listas, comprobar correspondencia de estados reales de la tabla. No confiar en filtrar solamente filas ya renderizadas.
- `app/Views/public/animal.php`: falta galería real de las fotografías de `animal_fotografias`, timeline y composición editorial detallada; mostrar estados de adopción y salud correctamente.
- `MediaController`/`MediaStorage`: revisar rutas `storage`, validación MIME/tamaño, autorización para leer fotos y documentos; ninguna subida debe ir directamente a `public/uploads` sin controles. Recursos ilustrativos del proyecto no deben confundirse con fotos verificadas.
- La página `app/Views/public/contact.php` reconoce no tener formulario funcional; diseñar el canal completo solo si existe backend persistente/correo compatible o plantear una migración revisable antes de implementarlo. Nunca simular un mensaje enviado.
- Historias, campañas y transparencia deben respetar `publicada`, `estado`, confirmación de pagos y autorizaciones; verificar vínculos y estados al entrar desde rutas externas.

### 3.6 Administración y procesos incompletos
- `app/Views/admin/dashboard.php`: pocos indicadores, sin pendientes prioritarios, agenda, solicitudes recientes, alertas de inventario ni tareas rápidas. Convertirlo en centro operativo, no una página de métricas decorativas.
- `app/Controllers/AdminController.php` / `AdminModuleController.php` / `app/Views/admin/listing.php`: revisar límites de 50 registros y listados de animales limitados a 100; búsqueda solo cliente sobre filas cargadas. Sustituir por consultas SQL con paginación, filtros persistentes y ordenamiento seguro.
- Faltan experiencias completas de gestión de adopciones formalizadas, seguimientos, inventario y movimientos, gastos, reportes y evidencias. No afirmar “CRUD completo” si faltan acciones.
- Editor de imágenes: añadir arrastrar y soltar, carga múltiple, previsualización, progreso, ordenar fotos, foto principal, texto alternativo y retiro controlado, sin romper las claves foráneas.
- `ThemeService` y `/admin/tema`: editor de paletas y niveles de movimiento con vista previa real escritorio/tablet/móvil, guardar/restaurar, contraste y autorización. Persistencia actual en `storage/app/private/visual-settings.json`; conservar salvo cambio justificado.
- Diez cuentas admin no significan diez roles: no inventar roles en DB. Si se requieren permisos granulares, documentar migración opcional y pedir aprobación antes de alterar esquema.
- Revisar reglas de negocio de cupos, estados de adopción, aportes pendientes/confirmados, saldos, documentos privados y registros auditados. Usar transacciones e idempotencia cuando proceda.

### 3.7 Seguridad y calidad
- Mantener PDO con consultas preparadas, CSRF, sesión segura, escape HTML, autorización por ruta y auditoría. Verificar login con hash almacenado `pbkdf2_sha256$...`, sin contraseñas en claro ni rehash SHA256 simple. No publicar la contraseña compartida anteriormente en código, logs o Git.
- Revisar `APP_DEBUG`, `.env`, CORS/CSP, headers, cookies SameSite/HttpOnly/Secure según HTTPS, regeneración de sesión y protección de fuerza bruta.
- Prevenir acceso horizontal a comprobantes privados, path traversal y subida de archivos activos o SVG no confiables; MIME y autorización servidor.
- Separar archivos persistentes de assets rastreables; no subir `storage/app/review` ni datos identificables a GitHub. Proveer placeholders de código/ilustraciones sin presentarlos como animales reales.
- Evitar dependencias excesivas, bloqueos de rendimiento, caracteres no accesibles y grandes librerías de movimiento innecesarias.

## 4. Hoja de ruta modular: ramas sugeridas y orden
Trabaja **una feature a la vez**, excepto cuando la independencia sea clara y no se produzcan conflictos:
1. `feature/ui-foundation`: tokens de diseño, iconos SVG, layouts, componentes, alertas y estados reutilizables.
2. `feature/admin-shell`: sidebar jerárquico, responsive, breadcrumbs, menús, topbar, acciones, dashboard orientado a pendientes.
3. `feature/motion-engine`: timeline Hyperframes, cursor contextual, animaciones accesibles, transiciones, microinteracciones.
4. `feature/public-home`: hero inmersivo, fotografía, storytelling, CTAs, navegación y footer responsivo.
5. `feature/public-animals`: búsqueda y filtros SQL, paginación, tarjetas, ficha, galería, estados y recursos visuales.
6. `feature/public-adoptions`: proceso, formularios por pasos, validaciones, envío, confirmación y privacidad.
7. `feature/public-donations-campaigns`: campañas, metas verificadas, donaciones económicas/materiales con estados honestos.
8. `feature/public-stories-volunteers`: historias editoriales, actividades, postulaciones; corregir `nosotros`, `contacto` y privacidad en subcommits claros si el alcance se vuelve grande.
9. `feature/admin-animals-media`: CRUD y gestor multimedia, controles, confirmación de archivo, auditoría.
10. `feature/admin-workflows`: evaluación de solicitudes, formalización de adopciones y seguimientos, cupos de voluntariado.
11. `feature/admin-finance-inventory`: aportes, conciliación, gastos, transparencia, inventario, evidencias, permisos de lectura.
12. `feature/admin-theme-settings`: editor de identidad visual, presets, previsualización y persistencia.
13. `feature/security-quality`: endurecimiento, accesibilidad, rendimiento, cierre de pendientes y documentación técnica.

Puedes dividir una feature demasiado grande en dos (`feature/public-stories` y `feature/public-volunteers`, por ejemplo), dejando constancia en el backlog. Cada PR debe ser revisable y no mezclar una reescritura de todo el proyecto.

## 5. Contratos de diseño y estructura de proyecto
Conserva MVC PHP y orienta las carpetas al dominio. Arquitectura objetivo (ajusta, sin mover todo por estética):
```text
Refugio/
  app/
    Controllers/{Public,Admin}/
    Models/
    Repositories/
    Services/
    Middleware/
    Validators/
    Views/
      layouts/{public,admin,auth}/
      components/{navigation,buttons,cards,forms,alerts,modals,media,tables}/
      public/{home,animals,adoptions,campaigns,stories,volunteers,transparency,contact}/
      admin/{dashboard,animals,adoptions,campaigns,donations,inventory,volunteers,stories,transparency,settings}/
  routes/{web,admin}.php
  resources/
    css/{app.css,tokens/,base/,components/,layouts/,animations/,pages/,tailwind-input.css}
    js/{app.js,core/,components/,interactions/,animations/,modules/}
    images/
  public/{index.php,assets/}
  storage/{app/public/,app/private/,app/review/,logs/}
  docs/{architecture/,decisions/,modules/,progress/}
  tests/{Unit/,Integration/,Feature/}
  scripts/
  .env.example
  .gitignore
  package.json
  README.md
```
`resources/css/app.css` y `resources/js/app.js` siguen siendo las ENTRADAS CENTRALES; internamente pueden importar módulos. NO escribir un valor HEX específico ni lógica duplicada en cada vista. Aplicar componentes accesibles, `aria-*`, navegación por teclado, contraste suficiente y responsive.

## 6. Menú administrativo de referencia
**GENERAL:** Panel principal; Notificaciones y pendientes.
**GESTIÓN DEL REFUGIO:** Animales y fotografías; Adopciones y seguimientos; Historias y publicaciones.
**COLABORACIÓN Y RECURSOS:** Campañas; Aportes y donaciones; Inventario; Voluntariado.
**CONTROL Y REPORTES:** Transparencia y gastos; Auditoría.
**SISTEMA:** Configuración institucional; Diseño e identidad visual; Seguridad.

Debe haber jerarquía de menús, colapsado, estado activo de subrutas, etiquetas legibles, contadores respaldados por MySQL, búsqueda y acciones rápidas, soporte móvil y accesibilidad; iconos SVG coherentes. No crear rutas ficticias que terminen en una página vacía sin explicación.

## 7. Navegación pública y patrones visuales
Mantener las rutas existentes y orientar cada sección a una composición diferente:
- `/`: hero fotográfico cálido con timeline de animación, animales destacados REALES autorizados, tres caminos Adoptar/Donar/Voluntariado, transparencia y relatos.
- `/animales` y `/animales/{id}`: catálogo editorial filtrable, ficha fotográfica, galería interactiva y estados verificados.
- `/adopciones` y `/adopciones/solicitar/{id}`: proceso sencillo, requisitos, formulario guiado, errores por campo.
- `/campanas`, `/campanas/{id}`, `/campanas/{id}/aportar`, `/donaciones/materiales`: aportes responsables, estados de verificación, seguimiento de objetivos.
- `/historias` y `/historias/{id}`: composición revista + cronología visual.
- `/voluntariado`, `/voluntariado/{id}`, `/voluntariado/{id}/solicitar`: agenda, detalle y postulación.
- `/transparencia` y `/transparencia/informes/{id}`: informes, cifras conciliadas y evidencias autorizadas, sin datos inventados.
- `/nosotros`, `/contacto`, `/privacidad`: páginas verdaderas, con información útil y políticas aplicables.
- `/admin/login`: entrada privada discreta accesible desde la cabecera pública; login seguro y adaptable.

## 8. Visualización REAL en navegador
**Obligatorio al terminar cada feature que cambie interfaz**:
1. Detecta entorno: Laragon local, PHP, MySQL, puerto, virtual host `http://refugio.test` y `.env` SIN imprimir contraseñas.
2. Si hay navegador integrado de escritorio disponible, abre la app REAL, recorre interacciones, inspecciona estados y toma capturas. Si usas Codex CLI/IDE sin navegador integrado, verifica si existe un navegador automatizable ya permitido (Playwright/Chromium, por ejemplo). Si no existe, NO afirmes haber visto la interfaz: informa la limitación y entrega URL + pasos manuales, y solicita capturas al usuario.
3. Verifica 3 tamaños representativos: escritorio ~1440 px, tablet ~768 px, móvil ~390 px. Asegura que no haya desplazamiento horizontal, menús ilegibles ni botones fuera de pantalla.
4. Comprueba focus visible, teclado, `prefers-reduced-motion`, estados hover, modales, alertas, carga de imágenes y error/éxito.
5. Capturas ANTES/DESPUÉS y resumen de cambios por módulo sin exponer registros personales. Proporciona al usuario la evidencia junto con la URL de PR.
6. No dependas de una maqueta para declarar una función terminada; registra resultados de interacción contra app local y rutas reales.

## 9. Política de pruebas: una revisión completa AL FINAL de cada módulo
No ejecutes baterías extensas después de cada cambio menor. Trabaja y luego ejecuta un único ciclo de verificación de la feature terminada, repitiendo solo las pruebas afectadas por correcciones:
- `php -l` para los PHP cambiados; `npm run build` para assets; validación JS si se modifica.
- Smoke de rutas y un flujo principal del módulo: inicio, navegación, envío/CRUD si procede, casos de error necesarios.
- Una verificación de seguridad focalizada si cambia auth, dinero, datos privados o uploads.
- Vista real en los 3 breakpoints cuando hay UI.
- Comprobar MySQL sin modificar datos de producción. Usar entorno local y registros autorizados; no eliminar información.
- Corregir errores y documentar evidencia. No decir “sin errores” sin haber ejecutado los checks y haber revisado resultados.
- No testear 22 tablas completas cada vez; al final de V2 sí realizar revisión integrada entre módulos.

## 10. Criterios de aceptación para CADA feature
La rama puede solicitar revisión hacia `develop` solo si:
- URLs y operaciones del módulo funcionan en Laragon.
- No rompe controladores, rutas o campos existentes.
- Usa tokens globales, SVG coherentes, componentes reutilizables, accesibilidad y responsive.
- Todas las alertas de ese módulo son descriptivas y las acciones peligrosas piden confirmación.
- Nunca revela PII, contraseñas, comprobantes ni datos sintéticos como registros verificados.
- Cambios registrados con commits pequeños, build y pruebas focalizadas finalizadas, capturas cuando correspondan.
- README/docs actualizados solo sobre comportamiento REAL.

## 11. Informe de entrega que debe producir Codex por módulo
Al terminar cada módulo, presentar SIEMPRE:
```text
MÓDULO: ...
RAMA: feature/...
COMMITS: SHA corto + mensaje de cada commit
ARCHIVOS CAMBIADOS: ...
RUTAS AFECTADAS: ...
QUÉ FUNCIONA: ...
QUÉ NO ESTÁ TERMINADO: ...
CHECKS EJECUTADOS (UNA VEZ AL CIERRE): ...
EVIDENCIA VISUAL: URL local y capturas / limitación honesta
PR HACIA: develop
ENLACE DEL PR: ... (solo si realmente fue creado)
ESTADO: pendiente de validación del usuario / aprobado e integrado
SIGUIENTE MÓDULO: ...
```
No inventar SHA, PR, capturas, logs ni resultados. Esperar aprobación antes de fusionar features a `develop`, salvo que el usuario ordene explícitamente un flujo automático.

## 12. Reglas de seguridad de Git y entregas
- No subir `.env`, certificados, SQL con datos reales, archivos de `storage` privados, PII ni capturas confidenciales al repositorio público.
- NO subir `node_modules` ni `vendor`; mantener lockfiles y documentación de instalación.
- No `push --force` ni modificar `main` sin autorización. No crear `release` o publicar `main` anticipadamente.
- Al resolver conflictos, explicar qué archivos se tocaron y conservar los cambios aceptados previamente.
- Hacer `git fetch` y `git pull --ff-only` de la rama base antes de iniciar la siguiente feature.
- Si una acción requiere autenticación de GitHub o permisos del navegador, solicitar el acceso por el mecanismo normal; nunca pedir tokens en el chat ni registrarlos.
- `.gitignore` ya existe remotamente; confirmar que cumple la política y adaptarlo sin hacer desaparecer archivos que necesitan ir a Git.

## 13. Ejecución: orden desde ESTE MOMENTO
**PRIMER HITO, ejecuta AHORA:**
1. Auditar el estado local + remoto.
2. Proteger datos privados y reconciliar Git.
3. Crear/actualizar `develop` sin tocar `main`.
4. Incorporar el código V1 actual.
5. Commit EXACTO: `Version 1 Refugio Oficial`.
6. Push real a `origin/develop`.
7. Comunicar SHA/URL y estado; si bloqueado, explicar la causa concreta y dejar comandos seguros para resolver.

**SEGUNDO HITO:** iniciar `feature/ui-foundation` desde el `develop` actualizado; diseñar componentes/tokens/iconos/alertas. Completar implementación, un ciclo final de pruebas, revisión en navegador y PR hacia `develop`. Mostrarlo al usuario para aprobación antes del merge.

**DESPUÉS:** ejecutar el resto de las features en el orden del apartado 4, una por vez. Cada vez que surja un error, gestionarlo con ramas `fix/...` según el origen, regresar al flujo y mantener la historia limpia. NUNCA dar por publicado `main` sin autorización expresa.

**Principio rector:** funcionalidad verificable + diseño cálido y excepcional + arquitectura sencilla de mantener + GitFlow trazable. Prioriza una mejora terminada y revisable por módulo sobre una reescritura masiva sin pruebas.
