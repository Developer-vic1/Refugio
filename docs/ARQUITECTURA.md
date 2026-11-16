# Arquitectura de referencia: Refugio Vida Animal

## Visión general

**Front Controller + Router + MVC + Servicios + Repositorios (PDO)**. Una aplicación PHP con dos áreas y un único esquema MySQL `refugio` de 22 tablas. El público utiliza rutas de lectura y envío de solicitudes; solo las cuentas activas de `administradores` entran al panel privado.

## Fronteras de seguridad

- `public/`: ÚNICA carpeta que debe ver Apache/Nginx.
- `app/`, `routes/`, `.env`, `storage/`: fuera de DocumentRoot.
- CSRF comprobado globalmente para métodos POST.
- Sesiones administrativas con cookie HttpOnly y vencimiento por inactividad.
- Se validan los estados en el backend, no solo en el frontend.
- Las publicaciones y cifras públicas están deshabilitadas por defecto hasta que los datos del refugio sean verificados.

## Entradas (rutas públicas)

| GET | Controlador | Tabla(s) principales | Comportamiento |
|---|---|---|---|
| `/` | PublicController::home | animales | hero y animales autorizados |
| `/nosotros` | PublicController::about | refugio_configuracion | misión y presentación |
| `/animales` | PublicController::animals | animales, animal_fotografias | búsqueda y filtros |
| `/animales/{id}` | PublicController::animal | animales, animal_fotografias | ficha por ID |
| `/adopciones` | PublicController::adoptionInfo | — | requisitos |
| `/adopciones/solicitar/{id}` | FormController::adoption | animales | formulario |
| `/donaciones` | PublicController::donationInfo | — | alternativas de apoyo |
| `/campanas` | PublicController::campaigns | v_campanas_resumen | campañas activas |
| `/campanas/{id}` | PublicController::campaign | v_campanas_resumen | detalle publicado |
| `/campanas/{id}/aportar` | FormController::contribute | campanas | formulario de aporte |
| `/donaciones/materiales` | FormController::material | — | ofrecimiento material |
| `/voluntariado` | PublicController::volunteers | actividades_voluntariado | actividades abiertas futuras |
| `/voluntariado/{id}` | PublicController::activity | actividades_voluntariado | ficha de actividad |
| `/voluntariado/{id}/solicitar` | FormController::volunteer | actividades_voluntariado | postulación |
| `/historias` | PublicController::stories | historias, animales | publicadas |
| `/historias/{id}` | PublicController::story | historias | relato individual |
| `/transparencia` | PublicController::transparency | v_transparencia_general, reportes_transparencia | saldos verificados e informes |
| `/transparencia/informes/{id}` | PublicController::report | reportes_transparencia | informe publicado |
| `/contacto` | PublicController::contact | refugio_configuracion | información institucional |
| `/privacidad` | PublicController::privacy | — | texto por aprobar |

### Rutas de envío (POST)

- `POST /adopciones/solicitar/{id}` → `solicitudes_adopcion` con estado `pendiente`.
- `POST /voluntariado/{id}/solicitar` → `solicitudes_voluntariado` con estado `pendiente`.
- `POST /donaciones/materiales` → `donaciones_especie` con estado `solicitada`.
- `POST /campanas/{id}/aportar` → `aportes` con estado `pendiente`; NO efectúa cobro ni certifica pago.

### Rutas de administración

- `GET|POST /admin/login`, `POST /admin/logout`.
- `GET /admin` → indicadores de tablas existentes.
- Listados de consulta en `/admin/animales`, `/admin/adopciones`, `/admin/campanas`, `/admin/aportes`, `/admin/donaciones-materiales`, `/admin/inventario`, `/admin/historias`, `/admin/voluntariado`, `/admin/transparencia`, `/admin/gastos`, `/admin/configuracion`, `/admin/auditoria`.
- **Aún no implementadas:** altas/edición/archivo en admin, conciliación de pagos, aprobación de adopciones, publicaciones, gestión de fotografías, correos de notificación, exportaciones y motor de informes.

## Camino de datos: adopción

```
GET /animales/42
  -> Router -> PublicController::animal
  -> AnimalRepository::find
  -> Views/public/animal.php

POST /adopciones/solicitar/42
  -> CSRF -> FormController::adoptionSave
  -> Forms::adoption
  -> SELECT ... FOR UPDATE (revalidar disponibilidad)
  -> INSERT solicitudes_adopcion [pendiente]
  -> COMMIT -> redirect /confirmacion
```

## Camino de datos: aporte económico

```
GET /campanas/2
 -> verificar activa + donaciones_habilitadas
POST /campanas/2/aportar
 -> CSRF -> validar monto y medio
 -> INSERT aportes [pendiente]
 -> SIN sumar a la recaudación pública
 -> conciliación administrativa posterior (pendiente de desarrollo)
```

## Estrategia frontend

- Layout compartido `Views/layouts/main.php`.
- Tailwind v4 compilado en CSS estático: `resources/css/tailwind-input.css` → `public/assets/css/tailwind.css`.
- `public/assets/css/app.css` complementa el diseño editorial y sirve como base visible sin compilación.
- JavaScript Vanilla en `public/assets/js/app.js` para menú móvil y revelado por intersección.
- Respetar `prefers-reduced-motion`; no depender exclusivamente de animaciones para transmitir información.

## Siguiente fase

Prioridades recomendadas: 1) autorización/política real, 2) CRUD de animales/fotografías, 3) solicitudes administrativas con auditoría, 4) campañas y verificación de aportes, 5) voluntariado y control de cupos, 6) inventario y transparencia, 7) pruebas de aceptación y seguridad.
