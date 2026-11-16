<?php $result = $props['result']; $filters = $props['filters']; ?>
<?php if ($result['pages'] > 1): ?>
<nav class="companion-pagination" aria-label="Páginas de animales">
<?php if ($result['page'] > 1): ?><a class="pagination-direction" href="<?= e($filters->href($result['page'] - 1)) ?>#resultados"><?= icon('arrow-left') ?> Anterior</a><?php else: ?><span class="pagination-direction" aria-disabled="true"><?= icon('arrow-left') ?> Anterior</span><?php endif ?>
<div><?php for ($number = max(1,$result['page']-2); $number <= min($result['pages'],$result['page']+2); $number++): ?><a href="<?= e($filters->href($number)) ?>#resultados" <?= $number === $result['page'] ? 'aria-current="page"' : '' ?> aria-label="Página <?= $number ?>"><?= $number ?></a><?php endfor ?></div>
<?php if ($result['page'] < $result['pages']): ?><a class="pagination-direction" href="<?= e($filters->href($result['page'] + 1)) ?>#resultados">Siguiente <?= icon('arrow-right') ?></a><?php else: ?><span class="pagination-direction" aria-disabled="true">Siguiente <?= icon('arrow-right') ?></span><?php endif ?>
</nav><?php endif ?>
