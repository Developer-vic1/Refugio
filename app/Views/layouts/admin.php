<div class="admin-shell">
  <aside class="admin-sidebar" id="admin-sidebar" aria-label="Secciones administrativas">
    <a class="brand admin-brand" href="/admin"><span class="brand-icon" aria-hidden="true"><?= icon('paw') ?></span><span>Refugio <b>Vida Animal</b><small>Centro de administración</small></span></a>
    <nav class="admin-menu" aria-label="Navegación administrativa">
      <?php foreach ([['/admin','grid','Resumen'],['/admin/animales','paw','Animales'],['/admin/adopciones','heart','Adopciones'],['/admin/campanas','flag','Campañas'],['/admin/aportes','coins','Aportes'],['/admin/donaciones-materiales','box','Donaciones materiales'],['/admin/inventario','box','Inventario'],['/admin/historias','book','Historias'],['/admin/voluntariado','users','Voluntariado'],['/admin/transparencia','chart','Transparencia'],['/admin/gastos','chart','Gastos'],['/admin/configuracion','settings','Configuración'],['/admin/tema','palette','Identidad visual'],['/admin/auditoria','history','Auditoría']] as $nav): ?>
        <a class="admin-nav-item <?= nav_active($nav[0])?'active':'' ?>" href="<?= e($nav[0]) ?>" <?= nav_active($nav[0]) ? 'aria-current="page"' : '' ?>><span aria-hidden="true"><?= icon($nav[1]) ?></span><?= e($nav[2]) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-bottom"><a href="/" target="_blank" rel="noopener"><?= icon('arrow-up-right') ?> Abrir sitio público</a>
      <form method="post" action="/admin/logout" data-confirm="¿Quieres cerrar la sesión administrativa?"><?= csrf_field() ?><button type="submit">Cerrar sesión</button></form></div>
  </aside>
  <div class="admin-main">
    <header class="admin-topbar"><button class="mobile-admin-toggle" type="button" data-admin-menu aria-label="Abrir panel lateral" aria-expanded="false" aria-controls="admin-sidebar"><?= icon('menu') ?></button><div class="admin-current">Panel de gestión <span> / <?= e($title??'Resumen') ?></span></div><div class="admin-profile"><span class="status-dot"></span><?= e($_SESSION['admin_name']??'Administrador') ?></div></header>
    <main id="contenido" class="admin-content" tabindex="-1"><?= $content ?></main>
  </div>
</div>
