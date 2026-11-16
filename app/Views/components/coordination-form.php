<?php
use App\Services\PublicCoordination as Coordination;
use App\Support\AnimalPresenter;
$kind=$props['kind']??'ayuda';
$volunteer=$kind==='voluntariado';
$types=$volunteer?Coordination::ROLES:Coordination::HELP;
$id='coordinate-'.$kind;
$optionList=static fn(array $items,string $empty)=>AnimalPresenter::options(array_keys($items),$items,$empty);
?>
<form class="coordination-form" action="/colaborar/preparar" method="post" data-coordination-form>
<?= csrf_field() ?><input type="hidden" name="kind" value="<?= e($kind) ?>">
<span class="micro-label"><?= icon('message') ?> EMPECEMOS POR CONVERSAR</span><h3><?= $volunteer?'Tu tiempo tiene algo que aportar.':'Tu ayuda puede encontrar su lugar.' ?></h3><p class="field-hint"><?= $volunteer?'Cuéntanos qué te interesa y cuándo podrías acompañar.':'Cuéntanos qué tienes en mente; coordinaremos necesidades y entrega por mensaje.' ?></p>
<?= component('select-field',['id'=>$id.'-type','name'=>'tipo','label'=>$volunteer?'¿Dónde te gustaría sumar?':'¿Qué te gustaría compartir?','required'=>true,'value'=>$props['selectedType']??'','options'=>$optionList($types,'Elige una forma de participar')]) ?>
<?php if(!$volunteer): ?><?= component('select-field',['id'=>$id.'-line','name'=>'linea','label'=>'Una iniciativa que te mueve','value'=>$props['selectedLine']??'','options'=>\App\Services\PublicInitiatives::options()]) ?><div class="participation-form-row" data-coordinate-quantity><div class="form-field"><label for="<?= e($id) ?>-quantity">Cantidad orientativa <small>(opcional)</small></label><input type="number" step="0.1" min="0.1" max="100000" name="cantidad" id="<?= e($id) ?>-quantity" placeholder="Por conversar"></div><?= component('select-field',['id'=>$id.'-unit','name'=>'unidad','label'=>'Unidad','value'=>'unidades','options'=>array_map(fn($v,$l)=>['value'=>$v,'label'=>$l],array_keys(Coordination::UNITS),array_values(Coordination::UNITS))]) ?></div>
<?php else: ?><?= component('select-field',['id'=>$id.'-time','name'=>'franja','label'=>'Tu disponibilidad','value'=>'por_coordinar','options'=>array_map(fn($v,$l)=>['value'=>$v,'label'=>$l],array_keys(Coordination::TIMES),array_values(Coordination::TIMES))]) ?><?php endif ?>
<div class="form-field"><label for="<?= e($id) ?>-date">Una fecha posible <small>(opcional)</small></label><input id="<?= e($id) ?>-date" name="fecha" type="date" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d',strtotime('+2 years')) ?>"><small class="field-hint">Es una propuesta para conversar; acordaremos la fecha contigo.</small></div>
<?= component('form-field',['id'=>$id.'-detail','name'=>'detalle','type'=>'textarea','label'=>$volunteer?'Una habilidad o idea que quieras compartir':'Cuéntanos un poco más','maxlength'=>600,'rows'=>3,'hint'=>'Opcional. Comparte solo lo necesario para empezar.']) ?>
<button class="btn btn-primary" type="submit">Preparar mi mensaje <?= icon('arrow-right') ?></button><p class="coordination-status" data-coordinate-status role="status"></p>
<div class="coordination-ready" data-coordinate-ready hidden><h4>Un mensaje con tu intención.</h4><p data-coordinate-preview></p><a class="btn btn-outline" data-coordinate-link target="_blank" rel="noopener">Abrir WhatsApp <?= icon('message') ?></a><small>Revisa el texto y decide cuándo enviarlo desde WhatsApp.</small></div>
<noscript><p class="field-hint">Podrás revisar el mensaje en el siguiente paso antes de abrir WhatsApp.</p></noscript>
</form>
