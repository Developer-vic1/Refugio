# Inicio directo — Version 2

- Entrada: `Iniciar Refugio.cmd`, acceso de escritorio con icono o `php start`.
- `Crear acceso directo.cmd` genera accesos locales; los archivos `.lnk` no se versionan.
- PHP y MySQL existentes siguen siendo necesarios. No se abre la interfaz de Laragon.
- El estado y los registros permanecen en carpetas ignoradas. No se guardan credenciales en accesos ni en el perfil de arranque.
- El servidor escucha únicamente en loopback. La comprobación de identidad y base está en el router de desarrollo, no en las rutas de Apache. No expone carpetas ni credenciales.
- El iniciador nunca detiene procesos ajenos, inicializa bases ni instala otro MySQL. El perfil se obtiene de la instancia conectada y conserva su versión, ejecutable y datos. El arranque automático está limitado a MySQL 8 local, con configuración ya existente.
- Si PHP o MySQL no están disponibles, muestra una indicación concreta; no abre una página rota.
- El icono propio se comparte entre acceso de Windows, pestaña y acceso móvil.

Verificación: conexión local existente y reutilización; inicio PHP en un puerto alternativo, respuesta HTTP e identidad, segunda ejecución sin duplicar servidor, acceso COM e iconos. No se apagó la base del usuario para simular un arranque en frío. Ese caso queda sin verificación práctica.

Publicación autorizada: feature → develop → main, sin force push. `Version 2` describe esta edición local; los pendientes administrativos y de producción del seguimiento continúan vigentes. Si aparece una regresión de arranque, conservar los datos y revertir el cambio mediante un nuevo PR; no restablecer la base ni reescribir el historial.
