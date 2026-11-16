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
$content .= '<form id="test-controls" method="get" action="/ui-check">';
$content .= component('select-field', ['required'=>true,'name'=>'species-test','id'=>'species-test','label'=>'Compañero de prueba','options'=>[['value'=>'','label'=>'Elige un compañero'],['value'=>'perro','label'=>'Perro'],['value'=>'gato','label'=>'Gato'],['value'=>'otro','label'=>'No disponible','disabled'=>true]]]);
$content .= '<button class="btn btn-outline" type="submit">Verificar selección</button></form>';
$content .= component('date-field', ['name'=>'date-test','id'=>'date-test','label'=>'Día de prueba','value'=>'2026-11-15','min'=>'2026-01-01','max'=>'2027-12-31']);
$content .= component('search-field', ['name'=>'search-test','id'=>'search-test','label'=>'Buscar en la prueba','value'=>'huellas']);
$content .= component('reset-filters', ['href'=>'/ui-check']);
$content .= '</section><script type="module" src="/fixture-events.js"></script>';
require BASE_PATH.'/app/Views/layouts/main.php';
