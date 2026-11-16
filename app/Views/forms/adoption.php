<?php
use App\Services\AdoptionPreparation as Preparation;
use App\Support\AnimalPresenter as Presenter;
$values=$values??[];$errors=$errors??[];$formError=$formError??'';
$field=static function(string $name,array $extra=[]) use($values,$errors):string {
    $definition=Preparation::FIELDS[$name];
    return component('form-field',$extra+['name'=>$name,'id'=>'prepare-'.$name,'label'=>$definition['label'],'maxlength'=>$definition['max'],'required'=>empty($definition['optional']),'value'=>$values[$name]??'','error'=>$errors[$name]??'']);
};
?>
<section class="wrap preparation-page">
<?= component('breadcrumbs',['items'=>[['label'=>'Inicio','href'=>'/'],['label'=>'Adopciones','href'=>'/adopciones'],['label'=>'Tu ficha']]]) ?>
<div class="preparation-heading"><span class="micro-label"><?= icon('heart') ?> UNA HISTORIA QUE EMPIEZA CONTIGO</span><h1>Preparar mi encuentro<br><em>con <?= e($animal['nombre']) ?>.</em></h1><p>Cuéntanos sobre ti y el hogar que imaginas. Podrás revisar tus respuestas, guardar tu PDF y compartirlo por WhatsApp cuando decidas continuar.</p></div>
<div class="preparation-grid"><div>
<form method="post" action="/adopciones/solicitar/<?= (int)$animal['id'] ?>/pdf" class="preparation-form" data-prepared-form data-animal-id="<?= (int)$animal['id'] ?>">
<?= csrf_field() ?>
<div class="preparation-progress" data-step-navigation hidden><ol aria-label="Tu recorrido"><li data-step-indicator="0" aria-current="step"><span>1</span> Sobre ti</li><li data-step-indicator="1"><span>2</span> Tu cuidado</li><li data-step-indicator="2"><span>3</span> Revisar</li></ol><progress max="3" value="1" aria-label="Paso actual" data-step-progress></progress></div>
<div class="preparation-error" data-form-error role="alert" <?= $errors || $formError ? '' : 'hidden' ?>><?= e($formError ?: ($errors ? 'Revisa los campos señalados. Tus respuestas siguen aquí.' : '')) ?></div>
<fieldset data-form-step="0"><legend tabindex="-1">Primero, conozcámonos.</legend><p class="step-intro">Tus datos acompañarán el documento que tú decidas compartir.</p><?= $field('nombre_completo',['autocomplete'=>'name']) ?><div class="preparation-row"><?= $field('edad',['type'=>'number','min'=>18,'max'=>120,'hint'=>'La ficha la prepara una persona mayor de edad.']) ?><?= $field('telefono',['type'=>'tel','autocomplete'=>'tel','hint'=>'Incluye tu prefijo si corresponde.']) ?></div><?= $field('correo',['type'=>'email','autocomplete'=>'email']) ?></fieldset>
<fieldset data-form-step="1"><legend tabindex="-1">El cuidado que puedes ofrecer.</legend><p class="step-intro">No buscamos respuestas perfectas. Cuéntanos con honestidad cómo te gustaría acompañar este vínculo.</p>
<?= $field('motivo',['type'=>'textarea','counter'=>true,'hint'=>'¿Qué te mueve a dar este paso?']) ?>
<?= $field('experiencia_previa',['type'=>'textarea','counter'=>true,'hint'=>'También puedes contarnos si sería tu primer compañero.']) ?>
<?= $field('condiciones_hogar',['type'=>'textarea','counter'=>true,'hint'=>'Describe el espacio, con quién vives y cómo prepararías su llegada.']) ?>
<?= $field('disponibilidad_responsabilidades',['type'=>'textarea','counter'=>true,'hint'=>'Piensa en el tiempo diario, los cuidados y quién podría acompañarte.']) ?>
<?= $field('informacion_adicional',['type'=>'textarea','counter'=>true,'rows'=>3,'hint'=>'Opcional. Hay preguntas o detalles que merecen su propio espacio.']) ?>
</fieldset>
<fieldset data-form-step="2"><legend tabindex="-1">Una última mirada, con calma.</legend><p class="step-intro">Revisa lo que quieres compartir. Puedes volver a cualquier paso antes de preparar el archivo.</p><dl class="preparation-review" data-review-summary hidden><?php foreach(Preparation::FIELDS as $name=>$definition): ?><div><dt><?= e($definition['label']) ?></dt><dd data-review-value="<?= e($name) ?>"></dd></div><?php endforeach ?></dl>
<label class="preparation-consent"><input required type="checkbox" name="acepta_privacidad" value="1" <?= ($values['acepta_privacidad']??'')==='1'?'checked':'' ?> <?= isset($errors['acepta_privacidad'])?'aria-invalid="true"':'' ?>><span>Leí la <a href="/privacidad" target="_blank" rel="noopener">información de privacidad</a> y quiero preparar mi ficha para compartirla por mi cuenta.</span></label><?php if(isset($errors['acepta_privacidad'])):?><p class="field-error"><?= e($errors['acepta_privacidad']) ?></p><?php endif ?>
<div class="preparation-submit"><button type="submit" class="btn btn-primary" data-prepare-pdf>Preparar mi PDF <?= icon('file-text') ?></button><small>El archivo quedará en tus manos. El siguiente paso lo conversas con el equipo.</small></div>
</fieldset>
<div class="preparation-step-actions" data-step-navigation hidden><button type="button" class="btn btn-outline" data-step-back><?= icon('arrow-left') ?> Volver</button><span data-step-label role="status">Paso 1 de 3</span><button type="button" class="btn btn-primary" data-step-next>Continuar <?= icon('arrow-right') ?></button></div>
<div class="preparation-draft" data-draft-panel hidden><label><input type="checkbox" data-save-draft><span>Guardar mi avance mientras esta pestaña siga abierta.</span></label><button type="button" class="text-cta" data-clear-draft hidden>Eliminar borrador <?= icon('refresh') ?></button><p data-draft-status role="status"></p></div>
<p class="preparation-status" data-prepare-status role="status"></p>
</form>
<section class="preparation-ready" data-pdf-ready hidden aria-labelledby="pdf-ready-title"><span class="ready-symbol"><?= icon('check-circle') ?></span><span class="micro-label">LA CONVERSACIÓN YA TIENE UN COMIENZO</span><h2 id="pdf-ready-title">Tu ficha está lista.<br><em>El próximo paso es tuyo.</em></h2><p>Descarga el archivo y consérvalo. Cuando quieras continuar, abre WhatsApp y adjúntalo tú mismo al mensaje.</p><div class="preparation-ready-actions"><a class="btn btn-primary" data-pdf-download>Descargar mi ficha <?= icon('download') ?></a><a class="btn btn-outline" href="<?= e(\App\Services\SiteIdentity::whatsapp('Hola, preparé una ficha para conversar sobre '.$animal['nombre'].'. Quisiera conocer los siguientes pasos.')) ?>" target="_blank" rel="noopener">Continuar por WhatsApp <?= icon('message') ?></a></div><small>La descarga prepara tu documento personal. El equipo podrá conocerlo cuando tú lo compartas.</small></section>
<noscript><p class="form-note">Al preparar el PDF se iniciará la descarga. Después puedes <a href="<?= e(\App\Services\SiteIdentity::whatsapp()) ?>" target="_blank" rel="noopener">abrir WhatsApp</a> y adjuntar el archivo.</p></noscript>
</div><aside class="preparation-companion"><a href="/animales/<?= (int)$animal['id'] ?>"><img src="<?= e(asset($animal['foto'])) ?>" alt="Retrato de <?= e($animal['nombre']) ?>" width="600" height="450"></a><div><span class="micro-label"><?= icon('paw') ?> TU POSIBLE COMPAÑERO</span><h2><?= e($animal['nombre']) ?></h2><p><?= e(ucfirst($animal['especie'])) ?> · <?= e(Presenter::age($animal['edad_aproximada_meses']===null?null:(int)$animal['edad_aproximada_meses'])) ?></p><blockquote>Un vínculo se construye con tiempo, cuidado y ganas de aprender juntos.</blockquote><a class="text-cta" href="/animales/<?= (int)$animal['id'] ?>">Volver a su historia <?= icon('arrow-up-right') ?></a></div></aside></div>
</section>
