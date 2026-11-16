<?php $type = \App\Support\Ui::noticeType($props['type'] ?? 'info'); ?>
<span class="ui-badge notice-<?= e($type) ?>"><?= icon(\App\Support\Ui::noticeIcon($type)) ?><?= e($props['label'] ?? '') ?></span>
