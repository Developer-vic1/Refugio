<div class="ui-empty">
    <span class="ui-empty-icon"><?= icon($props['icon'] ?? 'paw') ?></span>
    <h2><?= e($props['title'] ?? 'Todavía no hay registros') ?></h2>
    <p><?= e($props['message'] ?? '') ?></p>
    <?php if (!empty($props['action'])): ?><?= component('button', $props['action']) ?><?php endif ?>
</div>
