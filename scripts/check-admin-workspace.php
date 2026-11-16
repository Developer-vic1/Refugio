<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';

$workspace = \App\Services\AdminWorkspace::dashboard();
if (!isset($workspace['counts']['inventario'], $workspace['counts']['pendientes'])) throw new RuntimeException('Resumen incompleto');
foreach ($workspace['counts'] as $value) if (!is_int($value) || $value < 0) throw new RuntimeException('Contador inválido');
foreach ($workspace['recent'] as $record) {
    if (array_diff(array_keys($record), ['modulo','id','estado','creado_en'])) throw new RuntimeException('El resumen incluye campos privados');
}
$router = require BASE_PATH.'/routes/web.php';
$property = new ReflectionProperty($router, 'routes');
$routes = $property->getValue($router);
foreach (\App\Support\AdminNavigation::groups() as $links) foreach ($links as $link) {
    $found = false;
    foreach ($routes as [$method,$path,$regex,$handler,$auth]) {
        if ($method === 'GET' && $path === $link['href']) { $found = true; if (!$auth) throw new RuntimeException('Módulo sin autorización'); }
    }
    if (!$found) throw new RuntimeException('Ruta inexistente en navegación');
}
$_SERVER['REQUEST_URI'] = '/admin/animales/12/editar';
$crumbs = \App\Support\AdminNavigation::breadcrumbs('Editar animal');
if ($crumbs !== [['label'=>'Panel','href'=>'/admin'],['label'=>'Animales y fotografías','href'=>'/admin/animales'],['label'=>'Editar animal']]) throw new RuntimeException('Breadcrumb de subruta');
echo "Panel: consultas de lectura, privacidad del resumen, rutas protegidas y breadcrumbs correctos.\n";
