<?php
declare(strict_types=1);
require dirname(__DIR__, 2).'/bootstrap.php';
$workspace = \App\Services\AdminWorkspace::dashboard();
$title = 'Vista de componentes administrativos';
$originalSession = $_SESSION ?? [];
$_SESSION['admin_name'] = 'Vista de componentes';
ob_start();
echo component('alert', ['type'=>'info','message'=>'Revisión aislada del layout. Solo lectura de contadores locales; no inicia sesión ni ejecuta operaciones.']);
require BASE_PATH.'/app/Views/admin/dashboard.php';
$content = (string)ob_get_clean();
?>
<!doctype html><html lang="es" class="no-js" data-theme="bosque" data-motion="equilibrada"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Verificación del panel</title><link rel="stylesheet" href="/assets/css/tailwind.css"><link rel="stylesheet" href="<?= e(versioned_asset('/assets/css/app.css')) ?>"><script type="module" src="<?= e(versioned_asset('/assets/js/app.js')) ?>"></script></head><body class="mode-admin">
<?php require BASE_PATH.'/app/Views/layouts/admin.php'; ?>
<?= component('confirm-dialog') ?>
</body></html>
<?php $_SESSION = $originalSession; ?>
