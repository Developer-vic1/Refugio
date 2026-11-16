<header class="site-header" data-header>
 <div class="container-shell site-header-inner">
   <a class="brand" href="/" aria-label="Refugio Vida Animal, ir al inicio"><span class="brand-icon" aria-hidden="true"><?= icon('paw') ?></span><span>Refugio <b>Vida Animal</b></span></a>
   <button class="menu-toggle" type="button" data-menu aria-controls="public-nav" aria-expanded="false" aria-label="Abrir menú"><span></span><span></span><span></span></button>
   <nav class="main-nav" id="public-nav" aria-label="Navegación principal">
    <?php foreach([['/','Inicio'],['/nosotros','Nosotros'],['/animales','Animales'],['/adopciones','Adopciones'],['/campanas','Campañas'],['/donaciones','Donaciones'],['/voluntariado','Voluntariado'],['/historias','Historias'],['/transparencia','Transparencia'],['/contacto','Contacto']] as $link): ?>
      <a class="nav-link <?= nav_active($link[0])?'selected':'' ?>" href="<?= e($link[0]) ?>" <?= nav_active($link[0]) ? 'aria-current="page"' : '' ?>><?= e($link[1]) ?></a>
    <?php endforeach; ?>
    <a class="admin-entry" href="/admin/login"><?= icon('lock') ?> Ingreso administrativo</a>
   </nav>
 </div>
</header>
