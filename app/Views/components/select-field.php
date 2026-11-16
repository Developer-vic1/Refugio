<?php $id = (string)($props['id'] ?? $props['name']); ?>
<div class="form-field"><label for="<?= e($id) ?>"><?= e($props['label']) ?></label>
<select id="<?= e($id) ?>" name="<?= e($props['name']) ?>" data-refugio-select <?= ($props['required'] ?? false) ? 'required' : '' ?>>
<?php foreach ($props['options'] as $option): ?><option value="<?= e($option['value']) ?>" <?= (string)($props['value'] ?? '') === (string)$option['value'] ? 'selected' : '' ?> <?= ($option['disabled'] ?? false) ? 'disabled' : '' ?>><?= e($option['label']) ?></option><?php endforeach ?>
</select><?php if (!empty($props['hint'])): ?><small class="field-hint"><?= e($props['hint']) ?></small><?php endif ?></div>
