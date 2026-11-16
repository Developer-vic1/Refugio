# Inicio e identidad institucional

Rama: `feature/public-home`. Incluye inicio, identidad y páginas institucionales solicitadas durante la revisión: Nosotros, Contacto y Privacidad. Los módulos de adopción, campañas, voluntariado y catálogo mantienen sus ramas posteriores.

- Nombre de trabajo elegido: Huella Serena. Configuración pública en config/site.php y lectura mediante SiteIdentity, sin cambiar datos institucionales existentes en MySQL. La búsqueda exploratoria detectó otros usos de Lazos de Vida y Nido de Huellas; se eligió una expresión que comunica cuidado y calma. No se afirma exclusividad o registro de marca.
- Teléfono facilitado por el usuario: +59175859142. Enlaces tel y WhatsApp reales, sin envío automático. Contacto usa este canal y permite copiar el número. No se inventaron dirección, correo, horarios ni instalaciones.
- Inicio con el fondo completo de perro y gato facilitado por el usuario, caminos de participación, relato del comienzo, preparación para adopción e interacción de gestos de cuidado. El selector de gestos modifica contenido y enlace; no simula una donación o resultado real. Profundidad y transición 3D moderadas respetan movimiento reducido.
- Nosotros tiene relato original de una iniciativa en formación, propósito, visión, valores y próximos pasos. No se atribuyen rescates, cifras, testimonios o resultados sin verificar.
- Contacto y Privacidad tienen vistas específicas. Privacidad describe el funcionamiento de sesión, formularios, preferencias y contacto externo sin declarar cumplimiento legal ni plazos de conservación inexistentes.
- Siete assets originales optimizados para sus secciones; prompts y rutas en IMAGENES_PUBLICAS.md. Los PNG originales permanecen locales. El hero conserva la composición facilitada y no muestra rótulos sobre la fotografía. No se sustituyen fotografías de expedientes por ilustraciones sin rotularlas.
- MVC, Laragon, rutas y esquema conservados. No se cambiaron .env ni flags de publicación.

La gestión de la identidad desde administración se conectará en theme-settings. El contenido público de BD sigue sujeto al control de visibilidad; la revisión local rotulada y los filtros se completan en public-animals.

Fuentes de la exploración de nombre: [Lazos de Vida](https://www.sacamedelaviolencia.com/) y [Nido de Huellas](https://nidodehuellas.cl/). El texto institucional de este proyecto es original.

## Correcciones de revisión

Hero inmersivo con contraste gradual, encuadre móvil, profundidad al pasar el puntero y entradas Hyperframes. Se retiraron las preferencias visibles del pie y la nota técnica del copyright. El cursor público ahora es una patita, con saludo al pasar sobre enlaces y un salto breve al pulsar; conserva cursor nativo en campos, diálogos, tacto y movimiento reducido del sistema. No hay rótulos visibles de imagen ilustrativa en estas páginas.

## Verificación

Build de assets correcto; sintaxis PHP modificada y JavaScript correctos; 35 comprobaciones de UI y verificación de consultas del panel aprobadas. Inicio, Nosotros, Contacto y Privacidad revisados a 390, 768 y 1440 px en el navegador integrado: sin desbordamiento horizontal ni imágenes cargadas rotas. Copiado del teléfono y cambios de gesto de cuidado comprobados. Patita visible en enlaces, dos huellas breves al pulsar, cursor de texto nativo en campos y ausencia de patita con movimiento reducido. Evidencia visual conservada localmente fuera de Git.
