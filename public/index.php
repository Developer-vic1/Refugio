<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'none'; form-action 'self'; img-src 'self' data: blob:; script-src 'self'; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com");
if (\env('APP_ENV','local') !== 'local') ini_set('display_errors','0');
try { $router=require dirname(__DIR__).'/routes/web.php';$router->dispatch($_SERVER['REQUEST_METHOD']??'GET',$_SERVER['REQUEST_URI']??'/'); }
catch (\PDOException $e) {
    error_log('[refugio-db] '.$e->getMessage());
    \page('pages/status',['title'=>'Servicio temporalmente no disponible','message'=>'No fue posible conectar con la base de datos. Revisa la configuración del servidor.'],503);
}
catch (\Throwable $e) {
    error_log('[refugio-error] '.$e);
    \page('pages/status',['title'=>'Se produjo un error','message'=>\env('APP_DEBUG','false')==='true'?$e->getMessage():'Inténtalo de nuevo más tarde.'],500);
}
