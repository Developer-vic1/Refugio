# Adopciones y ficha PDF

Rama: feature/public-adoptions. Página editorial del proceso, cinco preparativos interactivos y preguntas frecuentes. Los perfiles disponibles enlazan al formulario de tres pasos, con errores por campo, contadores, revisión y borrador opcional en la pestaña. La privacidad se confirma de nuevo al recuperar un borrador.

El servidor valida campos, edad y privacidad mediante POST con CSRF. Dompdf 3.1.6 genera un documento A4 con identidad, retrato, respuestas y referencia propia; los recursos remotos, JavaScript y PHP del documento están desactivados. La preparación no inserta una solicitud ni envía mensajes. El visitante descarga y adjunta el archivo manualmente en WhatsApp +59175859142.

Comprobado: build, sintaxis, validación, PDF real, CSRF, descarga desde el navegador, recuperación y eliminación de borrador. Documentos normal y extenso renderizados. Composer audit sin avisos. Los ejemplos y datos sintéticos quedan en output, fuera de Git.
