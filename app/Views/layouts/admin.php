<?php
$navCounts = \App\Services\AdminWorkspace::summary();
$navGroups = \App\Support\AdminNavigation::groups();
?>
<div class="admin-shell" data-admin-shell>
    <aside class="admin-sidebar" id="admin-sidebar" aria-label="Secciones administrativas" tabindex="-1">
        <div class="admin-brand-row"><a class="brand admin-brand" href="/admin"><span class="brand-icon" aria-hidden="true"><?= icon('paw') ?></span><span class="sidebar-label"><?= e(\App\Services\SiteIdentity::brandParts()[0]) ?> <b><?= e(\App\Services\SiteIdentity::brandParts()[1]) ?></b><small>Espacio de cuidado</small></span></a><button type="button" class="icon-button admin-sidebar-close" data-admin-close aria-label="Cerrar panel lateral"><?= icon('close') ?></button></div>
        <div class="admin-nav-search"><label for="admin-nav-search" class="sr-only">Buscar un módulo</label><?= icon('search') ?><input id="admin-nav-search" type="search" placeholder="Buscar un módulo…" data-nav-search autocomplete="off"></div>
        <nav class="admin-menu" aria-label="Navegación administrativa">
            <?php foreach ($navGroups as $group=>$links): $hasActive = false; foreach ($links as $link) if (nav_active($link['href'])) $hasActive = true; ?>
            <details class="admin-nav-group" data-nav-group data-active="<?= $hasActive ? 'true' : 'false' ?>" open>
                <summary><span><?= e($group) ?></span><?= icon('chevron-down') ?></summary>
                <div class="admin-nav-links">
                <?php foreach ($links as $link): $count = isset($link['count']) ? ($navCounts[$link['count']] ?? 0) : null; ?>
                    <a class="admin-nav-item <?= nav_active($link['href']) ? 'active' : '' ?>" href="<?= e($link['href']) ?>" aria-label="<?= e($link['label'].($count !== null && $count > 0 ? ', '.$count.' pendientes' : '')) ?>" title="<?= e($link['label']) ?>" <?= nav_active($link['href']) ? 'aria-current="page"' : '' ?> data-nav-label="<?= e($link['label']) ?>">
                        <?= icon($link['icon']) ?><span class="sidebar-label"><?= e($link['label']) ?></span>
                        <?php if ($count !== null && $count > 0): ?><span class="nav-count" aria-label="<?= (int)$count ?> pendientes"><?= $count > 99 ? '99+' : (int)$count ?></span><?php endif ?>
                    </a>
                <?php endforeach ?>
                </div>
            </details>
            <?php endforeach ?>
            <p class="admin-nav-empty" data-nav-empty hidden role="status">No hay módulos con ese nombre.</p>
        </nav>
        <div class="sidebar-bottom"><a href="/" target="_blank" rel="noopener"><?= icon('arrow-up-right') ?> <span class="sidebar-label">Ver sitio público</span></a><form method="post" action="/admin/logout" data-confirm="¿Quieres cerrar la sesión administrativa?"><?= csrf_field() ?><button type="submit" aria-label="Cerrar sesión"><?= icon('lock') ?><span class="sidebar-label">Cerrar sesión</span></button></form></div>
    </aside>
    <button type="button" class="admin-sidebar-overlay" data-admin-overlay aria-label="Cerrar navegación" hidden tabindex="-1"></button>
    <div class="admin-main">
        <header class="admin-topbar">
            <button class="icon-button admin-menu-control" type="button" data-admin-menu aria-label="Abrir panel lateral" aria-expanded="false" aria-controls="admin-sidebar"><?= icon('menu') ?></button>
            <div class="admin-path"><?= component('breadcrumbs', ['items'=>\App\Support\AdminNavigation::breadcrumbs($title ?? 'Panel')]) ?></div>
            <div class="admin-topbar-actions"><a class="admin-pending-link" href="/admin/pendientes" aria-label="Pendientes: <?= (int)$navCounts['pendientes'] ?>"><?= icon('alert-circle') ?><span><?= (int)$navCounts['pendientes'] ?></span></a><div class="admin-profile"><span class="admin-avatar" aria-hidden="true"><?= icon('users') ?></span><span><?= e(explode(' ', (string)($_SESSION['admin_name'] ?? 'Administrador'))[0]) ?><small>Administración</small></span></div></div>
        </header>
        <main id="contenido" class="admin-content" tabindex="-1"><?= $content ?></main>
    </div>
</div>
