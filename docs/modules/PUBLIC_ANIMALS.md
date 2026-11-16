# Catálogo y fichas de animales

Rama: feature/public-animals. Búsqueda SQL preparada por nombre, especie, raza y resumen; filtros por especie, sexo, tamaño, edad, estado de salud y adopción. Los valores disponibles se obtienen de las filas no archivadas. Ordenación autorizada, 12 resultados por página y enlaces que conservan los filtros. Incluye todos los estados y los expresa sin confundir disponibilidad con adopción.

La revisión local se habilita únicamente con APP_ENV=local, conexión de loopback, host local reconocido y sin cabeceras de proxy. LOCAL_REVIEW_MODE permite desactivarla. PUBLIC_CONTENT_ENABLED permanece intacto: no se publican globalmente los registros de demostración. Por petición expresa del usuario, no se imprime un aviso de revisión sobre las páginas; el aislamiento del entorno sigue activo. Los formularios persistentes mantienen su propio bloqueo.

Las 240 vistas SVG del parche facilitado se recuperaron exclusivamente en storage/app/review, ignorado por Git, sin cambiar las rutas de BD. La ruta de revisión exige registro no archivado y valida el XML con una lista de elementos y atributos inertes; bloquea entidades, scripts, referencias externas y traversal. Sirve sin caché, con CSP sandbox y noindex. Nunca se admiten SVG mediante la subida normal.

Tarjetas reutilizables, paginación, galería centrada con teclado y restauración del foco; edad legible, estados de cuidado, relato y cronología basada en datos existentes. Las fotografías públicas conservan la ruta controlada de MediaController. Los recursos anteriores de revisión se conservan localmente; los retratos nuevos quedan documentados como arte original.

Verificación funcional: scripts/check-public-animals.php solo realiza consultas de lectura. La revisión visual y el resultado de publicación se registran al finalizar el módulo.

Ampliación autorizada: crear un retrato artístico para cada uno de los 80 perfiles y registrar sus rutas en animal_fotografias, conservando las rutas anteriores en un inventario local de recuperación. No se modifica el esquema ni la configuración de publicación.

## Retratos incorporados

Se generó un retrato artístico individual para cada uno de los 80 animales: 55 perros y 25 gatos. Herramienta integrada de generación, una solicitud por retrato; sin fotografías ajenas ni descargas de terceros. Los originales, prompts por perfil y manifiestos de recuperación se conservan únicamente en output/imagegen. Los WebP finales legítimos se versionan en public/assets/img/companions-art, sin metadatos de la herramienta ni datos privados.

La importación CLI valida MIME, dimensiones, digest y correspondencia con la ficha; bloquea las filas durante cada lote y conserva los valores anteriores antes de actualizar. Se cambiaron exclusivamente ruta y texto alternativo de 80 filas de animal_fotografias. Se mantienen 240 registros multimedia, 22 tablas y 3 vistas. La galería prioriza los retratos raster disponibles y conserva las referencias anteriores. La repetición del importador detecta 0 pendientes y no sobrescribe cambios posteriores.

Verificación: build y sintaxis correctos; consultas SQL, estados, paginación e aislamiento local aprobados. Se revisaron los 80 retratos en láminas de control: especie correcta, rostros completos e imágenes distintas. Catálogo y ficha comprobados a 390, 768 y 1440 px sin desbordamiento ni imágenes cargadas rotas. Galería centrada, avance de vistas, Escape y restauración del foco comprobados. El aviso de revisión y los rótulos de imagen no se muestran, por indicación expresa del usuario. La procedencia queda en esta documentación y en el texto alternativo.

La revisión final en el navegador integrado confirmó los nuevos retratos en las tarjetas y la ausencia de avisos visibles. Se preservó una captura local del catálogo, fuera de Git.
