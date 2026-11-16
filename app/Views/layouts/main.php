<!doctype html>
<html lang="es" class="no-js" data-theme="<?= e(\App\Services\ThemeService::current()['theme']) ?>" data-motion="<?= e(\App\Services\ThemeService::current()['motion']) ?>">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="Refugio Vida Animal: rescate, cuidado, adopciones y solidaridad con información responsable.">
<meta name="color-scheme" content="light">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<title><?= e($title ?? 'Inicio') ?> | Refugio Vida Animal</title>
<link rel="preload" href="/assets/img/hero-rescate.webp" as="image" fetchpriority="high">
<link rel="stylesheet" href="/assets/css/tailwind.css">
<link rel="stylesheet" href="<?= e(versioned_asset('/assets/css/app.css')) ?>">
<script type="module" src="<?= e(versioned_asset('/assets/js/app.js')) ?>"></script>
</head>
<?php $isAdmin = str_starts_with((string)(parse_url($_SERVER['REQUEST_URI']??'/', PHP_URL_PATH)??'/'),'/admin'); $authenticated = $isAdmin && \App\Core\Auth::check(); ?>
<body class="<?= $isAdmin?'mode-admin':'mode-public' ?>">
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<div class="scroll-progress" aria-hidden="true"></div>
<?php require BASE_PATH.'/app/Views/layouts/'.($authenticated ? 'admin' : ($isAdmin ? 'auth' : 'public')).'.php'; ?>
<?php $notice = take_flash(); if ($notice): ?><?= component('toast', $notice) ?><?php endif ?>
<?= component('confirm-dialog') ?>
</body></html>
