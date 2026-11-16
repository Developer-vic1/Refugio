<?php $type = \App\Support\Ui::noticeType($props['type'] ?? 'info'); ?>
<div class="ui-alert notice-<?= e($type) ?>" role="<?= $type === 'error' ? 'alert' : 'status' ?>" aria-atomic="true">
    <?= icon(\App\Support\Ui::noticeIcon($type)) ?>
    <div><?php if (!empty($props['title'])): ?><strong><?= e($props['title']) ?></strong><?php endif ?><p><?= e($props['message'] ?? '') ?></p></div>
</div>
