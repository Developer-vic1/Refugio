<section class="wrap section"><div class="info-panel status-panel">
    <h1><?= e($title) ?></h1>
    <?= component('alert', ['type'=>http_response_code() >= 400 ? 'error' : 'info', 'message'=>$message]) ?>
    <?= component('button', ['label'=>'Ir al inicio','href'=>'/','icon'=>'arrow-up-right']) ?>
</div></section>
