# Arquitectura MVC y rutas

## Cadena de solicitud

`Browser -> Apache Laragon -> public/index.php -> routes/web.php -> Router -> Controller -> Service/Repository -> PDO/MySQL -> Vista PHP -> Tailwind + recursos CSS/JS`.

## Carpetas

```
app/
  Core/              Router, Auth, Database y Repository base
  Controllers/       HTTP, validación de flujo y selección de vistas
  Repositories/      Consultas SQL con PDO
  Services/          Reglas de negocio, carga y temas
  Views/             Plantillas públicas y administrativas
routes/web.php       Tabla de rutas GET/POST
resources/css/       Fuente de estilos, variables, Tailwind
resources/js/        Interacciones y efectos globales
public/              Única raíz Apache, assets estáticos e index.php
storage/app/public/  Fotos subidas, servidas por MediaController
storage/app/private/ Ajustes visuales y futuros documentos confidenciales
storage/logs/        Registro local (fuera del público)
```

## Públicas

| Ruta | Función |
| --- | --- |
| `/` | Landing visual |
| `/nosotros` | Historia y misión |
| `/animales` y `/animales/{id}` | Catálogo y ficha |
| `/adopciones` | Requisitos |
| `/adopciones/solicitar/{id}` | Solicitud de adopción GET/POST |
| `/donaciones` | Formas de colaborar |
| `/campanas` y `/campanas/{id}` | Campañas activas |
| `/campanas/{id}/aportar` | Aporte pendiente, GET/POST |
| `/donaciones/materiales` | Ofrecimiento de bienes, GET/POST |
| `/voluntariado` y `/voluntariado/{id}` | Actividades abiertas |
| `/voluntariado/{id}/solicitar` | Postulación, GET/POST |
| `/historias` y `/historias/{id}` | Historias publicadas |
| `/transparencia` y `/transparencia/informes/{id}` | Informes publicados |
| `/contacto`, `/privacidad` | Información institucional |
| `/media/{bucket}/{filename}` | Fotografías desde storage con control de visibilidad |

## Privadas

| Ruta | Operaciones |
| --- | --- |
| `/admin/login` | GET/POST de autenticación |
| `/admin/logout` | POST de cierre |
| `/admin` | Dashboard |
| `/admin/animales` | Listado, alta `/nuevo`, edición `/{id}/editar`, archivo y fotos por POST |
| `/admin/historias` | Listado, alta `/nueva`, edición `/{id}/editar`, fotos |
| `/admin/campanas` | Listado, alta `/nueva`, edición `/{id}/editar` |
| `/admin/adopciones` | Solicitudes, revisión y decisión `/{id}/estado` por POST |
| `/admin/aportes` | Conciliación y estado `/{id}/estado` por POST |
| `/admin/voluntariado` | Actividades, alta `/nueva`, postulaciones `/{id}/estado` |
| `/admin/donaciones-materiales` | Listado |
| `/admin/inventario` | Listado |
| `/admin/gastos` | Listado |
| `/admin/transparencia` | Listado |
| `/admin/configuracion` | GET/POST configuración institucional |
| `/admin/tema` | GET/POST identidad visual y movimiento |
| `/admin/auditoria` | Listado de operaciones |

Todas las rutas privadas requieren una sesión válida. El controlador no debe escribir HTML fuera de vistas ni realizar consultas con parámetros concatenados.
