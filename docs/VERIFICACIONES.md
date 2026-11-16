# Comprobaciones locales ejecutadas

- Sintaxis de 57 archivos PHP: OK.
- Sintaxis de JavaScript: OK.
- Coincidencia entre recursos fuente y compilados base: OK.
- Configuración: no se incluye .env con credenciales.
- Imágenes hero WEBP: presentes.
- Pruebas HTTP (sin conectarse a MySQL):

  - /: HTTP 200
  - /animales: HTTP 200
  - /campanas: HTTP 200
  - /adopciones: HTTP 200
  - /donaciones: HTTP 200
  - /historias: HTTP 200
  - /voluntariado: HTTP 200
  - /transparencia: HTTP 200
  - /admin/login: HTTP 200
  - /admin: HTTP 303
  - /admin/tema: HTTP 303
  - /admin/animales/nuevo: HTTP 303
  - /assets/img/hero-rescate.webp: HTTP 200
  - /esto-no-existe: HTTP 404

Las transacciones y consultas PDO contra tu base MySQL de Laragon NO se probaron en este entorno; deberán comprobarse después de configurar .env. Tailwind CSS v4 se compila en Windows mediante `npm install` y `npm run build`.
