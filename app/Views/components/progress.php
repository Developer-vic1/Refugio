<?php $value = max(0, min(100, (int)($props['value'] ?? 0))); ?>
<label class="ui-progress"><?= e($props['label'] ?? 'Progreso') ?> <strong><?= $value ?>%</strong><progress value="<?= $value ?>" max="100"><?= $value ?>%</progress></label>
