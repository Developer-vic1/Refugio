<?php
$variant = in_array($props['variant'] ?? '', ['primary', 'outline', 'warm', 'danger'], true) ? $props['variant'] : 'primary';
$label = (string)($props['label'] ?? 'Continuar');
$disabled = (bool)($props['disabled'] ?? false);
?>
<?php if (isset($props['href']) && !$disabled): ?>
<a class="btn btn-<?= e($variant) ?>" href="<?= e(\App\Support\Ui::localHref($props['href'])) ?>"><?= e($label) ?><?= isset($props['icon']) ? icon($props['icon']) : '' ?></a>
<?php else: ?>
<button class="btn btn-<?= e($variant) ?>" type="<?= ($props['type'] ?? '') === 'submit' ? 'submit' : 'button' ?>" <?= $disabled ? 'disabled' : '' ?>><?= e($label) ?><?= isset($props['icon']) ? icon($props['icon']) : '' ?></button>
<?php endif; ?>
