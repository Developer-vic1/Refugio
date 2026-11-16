# Refugio Vida Animal — versión inicial para Laragon

Sistema web con **PHP 8.2+**, **MySQL 8/MariaDB**, **MVC modular**, **HTML**, **JavaScript** y **Tailwind CSS v4**. Incluye un diseño público editorial con fotografía protagonista, animaciones, adaptación móvil y un panel de administración con selector global de identidad visual.

## 1. Instalación en Laragon (Windows)

1. Extrae todo el proyecto en `C:\laragon\www\refugio`.
2. En MySQL Workbench comprueba que exista la base **`refugio` con sus 22 tablas y 3 vistas**. El proyecto no crea tablas ni modifica su esquema.
3. Copia `.env.example` a `.env` y ajusta `DB_USER`, `DB_PASSWORD`, `DB_HOST`, `DB_PORT` y `DB_NAME`. El usuario `root` con contraseña vacía solo es un valor inicial frecuente en **desarrollo local**; para producción utiliza una cuenta MySQL de privilegios mínimos.
4. **Configura Apache para que el DocumentRoot del sitio sea `C:/laragon/www/refugio/public`.** Hay un ejemplo en `laragon/refugio.test.conf`. Si Laragon genera `auto.refugio.test.conf`, sustituye ese sitio automático por uno manual, no mantengas dos VirtualHosts con el mismo nombre. Reinicia o recarga Apache y comprueba `http://refugio.test`.
5. En la terminal de Laragon, desde la raíz del proyecto, ejecuta `npm install` y `npm run build` para compilar Tailwind. Las hojas CSS y JS centrales ya están copiadas en `public/assets`, por lo que la apariencia base funciona incluso antes de ejecutar npm.
6. Verifica `http://refugio.test/`, `http://refugio.test/animales` y `http://refugio.test/admin/login`.

> Nunca configures el DocumentRoot hacia la **raíz** del proyecto: allí está `.env`, el código PHP y los archivos privados. Laragon ofrece hosts `.test` automáticos, pero para este proyecto el host debe apuntar expresamente a `public/`.

### Configuración recomendada de PHP

Comprueba que estén habilitadas las extensiones `pdo_mysql`, `fileinfo` y `openssl` (y, de ser posible, `mbstring`). Para imágenes de hasta 5 MB configura `upload_max_filesize=6M` y `post_max_size=8M` o valores superiores. Para errores de permisos en imágenes, comprueba escritura sobre `storage/app/public/animales`, `storage/app/public/historias` y `storage/app/private`.

## 2. Tema, CSS y animaciones

**Editar únicamente las fuentes centrales:**

- `resources/css/app.css`: paleta, sombras, tipografía, tamaños, responsive, componentes y efectos.
- `resources/js/app.js`: menú, revelado por scroll, progreso de lectura, cursor ambiental, tarjetas con inclinación, previsualización de imágenes y confirmaciones accesibles.
- `resources/css/tailwind-input.css`: fuente de Tailwind v4 (procesa las clases de vistas PHP y JavaScript).

Tras cambiar las fuentes ejecuta `npm run build` o `npm run assets:sync` si solo editaste CSS/JS personalizados. Evita editar `public/assets/css/app.css` y `public/assets/js/app.js`, ya que son copias generadas.

**El administrador puede cambiar entre tres identidades** en `/admin/tema`: Bosque y Hogar, Atardecer Solidario y Lila Esperanza, y seleccionar movimiento sereno, equilibrado o expresivo. Se guarda en `storage/app/private/visual-settings.json`, sin modificar las 22 tablas. Siempre se respeta `prefers-reduced-motion`.

## 3. Acceso y publicación

- Sitio público: `/`.
- Panel: `/admin/login` y `/admin`.
- Contraseñas: lectura de los hashes PBKDF2-HMAC-SHA256 con el formato `pbkdf2_sha256$...` de `administradores.contrasena_hash`. **El proyecto no incluye contraseñas en texto plano ni crea cuentas.** Usa las cuentas que ya incorporaste en MySQL; cambia las contraseñas iniciales débiles antes de publicar.
- `PUBLIC_CONTENT_ENABLED=false` evita mostrar como hechos reales los registros generados anteriormente para desarrollo.
- `PUBLIC_FORMS_ENABLED=false` bloquea la recepción pública de formularios hasta terminar la revisión de privacidad, antispam y flujo de atención.
- Las campañas solo aparecen si están activas; las historias deben tener `publicada=1`; los aportes quedan `pendiente` hasta su conciliación.

Los recursos `hero-rescate.webp` y `hero-compania.webp` son **fotografías ilustrativas generadas para la interfaz**. No deben presentarse como expedientes o fotografías auténticas de animales del refugio.

## 4. Subida de fotografías y almacenamiento

Las fotografías que incorpora el administrador se guardan en:

- `storage/app/public/animales/`
- `storage/app/public/historias/`

**No** se suben directamente a `public/`. El frontend recibe rutas `/media/{categoria}/{archivo}` servidas por `MediaController`; antes de entregar una imagen se comprueba la sesión administrativa o su disponibilidad pública real. Se admiten JPG/PNG/WEBP de hasta 5 MB, con validación MIME, dimensiones y nombres aleatorios de 128 bits. Los comprobantes sensibles deben mantenerse en `storage/app/private/`, con controlador autenticado específico cuando implementemos esa fase.

## 5. Qué funciona ya en esta versión

- Front Controller `public/index.php`, Router por método HTTP y rutas limpias; vistas PHP y layouts diferenciados.
- Portada responsiva, CTA, catálogo y ficha pública, historias, campañas, voluntariado, transparencia y formularios principales.
- Autenticación administrativa, limitación básica de intentos, sesiones seguras y cierre de sesión.
- Dashboard administrativo, listados y búsqueda visual por tabla.
- Registro, edición, archivado y subida de fotos de animales.
- Registro, edición, verificación previa a publicación y fotografías de historias.
- Registro y edición de campañas; activación tras una confirmación administrativa explícita.
- Alta de actividades de voluntariado, revisión de postulaciones con bloqueo y control de cupos.
- Gestión de estados de solicitudes de adopción (no formaliza la entrega), y confirmación o rechazo de aportes tras marcar conciliación.
- Configuración institucional, de contacto y bancos; selección de tema global; auditoría básica de operaciones.
- Protección CSRF para POST, PDO con parámetros, escape de salida HTML, sesiones HttpOnly, validaciones y cabeceras HTTP de seguridad.

## 6. Pendiente antes de considerar el sistema final o publicarlo

Esta es una **primera versión integrada**, no un sistema listo para producción. Falta completar y validar en Laragon: formalización y seguimiento de adopciones; kardex de inventario con comprobantes; alta, revisión y conciliación de gastos; informes financieros publicables; archivos y evidencias privadas; permisos granulares para las diez cuentas; sistema de notificaciones por correo; formulario de contacto con persistencia; anti-spam y controles perimetrales; pruebas automatizadas de integración y aceptación; copias de seguridad, privacidad institucional y revisión de datos reales.

El código no puede comprobarse contra la MySQL de tu equipo desde este entorno. La verificación real de altas, claves foráneas, medios y transacciones debe hacerse localmente antes de habilitar el acceso público.

## 7. Comprobaciones rápidas

- `php -l public/index.php` valida la sintaxis de un archivo.
- `php -S 127.0.0.1:8000 -t public public/dev-router.php` ejecuta una prueba de desarrollo alternativa a Laragon.
- `powershell -ExecutionPolicy Bypass -File scripts/comprobar-rutas.ps1 -BaseUrl http://refugio.test` revisa las rutas HTTP esenciales.
- `npm run build` compila Tailwind y sincroniza los recursos centrales.

Consulta además `docs/RUTAS_Y_MODULOS.md` y `docs/SEGURIDAD_Y_PUBLICACION.md`.
