<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';

$checks = 0;
function check(bool $condition, string $label): void {
    global $checks;
    if (!$condition) throw new RuntimeException($label);
    $checks++;
}

foreach (['success','error','warning','info','progress'] as $type) {
    $markup = component('toast', ['type'=>$type, 'message'=>'<script>alert(1)</script>']);
    check(str_contains($markup, 'notice-'.$type), 'Tipo de notificación');
    check(!str_contains($markup, '<script>'), 'Escape de notificaciones');
    check(str_contains($markup, $type === 'error' ? 'role="alert"' : 'role="status"'), 'Semántica ARIA');
}
check(\App\Support\Ui::noticeType('otro') === 'info', 'Tipo desconocido');
foreach (['javascript:alert(1)', '//example.com', '/\\example.com', "/\nexample.com"] as $href) {
    check(\App\Support\Ui::localHref($href) === '/', 'Enlace externo bloqueado');
}
check(\App\Support\Ui::localHref('/animales?q=gato') === '/animales?q=gato', 'Enlace local');
$_SERVER['REQUEST_URI'] = '/admin/animales/12/editar?tab=fotos';
check(nav_active('/admin/animales') && !nav_active('/admin'), 'Subruta activa');
$_SERVER['REQUEST_URI'] = '/animales-extra';
check(!nav_active('/animales'), 'Límite de segmentos');
$_SESSION = [];
flash('Falló', 'error');
check(take_flash() === ['message'=>'Falló','type'=>'error'] && take_flash() === null, 'Flash de un solo uso');
$_SESSION['flash'] = 'Mensaje anterior';
check(get_flash() === 'Mensaje anterior', 'Compatibilidad con mensajes V1');
$password = component('form-field', ['name'=>'password', 'type'=>'password', 'value'=>'NO_MOSTRAR', 'label'=>'Clave', 'hint'=>'Ayuda', 'error'=>'Error']);
check(!str_contains($password, 'NO_MOSTRAR'), 'No repoblar contraseña');
check(str_contains($password, 'aria-invalid="true"') && str_contains($password, 'password-hint password-error'), 'Error asociado al campo');
$button = component('button', ['label'=>'<img src=x>', 'href'=>'javascript:alert(1)']);
check(!str_contains($button, '<img') && str_contains($button, 'href="/"'), 'Botón seguro');
try { component('../layouts/main'); check(false, 'Ruta de componente inválida'); }
catch (InvalidArgumentException) { check(true, 'Lista de componentes permitidos'); }
foreach (['badge','empty-state','breadcrumbs','progress','skeleton','confirm-dialog'] as $name) {
    check(component($name, []) !== '', 'Renderizar '.$name);
}
echo "UI: {$checks} comprobaciones correctas.\n";
