# Seguridad, contenido y puesta en servicio

## Configuración de desarrollo

Nunca apuntar Apache al directorio raíz del proyecto; usar `public/` como DocumentRoot. El `.env` debe permanecer privado y fuera de control de versiones. No colocar comprobantes bancarios o datos de identidad en `public/` o `storage/app/public/`.

## Controles disponibles

- PDO con parámetros y listas blancas de columnas cuando se requieren identificadores SQL.
- Sesiones HttpOnly, SameSite=Lax y renovación de ID al autenticarse.
- Verificación CSRF en todas las rutas POST.
- Protección básica contra intentos repetidos de acceso.
- Escapado de HTML en plantillas.
- Validación de imágenes por MIME, tamaño, dimensión y nombre aleatorio.
- Imágenes almacenadas fuera de la raíz pública y servidas por un controlador.
- Revisión de estados y confirmación explícita para campañas e historias.
- No confirma aportes por el simple hecho de recibir una solicitud.
- Cabeceras CSP, X-Frame-Options, Referrer-Policy, Permissions-Policy, nosniff.
- Preferencia `prefers-reduced-motion` incluso si el tema seleccionado es expresivo.

## Antes de producción

1. Confirmar expedientes, autorizaciones de fotografías y datos institucionales reales.
2. Cambiar credenciales débiles y usar HTTPS.
3. Crear un usuario MySQL con permisos mínimos; no usar `root`.
4. Validar política de privacidad y períodos de retención.
5. Implementar antispam (incluyendo limitación de tasa servidor) antes de habilitar formularios.
6. Realizar pruebas contra el MySQL real de Laragon, sobre todo FK, transacciones y publicación.
7. Completar formalización de adopciones, inventario, gestión de gastos y evidencias.
8. Habilitar auditoría y copia de seguridad automática según las políticas de la institución.
9. Revisar permisos de distintos administradores. La base actual tiene cuentas, no una matriz de permisos.
10. Revisar `APP_DEBUG=false`, logs y TLS antes de publicar.

**Aviso:** el material generado anteriormente para desarrollo no es evidencia de rescates, donaciones ni campañas reales. Dejar `PUBLIC_CONTENT_ENABLED=false` hasta completar la verificación humana.
