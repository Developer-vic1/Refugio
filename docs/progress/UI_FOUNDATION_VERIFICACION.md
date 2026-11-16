# Verificación de ui-foundation

Estado: implementación local verificada, pendiente de aprobación e integración.

## Comprobaciones ejecutadas al finalizar

- 33 archivos PHP del alcance inicial: sintaxis correcta. Tras la ampliación visual solicitada se repitió solamente la sintaxis de las tres vistas afectadas y del router de pruebas.
- 35 comprobaciones en `php scripts/check-ui.php`: escape HTML, rechazo de enlaces externos o activos, ARIA por tipo, estados de campo, no repoblar contraseñas, subrutas activas, compatibilidad flash y lista de componentes permitidos.
- 7 archivos JS/sincronizador iniciales con `node --check`; Hyperframes y la entrada JS se comprobaron al incorporarlos.
- `npm run build` correcto, repetido tras los cambios visuales solicitados. Tailwind compiló y contiene gap-4, utilizada por la cuadrícula del catálogo. Las clases retiradas de las vistas ya no se exigen en el CSS final. CSS propio se entrega con imports aplanados.
- HTTP 200: inicio, catálogo, campañas, historias y acceso. Administración sin sesión devuelve 303 al acceso; ruta inexistente devuelve 404. PNG original responde 200.
- MySQL consultada en modo lectura: 22 tablas base y 3 vistas. No se ejecutaron migraciones, escrituras de prueba ni cambios de configuración.
- `git diff --check`: correcto después de normalizar el final del CSS generado.

## Navegador integrado de ChatGPT

Se utilizó el navegador integrado para revisar la aplicación real en `http://127.0.0.1:8000`. Se cerró el navegador externo al recibir la preferencia del usuario.

- Catálogo: búsqueda GET con gato, conservación del campo, estado sin resultados y limpieza de filtros correctos.
- Acceso: etiquetas y acción mostrar/ocultar contraseña verificadas, sin enviar credenciales ni iniciar sesión.
- Menú: abrir/cerrar, Escape y devolución de foco verificados.
- Confirmación en fixture aislada sin datos: clic abre diálogo; Escape conserva cero envíos; Enter también exige confirmación; confirmar produce un único envío con submitter `archivar`. No se archivó ningún animal real.
- Centrado: diálogo de aproximadamente 440 × 268 CSS px en viewport 1821 × 1121, posición x≈691, y≈427, acorde al centro. Captura inspeccionada.
- Layout responsive medido en tres anchos efectivos: escritorio (clientWidth 1415), tablet (743) y móvil (374), que corresponden aproximadamente a objetivos 1440/768/390 descontando barra y zoom. ScrollWidth coincide con clientWidth en los tres.
- Escritorio: tres columnas de filtros. Tablet: dos columnas más acciones. Móvil: una columna; campos entre x≈37 y x≈337 dentro del ancho 374. Panel auxiliar oculto; contenido vacío en una columna.
- Movimiento reducido emulado: contenido con opacity 1 y transform none. Preferencia de emulación retirada al finalizar.
- Sin mensajes de error en la consola revisada de la página del catálogo.

## Evidencia y límites

Capturas locales fuera de Git: `output/playwright/catalogo-v2.jpg`, `modal-centrado.jpg` y referencia anterior `catalogo-antes.jpg`. El navegador integrado produjo duplicación de mosaicos en algunas capturas con viewport emulado; esas capturas no se usan como evidencia visual fiable. La revisión visual se apoyó en la vista normal integrada, una captura tablet válida y mediciones DOM de los tres anchos. Falta confirmar en un teléfono físico.

`http://refugio.test` no respondió en esta sesión (curl HTTP 000). Se conservó la configuración Laragon sin modificaciones. La aplicación sí se verificó con el servidor local 8000.

No se validaron operaciones administrativas autenticadas, registros privados ni los flujos de módulos futuros. El resto de la V2 continúa según el plan y requiere aprobación por módulo.
