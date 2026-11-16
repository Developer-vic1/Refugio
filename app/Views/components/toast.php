<?php $type = \App\Support\Ui::noticeType($props['type'] ?? 'info'); ?>
<div class="toast ui-alert notice-<?= e($type) ?>" role="<?= $type === 'error' ? 'alert' : 'status' ?>" aria-atomic="true" data-toast>
    <?= icon(\App\Support\Ui::noticeIcon($type)) ?><div><strong><?= e(['success'=>'Operación completada','error'=>'No se pudo completar','warning'=>'Revisa esta información','info'=>'Información','progress'=>'En proceso'][$type]) ?></strong><p><?= e($props['message'] ?? '') ?></p></div>
    <button type="button" class="icon-button" data-close-toast aria-label="Cerrar notificación"><?= icon('close') ?></button>
</div>
