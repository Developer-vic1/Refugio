<nav class="ui-breadcrumbs" aria-label="Ruta de navegación"><ol>
<?php foreach (($props['items'] ?? []) as $item): ?><li><?php if (isset($item['href'])): ?><a href="<?= e(\App\Support\Ui::localHref($item['href'])) ?>"><?= e($item['label']) ?></a><?php else: ?><span aria-current="page"><?= e($item['label']) ?></span><?php endif ?></li><?php endforeach ?>
</ol></nav>
