<section class="login-screen">
    <div class="login-picture">
        <img src="/assets/img/hero-compania.webp" alt="Imagen ilustrativa de un perro y un gato" fetchpriority="high">
        <div class="login-overlay"><span class="micro-label">UN COMPROMISO QUE SIGUE CADA DÍA</span><h1>Cuidar cada historia, desde dentro.</h1><p>Un espacio privado para acompañar rescates, adopciones y recursos con responsabilidad.</p></div>
    </div>
    <div class="login-form-area"><div class="login-card">
        <?= component('badge', ['type'=>'info', 'label'=>'Solo personal autorizado']) ?>
        <h2>Bienvenido de nuevo.</h2>
        <p>Ingresa con tu cuenta administrativa para continuar.</p>
        <form method="post" action="/admin/login" class="editor-form" data-submit-state>
            <?= csrf_field() ?>
            <?= component('form-field', ['id'=>'admin-user', 'name'=>'usuario', 'label'=>'Correo o nombre de usuario', 'autocomplete'=>'username', 'required'=>true, 'maxlength'=>150]) ?>
            <?= component('form-field', ['id'=>'admin-password', 'name'=>'contrasena', 'label'=>'Contraseña', 'type'=>'password', 'autocomplete'=>'current-password', 'required'=>true, 'maxlength'=>1024, 'hint'=>'Puedes pegar tu contraseña o usar tu gestor de contraseñas.']) ?>
            <?= component('button', ['label'=>'Ingresar al panel', 'type'=>'submit', 'icon'=>'arrow-up-right']) ?>
            <p class="form-submit-status" role="status" data-submit-status></p>
        </form>
        <a class="text-cta" href="/"><?= icon('arrow-left') ?> Volver al sitio público</a>
        <p class="login-note"><?= icon('lock') ?> Acceso limitado a personal autorizado. Tu actividad puede quedar registrada por seguridad.</p>
    </div></div>
</section>
