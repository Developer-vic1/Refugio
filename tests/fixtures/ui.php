<?php
declare(strict_types=1);
// Fixture sin datos, accesible solamente desde el servidor aislado descrito en docs.
require dirname(__DIR__, 2).'/bootstrap.php';
$title = 'Verificación de componentes';
$content = '<section class="wrap section"><h1>Verificación de componentes</h1>';
foreach (['success','error','warning','info','progress'] as $type) {
    $content .= component('alert', ['type'=>$type, 'title'=>ucfirst($type), 'message'=>'Mensaje de prueba sin datos personales.']);
}
$content .= '<form id="test-confirm" method="post" data-confirm="Confirma la operación de prueba. No modifica datos."><label>Nota de prueba<input name="nota" value="Prueba" required></label><button class="btn btn-danger" name="accion" value="archivar">Archivar prueba</button></form><p id="test-result" role="status">Envíos confirmados: 0</p>';
$content .= component('skeleton').component('progress', ['label'=>'Comprobación de interfaz', 'value'=>50]);
$content .= '</section><script type="module" src="/fixture-events.js"></script>';
require BASE_PATH.'/app/Views/layouts/main.php';
