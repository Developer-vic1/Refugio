<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($path === '/__refugio/health') {
    // Solo para el iniciador local; no se expone en las rutas del sitio ni en Apache.
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) ||
        !preg_match('/^(?:127\.0\.0\.1|localhost)(?::\d+)?$/D', $_SERVER['HTTP_HOST'] ?? '') ||
        isset($_SERVER['HTTP_FORWARDED']) || isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        http_response_code(404); echo '{}'; return true;
    }
    require dirname(__DIR__).'/bootstrap.php';
    echo json_encode(['app' => 'huella-serena', 'version' => '2', 'instance' => \App\Support\LocalRuntime::instance(), 'database' => \App\Support\LocalRuntime::databaseAvailable()]);
    return true;
}
$file = __DIR__ . $path;
if ($path !== '/' && is_file($file) && !str_ends_with($file, '.php')) return false;
require __DIR__ . '/index.php';
