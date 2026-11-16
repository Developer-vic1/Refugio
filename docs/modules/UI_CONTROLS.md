# Controles con identidad propia

Rama: `feature/ui-controls`. Amplía la biblioteca según la petición del usuario, antes de continuar con los módulos públicos.

- `component('select-field', props)`: label, name, id, value y opciones value/label/disabled. El select HTML permanece como fuente del valor enviado. JavaScript lo mejora con un combobox propio; sin JS conserva el control nativo. También mejora los select simples existentes, transformando etiquetas implícitas sin dejar dos controles dentro del mismo label.
- Selección: flechas, Home/End, PageUp/PageDown, búsqueda por caracteres sin acentos, Enter/Espacio/Tab para aceptar y Escape para cancelar. La lista ajusta su posición al espacio disponible. Opciones deshabilitadas no se eligen. Reset nativo sincroniza el estado visual. Se conserva validación required y se enfoca el control visible ante error.
- `component('date-field', props)`: conserva input date con validación min/max y envío ISO. Añade calendario modal propio en español, semana desde lunes, cambios por mes/año, días con foco móvil mediante flechas, Home/End y PageUp/PageDown; Enter elige y Escape cancela. La fecha elegida se anuncia al volver al botón. El campo sigue admitiendo edición por teclado y fallback nativo sin JS.
- `component('search-field', props)`: búsqueda con icono y borrado accesible. `component('reset-filters', props)`: acción Empezar de nuevo con icono y estados coherentes.
- Fuentes: PHP en components, JS en components y core/icons, CSS en controls-signature.css. Entradas y build conservados. No se añadió dependencia de widgets.
- Los valores se validan en servidor mediante los contratos existentes. Estos controles no autorizan operaciones ni habilitan publicación de datos.

Patrones de referencia: [W3C select-only combobox](https://www.w3.org/WAI/ARIA/apg/patterns/combobox/examples/combobox-select-only/) y [W3C date picker dialog](https://www.w3.org/WAI/ARIA/apg/patterns/dialog-modal/examples/datepicker-dialog/). Implementación propia; no se copió el ejemplo de producción. Falta validación con lectores de pantalla físicos; las verificaciones de navegador no sustituyen esa revisión.

La revisión aislada está en /ui-check del servidor de tests. El catálogo usa los componentes reales de búsqueda, especie y restablecimiento. Más filtros y opciones de MySQL se completan en public-animals.

## Verificación ejecutada

Sintaxis de ocho PHP y cinco JS correcta; 35 contratos base y build correctos. Tras los ajustes del selector se repitieron solo su sintaxis, sincronización y flujos afectados. En navegador integrado: g selecciona Gato, Enter acepta y cierra, Escape conserva la selección anterior; required enfoca el control visible y anuncia error, que desaparece al elegir. Se corrigió cierre prematuro al desplazar la página y activación duplicada del botón con teclado.

Calendario: flecha derecha del día 15 al 16, Enter genera ISO 2026-11-16 y devuelve foco; límite mínimo impide retroceder antes de enero 2026; Escape conserva la fecha. Borrado de búsqueda vacía el campo. El catálogo real envió GET ?q=&especie=Gato y mostró el valor elegido.

Captura móvil del calendario inspeccionada a 390px, deviceScaleFactor 1, modal centrado y botones dentro del ancho. Evidencia local fuera de Git: output/playwright/calendario-propio-movil.jpg. No se introdujeron operaciones de escritura en la BD.
