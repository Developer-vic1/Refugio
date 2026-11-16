<?php
use App\Support\AnimalFilters as F;
use App\Support\AnimalPresenter as P;
?>
<section class="catalogue-hero"><div class="wrap">
    <?= component('breadcrumbs', ['items'=>[['label'=>'Inicio','href'=>'/'],['label'=>'Animales']]]) ?>
    <div class="catalogue-intro" data-sequence><div><span class="micro-label"><?= icon('paw') ?> CADA ENCUENTRO TIENE SU MOMENTO</span><h1>Un compañero.<br><em>Una nueva historia.</em></h1><p>Cada vida tiene su propio camino. Conoce su etapa de cuidado y encuentra el vínculo que puedas acompañar con responsabilidad.</p><a class="text-cta" href="/adopciones">Así funciona la adopción <?= icon('arrow-up-right') ?></a></div><aside class="catalogue-promise"><span class="catalogue-promise-icon"><?= icon('heart') ?></span><h2>El cariño empieza con responsabilidad.</h2><p>La afinidad se construye con tiempo, paciencia y una conversación honesta.</p><?= component('badge',['type'=>'success','label'=>'Un paso a la vez']) ?></aside></div>
</div></section>
<section class="wrap catalogue-content" aria-labelledby="catalogue-title">
    <form class="catalogue-filters" action="/animales#resultados" method="get" role="search" aria-label="Buscar animales">
        <div class="catalogue-filter-title"><span><?= icon('search') ?></span><div><h2 id="catalogue-title">Encuentra tu próximo vínculo</h2><p>Un poco más sobre ti. Un encuentro con sentido.</p></div></div>
        <div class="catalogue-filter-fields">
            <?= component('search-field',['id'=>'animal-search','name'=>'q','label'=>'Nombre, raza o especie','value'=>$filters->get('q'),'placeholder'=>'¿A quién te gustaría conocer?']) ?>
            <?= component('select-field',['id'=>'animal-species','name'=>'especie','label'=>'Especie','value'=>$filters->get('especie'),'options'=>P::options($options['especie'],[],'Todos los compañeros')]) ?>
            <?= component('select-field',['id'=>'animal-status','name'=>'estado','label'=>'Etapa de adopción','value'=>$filters->get('estado'),'options'=>P::options($options['estado_adopcion'],F::STATUS,'Todas las etapas')]) ?>
        </div>
        <details class="catalogue-more-filters" <?= array_filter([$filters->get('sexo'),$filters->get('tamano'),$filters->get('salud'),$filters->get('edad')]) ? 'open' : '' ?>><summary><?= icon('settings') ?> Afinar nuestro encuentro <span>Edad, tamaño y más <?= icon('chevron-down') ?></span></summary><div class="catalogue-extra-fields">
            <?= component('select-field',['id'=>'animal-sex','name'=>'sexo','label'=>'Sexo','value'=>$filters->get('sexo'),'options'=>P::options($options['sexo'],F::SEX,'Sin preferencia')]) ?>
            <?= component('select-field',['id'=>'animal-size','name'=>'tamano','label'=>'Tamaño','value'=>$filters->get('tamano'),'options'=>P::options($options['tamano'],F::SIZE,'Todos los tamaños')]) ?>
            <?= component('select-field',['id'=>'animal-age','name'=>'edad','label'=>'Edad aproximada','value'=>$filters->get('edad'),'options'=>P::options(array_keys(F::AGE),F::AGE,'Todas las edades')]) ?>
            <?= component('select-field',['id'=>'animal-health','name'=>'salud','label'=>'Etapa de salud','value'=>$filters->get('salud'),'options'=>P::options($options['estado_salud'],F::HEALTH,'Todas las etapas')]) ?>
        </div></details>
        <div class="catalogue-filter-bottom"><?= component('select-field',['id'=>'animal-order','name'=>'orden','label'=>'Orden de las historias','value'=>$filters->get('orden'),'options'=>array_map(fn($value,$label)=>['value'=>$value,'label'=>$label],array_keys(F::SORT),array_values(F::SORT))]) ?><div class="catalogue-filter-actions"><?= component('reset-filters',['href'=>'/animales#resultados']) ?><button class="btn btn-primary" type="submit">Encontrar compañeros <?= icon('search') ?></button></div></div>
    </form>
    <div class="catalogue-result-heading" id="resultados"><h2><?= $result['total'] ? 'Historias por conocer' : 'Dejemos espacio al encuentro' ?></h2><p role="status"><?= $result['total'] ? $result['first'].'–'.$result['last'].' de '.$result['total'].' perfiles' : 'Sin resultados para mostrar' ?></p></div>
    <?php if (!$result['items']): ?><div class="catalogue-empty" data-sequence><figure class="catalogue-illustration" data-depth-scene><img data-depth-layer src="/assets/img/companeros-ilustracion-v2.png" width="1024" height="1024" alt="Un perro y un gato juntos"></figure><div class="catalogue-empty-copy"><span class="micro-label"><?= icon('paw') ?> LOS BUENOS ENCUENTROS LLEGAN</span><h2><?= $filters->hasFilters() ? 'Probemos con otra búsqueda.' : 'Cada historia necesita su momento.' ?></h2><p><?= $filters->hasFilters() ? 'No encontramos perfiles con esta combinación. Amplía los filtros y descubramos otras posibilidades.' : 'Todavía no hay perfiles disponibles para compartir. Mientras tanto, conoce cómo prepararte para adoptar.' ?></p><div class="catalogue-empty-actions"><?= component('reset-filters',['href'=>'/animales#resultados','label'=>'Volver a descubrir']) ?><a class="text-cta" href="/adopciones">Prepararme para adoptar <?= icon('arrow-up-right') ?></a></div></div></div>
    <?php else: ?><div class="companion-grid" data-sequence><?php foreach ($result['items'] as $a): ?><?= component('animal-card',['animal'=>$a]) ?><?php endforeach ?></div><?= component('pagination',['result'=>$result,'filters'=>$filters]) ?><?php endif ?>
</section>
