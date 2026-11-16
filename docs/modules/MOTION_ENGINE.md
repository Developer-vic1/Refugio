# Hyperframes y movimiento accesible

Rama: `feature/motion-engine`.

- Hyperframes es código propio sobre Web Animations API e IntersectionObserver. `play(elements, options)` anima grupos y devuelve animaciones; `timeline(steps, scope)` coordina etapas con offsets, duración y efecto rise/scale/photo. La portada enlaza fotografía, etiqueta, titular, explicación, acciones y cierre; otros bloques entran al aparecer en pantalla.
- Tokens únicos para duración, stagger, distancia, easing e intensidades. La intensidad serena evita profundidad y magnetismo. El contenido permanece visible sin JS o sin la API. El foco de teclado finaliza animaciones que contengan el control enfocado.
- Escenas con perspectiva: `data-depth-scene` y `data-depth-layer`; movimiento máximo moderado, solo puntero fino. `data-magnetic` tiene comportamiento real limitado a tres píxeles en controles explícitos.
- Preferencias A tu ritmo en el pie: menos movimiento y cursor expresivo opcional. Se guardan solo en localStorage; la preferencia del dispositivo tiene prioridad. Reducir movimiento cancela secuencias activas, elimina profundidad, magnetismo y movimientos hover.
- Cursor contextual opcional: normal, enlace, click, zoom y drag. No aparece en controles de texto, comboboxes, diálogos, táctiles o movimiento reducido. Si se desactiva vuelve el cursor nativo. Los estados zoom/drag se usan únicamente cuando el módulo implementa esas acciones.
- Cross-document View Transitions mediante mejora progresiva, sin interceptar enlaces, formularios o historial. El sistema omite las transiciones cuando corresponde reducir movimiento. Los diálogos tienen entrada breve, sin overlays adicionales que bloqueen interacción.

No usa librería externa ni convierte MVC en SPA. No se afirma que Hyperframes sea un paquete instalado.

Referencias: [Web Animations API](https://developer.mozilla.org/en-US/docs/Web/API/Web_Animations_API), [View Transition API](https://developer.mozilla.org/en-US/docs/Web/API/View_Transition_API). Las APIs opcionales tienen fallback a contenido y navegación normales.

## Verificación ejecutada

Cinco PHP y seis JS del alcance sin errores de sintaxis; build correcto. Navegador integrado: menos movimiento actualiza data-user-motion, elimina transform de la fotografía y persiste tras recargar; cursor opcional muestra estado de enlace y vuelve al nativo en campos. La emulación prefers-reduced-motion reduce el movimiento, desactiva el control equivalente y oculta el cursor. Se retiró la emulación y se dejaron las preferencias opcionales desactivadas al finalizar. Consola revisada sin errores. Captura local fuera de Git: output/playwright/preferencias-movimiento.jpg.

No se probaron dispositivos táctiles físicos ni todas las combinaciones de navegador con View Transitions. Las mejoras no condicionan navegación o envío de formularios.
