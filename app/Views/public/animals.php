<section class="catalogue-hero">
    <div class="wrap">
        <?= component('breadcrumbs', ['items'=>[['label'=>'Inicio','href'=>'/'],['label'=>'Animales']]]) ?>
        <div class="catalogue-intro" data-sequence>
            <div>
                <span class="micro-label"><?= icon('paw') ?> UN ENCUENTRO PUEDE CAMBIARLO TODO</span>
                <h1>Un compañero.<br><em>Una nueva historia.</em></h1>
                <p>Conoce a quienes esperan un hogar. Cada ficha cuenta una historia de cuidado y una oportunidad para empezar de nuevo.</p>
                <a class="text-cta" href="/adopciones">Así funciona la adopción <?= icon('arrow-up-right') ?></a>
            </div>
            <aside class="catalogue-promise">
                <span class="catalogue-promise-icon"><?= icon('heart') ?></span>
                <h2>El cariño empieza con responsabilidad.</h2>
                <p>Solo publicamos perfiles revisados y autorizados por el refugio.</p>
                <?= component('badge', ['type'=>'success','label'=>'Información con cuidado']) ?>
            </aside>
        </div>
    </div>
</section>
<section class="wrap catalogue-content" aria-labelledby="catalogue-title">
    <form class="catalogue-filters" action="/animales" method="get" role="search" aria-label="Buscar animales">
        <div class="catalogue-filter-title"><span><?= icon('search') ?></span><div><h2 id="catalogue-title">Encuentra tu próximo vínculo</h2><p>Busca por nombre o especie.</p></div></div>
        <div class="catalogue-filter-fields">
            <?= component('search-field',['id'=>'animal-search','name'=>'q','label'=>'Nombre o especie','value'=>$query,'placeholder'=>'¿A quién te gustaría conocer?']) ?>
            <?= component('select-field',['id'=>'animal-species','name'=>'especie','label'=>'Especie','value'=>$species,'options'=>[['value'=>'','label'=>'Todos los compañeros'],['value'=>'Perro','label'=>'Perro'],['value'=>'Gato','label'=>'Gato']]]) ?>
            <div class="catalogue-filter-actions"><button class="btn btn-primary" type="submit">Buscar <?= icon('search') ?></button><?= component('reset-filters',['href'=>'/animales']) ?></div>
        </div>
    </form>
    <?php if (!$animals): ?>
        <div class="catalogue-empty" data-sequence>
            <figure class="catalogue-illustration"><img src="/assets/img/companeros-ilustracion-v2.png" width="1024" height="1024" alt="Ilustración original de un perro y un gato juntos"><figcaption>Ilustración · no representa animales registrados</figcaption></figure>
            <div class="catalogue-empty-copy">
                <span class="micro-label"><?= icon('paw') ?> LOS BUENOS ENCUENTROS LLEGAN</span>
                <h2><?= $query !== '' || $species !== '' ? 'Probemos con otra búsqueda.' : 'Una historia bonita también necesita su momento.' ?></h2>
                <p><?= $query !== '' || $species !== '' ? 'No encontramos perfiles públicos con estos filtros. Puedes ampliar la búsqueda o conocer el proceso de adopción.' : 'Todavía no hay fichas públicas disponibles. El equipo revisa cada perfil antes de compartirlo. Mientras tanto, descubre cómo prepararte para adoptar.' ?></p>
                <div class="catalogue-empty-actions"><?= component('button', ['href'=>'/adopciones','label'=>'Prepararme para adoptar','icon'=>'arrow-up-right']) ?><a class="text-cta" href="/contacto">Hablar con el refugio <?= icon('arrow-up-right') ?></a></div>
            </div>
        </div>
    <?php else: ?>
        <div class="animal-grid gap-4 catalogue-results" data-sequence>
            <?php foreach($animals as $a): ?><a class="pet-card" href="/animales/<?= (int)$a['id'] ?>"><img alt="<?= e('Fotografía de '.$a['nombre']) ?>" src="<?= e(asset($a['foto'])) ?>" loading="lazy"><div class="pet-meta"><span class="eyebrow"><?= e($a['especie']) ?> · <?= e(str_replace('_',' ',$a['estado_adopcion'])) ?></span><h3><?= e($a['nombre']) ?></h3><p><?= e($a['resumen']) ?></p><span class="text-link">Conocer su historia <?= icon('arrow-up-right') ?></span></div></a><?php endforeach ?>
        </div>
    <?php endif ?>
</section>
