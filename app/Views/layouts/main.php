<!doctype html>
<html lang="es" data-theme="<?= e(\App\Services\ThemeService::current()['theme']) ?>" data-motion="<?= e(\App\Services\ThemeService::current()['motion']) ?>">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="Refugio Vida Animal: rescate, cuidado, adopciones y solidaridad con información responsable.">
<meta name="color-scheme" content="light">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<title><?= e($title ?? 'Inicio') ?> | Refugio Vida Animal</title>
<link rel="preload" href="/assets/img/hero-rescate.webp" as="image" fetchpriority="high">
<link rel="stylesheet" href="/assets/css/tailwind.css">
<link rel="stylesheet" href="/assets/css/app.css">
<script defer src="/assets/js/app.js"></script>
</head>
<?php $isAdmin = str_starts_with((string)(parse_url($_SERVER['REQUEST_URI']??'/', PHP_URL_PATH)??'/'),'/admin'); ?>
<body class="<?= $isAdmin?'mode-admin':'mode-public' ?>">
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<div class="pointer-glow" aria-hidden="true"></div><div class="scroll-progress" aria-hidden="true"></div>
<?php if($isAdmin && \App\Core\Auth::check()): ?>
<div class="admin-shell">
  <aside class="admin-sidebar" id="admin-sidebar" aria-label="Secciones administrativas">
    <a class="brand admin-brand" href="/admin"><span class="brand-icon" aria-hidden="true">✿</span><span>Refugio <b>Vida Animal</b><small>Centro de administración</small></span></a>
    <nav class="admin-menu" aria-label="Navegación administrativa">
      <?php foreach ([['/admin','▦','Resumen'],['/admin/animales','♧','Animales'],['/admin/adopciones','♡','Adopciones'],['/admin/campanas','◈','Campañas'],['/admin/aportes','◉','Aportes'],['/admin/donaciones-materiales','▣','Donaciones materiales'],['/admin/inventario','▤','Inventario'],['/admin/historias','▧','Historias'],['/admin/voluntariado','✣','Voluntariado'],['/admin/transparencia','◫','Transparencia'],['/admin/gastos','▥','Gastos'],['/admin/configuracion','⚙','Configuración'],['/admin/tema','◐','Identidad visual'],['/admin/auditoria','↻','Auditoría']] as $nav): ?>
        <a class="admin-nav-item <?= (parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_PATH)===$nav[0])?'active':'' ?>" href="<?= e($nav[0]) ?>"><span aria-hidden="true"><?= e($nav[1]) ?></span><?= e($nav[2]) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-bottom"><a href="/" target="_blank" rel="noopener">↗ Abrir sitio público</a>
      <form method="post" action="/admin/logout"><?= csrf_field() ?><button type="submit">Cerrar sesión ↗</button></form></div>
  </aside>
  <div class="admin-main">
    <header class="admin-topbar"><button class="mobile-admin-toggle" type="button" data-admin-menu aria-label="Abrir panel lateral" aria-expanded="false">☰</button><div class="admin-current">Panel de gestión <span> / <?= e($title??'Resumen') ?></span></div><div class="admin-profile"><span class="status-dot"></span><?= e($_SESSION['admin_name']??'Administrador') ?></div></header>
    <main id="contenido" class="admin-content"><?= $content ?></main>
  </div>
</div>
<?php else: ?>
<header class="site-header" data-header>
 <div class="container-shell site-header-inner">
   <a class="brand" href="/" aria-label="Refugio Vida Animal, ir al inicio"><span class="brand-icon" aria-hidden="true">✿</span><span>Refugio <b>Vida Animal</b></span></a>
   <button class="menu-toggle" type="button" data-menu aria-controls="public-nav" aria-expanded="false" aria-label="Abrir menú"><span></span><span></span><span></span></button>
   <nav class="main-nav" id="public-nav" aria-label="Navegación principal">
    <?php foreach([['/','Inicio'],['/nosotros','Nosotros'],['/animales','Animales'],['/adopciones','Adopciones'],['/campanas','Campañas'],['/donaciones','Donaciones'],['/voluntariado','Voluntariado'],['/historias','Historias'],['/transparencia','Transparencia'],['/contacto','Contacto']] as $link): ?>
      <a class="nav-link <?= ((parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/')===$link[0])?'selected':'' ?>" href="<?= e($link[0]) ?>"><?= e($link[1]) ?></a>
    <?php endforeach; ?>
    <a class="admin-entry" href="/admin/login"><span aria-hidden="true">♙</span> Ingreso administrativo</a>
   </nav>
 </div>
</header>
<main id="contenido" class="site-content"><?= $content ?></main>
<footer class="site-footer"><div class="container-shell footer-inner"><div><a class="brand footer-brand" href="/"><span class="brand-icon">✿</span><span>Refugio <b>Vida Animal</b></span></a><p>Rescate, bienestar y adopción responsable.</p></div><div><h3>Explorar</h3><a href="/animales">Animales</a><a href="/historias">Historias</a><a href="/voluntariado">Voluntariado</a></div><div><h3>Colaborar</h3><a href="/donaciones">Cómo ayudar</a><a href="/campanas">Campañas</a><a href="/transparencia">Transparencia</a></div><div><h3>Información</h3><a href="/nosotros">Nuestra misión</a><a href="/contacto">Contacto</a><a href="/privacidad">Privacidad</a></div></div><div class="container-shell footer-note">© <?= date('Y') ?> Refugio Vida Animal. Las cifras públicas se muestran únicamente cuando han sido confirmadas.</div></footer>
<?php endif; ?>
<?php $message=get_flash();if($message): ?><div class="toast" role="status" data-toast><span aria-hidden="true">✓</span><?= e($message) ?><button type="button" data-close-toast aria-label="Cerrar">×</button></div><?php endif; ?>
</body></html>
