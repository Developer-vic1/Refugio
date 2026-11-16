<?php
$id = (string)($props['id'] ?? $props['name']);
$type = in_array($props['type'] ?? '', ['text','email','password','search','number','date'], true) ? $props['type'] : 'text';
$error = (string)($props['error'] ?? '');
$hint = (string)($props['hint'] ?? '');
$described = trim(($hint !== '' ? $id.'-hint ' : '').($error !== '' ? $id.'-error' : ''));
?>
<div class="form-field">
    <label for="<?= e($id) ?>"><?= e($props['label'] ?? '') ?><?php if ($props['required'] ?? false): ?> <span class="required-mark">(obligatorio)</span><?php endif ?></label>
    <div class="<?= $type === 'password' ? 'password-field' : 'field-control' ?>">
        <input id="<?= e($id) ?>" name="<?= e($props['name']) ?>" type="<?= e($type) ?>" value="<?= $type === 'password' ? '' : e($props['value'] ?? '') ?>" <?= ($props['required'] ?? false) ? 'required' : '' ?> maxlength="<?= (int)($props['maxlength'] ?? 150) ?>" autocomplete="<?= e($props['autocomplete'] ?? 'off') ?>" <?= $described !== '' ? 'aria-describedby="'.e($described).'"' : '' ?> <?= $error !== '' ? 'aria-invalid="true"' : '' ?>>
        <?php if ($type === 'password'): ?><button type="button" class="icon-button" data-toggle-password="<?= e($id) ?>" aria-label="Mostrar contraseña" aria-pressed="false"><?= icon('eye') ?></button><?php endif ?>
    </div>
    <?php if ($hint !== ''): ?><small id="<?= e($id) ?>-hint" class="field-hint"><?= e($hint) ?></small><?php endif ?>
    <?php if ($error !== ''): ?><p id="<?= e($id) ?>-error" class="field-error"><?= e($error) ?></p><?php endif ?>
</div>
