<!doctype html>
<html lang="es" class="no-js" data-theme="<?= e(\App\Services\ThemeService::current()['theme']) ?>" data-motion="<?= e(\App\Services\ThemeService::current()['motion']) ?>">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="<?= e(\App\Services\SiteIdentity::settings()['description']) ?>">
<meta name="color-scheme" content="light">
<meta name="application-name" content="<?= e(site_name()) ?>">
<meta name="theme-color" content="#17463d">
<link rel="icon" href="<?= e(versioned_asset('/favicon.ico')) ?>" sizes="any">
<link rel="icon" href="<?= e(versioned_asset('/assets/img/huella-serena.svg')) ?>" type="image/svg+xml">
<link rel="apple-touch-icon" href="/assets/img/huella-serena-180.png">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<title><?= e($title ?? 'Inicio') ?> | <?= e(site_name()) ?></title>
<?php if ((parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH) ?: '/') === '/'): ?><link rel="preload" href="/assets/img/hero-hogar-v2.webp" as="image" fetchpriority="high"><?php endif ?>
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
