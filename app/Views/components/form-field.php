<?php
$id = (string)($props['id'] ?? $props['name']);
$type = in_array($props['type'] ?? '', ['text','email','password','search','number','date','tel','textarea'], true) ? $props['type'] : 'text';
$error = (string)($props['error'] ?? '');
$hint = (string)($props['hint'] ?? '');
$counter = $type === 'textarea' && ($props['counter'] ?? false);
$described = trim(($hint !== '' ? $id.'-hint ' : '').($error !== '' ? $id.'-error ' : '').($counter ? $id.'-count' : ''));
?>
<div class="form-field">
    <label for="<?= e($id) ?>"><?= e($props['label'] ?? '') ?><?php if ($props['required'] ?? false): ?> <span class="required-mark">(obligatorio)</span><?php endif ?></label>
    <div class="<?= $type === 'password' ? 'password-field' : 'field-control' ?>">
        <?php if ($type === 'textarea'): ?><textarea id="<?= e($id) ?>" name="<?= e($props['name']) ?>" rows="<?= max(2,min(12,(int)($props['rows'] ?? 4))) ?>" maxlength="<?= (int)($props['maxlength'] ?? 1500) ?>" <?= ($props['required'] ?? false) ? 'required' : '' ?> <?= $described !== '' ? 'aria-describedby="'.e($described).'"' : '' ?> <?= $error !== '' ? 'aria-invalid="true"' : '' ?>><?= e($props['value'] ?? '') ?></textarea>
        <?php else: ?><input id="<?= e($id) ?>" name="<?= e($props['name']) ?>" type="<?= e($type) ?>" value="<?= $type === 'password' ? '' : e($props['value'] ?? '') ?>" <?= ($props['required'] ?? false) ? 'required' : '' ?> maxlength="<?= (int)($props['maxlength'] ?? 150) ?>" autocomplete="<?= e($props['autocomplete'] ?? 'off') ?>" <?= isset($props['min']) ? 'min="'.(int)$props['min'].'"' : '' ?> <?= isset($props['max']) ? 'max="'.(int)$props['max'].'"' : '' ?> <?= $described !== '' ? 'aria-describedby="'.e($described).'"' : '' ?> <?= $error !== '' ? 'aria-invalid="true"' : '' ?>><?php endif ?>
        <?php if ($type === 'password'): ?><button type="button" class="icon-button" data-toggle-password="<?= e($id) ?>" aria-label="Mostrar contraseña" aria-pressed="false"><?= icon('eye') ?></button><?php endif ?>
    </div>
    <?php if ($hint !== ''): ?><small id="<?= e($id) ?>-hint" class="field-hint"><?= e($hint) ?></small><?php endif ?>
    <?php if ($error !== ''): ?><p id="<?= e($id) ?>-error" class="field-error"><?= e($error) ?></p><?php endif ?>
    <?php if ($counter): ?><small class="field-counter" id="<?= e($id) ?>-count" data-count-for="<?= e($id) ?>"><?= mb_strlen((string)($props['value'] ?? '')) ?> / <?= (int)($props['maxlength'] ?? 1500) ?></small><?php endif ?>
</div>
