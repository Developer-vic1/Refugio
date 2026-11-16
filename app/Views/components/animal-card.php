<?php
use App\Support\AnimalFilters as F;
use App\Support\AnimalPresenter as P;
$a = $props['animal'];
?>
<article class="companion-card">
    <a class="companion-image" href="/animales/<?= (int)$a['id'] ?>" aria-label="Conocer a <?= e($a['nombre']) ?>"><img data-animal-media src="<?= e(asset($a['foto'])) ?>" alt="<?= e('Vista del perfil de '.$a['nombre']) ?>" width="600" height="400" loading="lazy"><?= component('badge', ['type'=>P::statusType($a['estado_adopcion']),'label'=>F::STATUS[$a['estado_adopcion']] ?? 'Por confirmar']) ?></a>
    <div class="companion-copy"><span class="micro-label"><?= e(ucfirst($a['especie'])) ?> · <?= e(F::SEX[$a['sexo']] ?? 'Por confirmar') ?></span><h3><a href="/animales/<?= (int)$a['id'] ?>"><?= e($a['nombre']) ?></a></h3><div class="companion-facts"><span><?= icon('calendar') ?> <?= e(P::age($a['edad_aproximada_meses'] === null ? null : (int)$a['edad_aproximada_meses'])) ?></span><span><?= icon('paw') ?> <?= e(F::SIZE[$a['tamano']] ?? 'Por confirmar') ?></span></div><p><?= e($a['resumen'] ?: 'Conoce más sobre su etapa de cuidado y lo que necesita para su próximo paso.') ?></p><a class="text-cta" href="/animales/<?= (int)$a['id'] ?>">Conocer su historia <?= icon('arrow-up-right') ?></a></div>
</article>
