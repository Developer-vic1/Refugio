<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
ini_set('display_errors', '0');
try {
    require dirname(__DIR__).'/bootstrap.php';
    $extensions = ['pdo_mysql', 'dom', 'mbstring', 'gd', 'fileinfo', 'openssl'];
    $missing = array_values(array_filter($extensions, static fn(string $name): bool => !extension_loaded($name)));
    $database = !$missing && \App\Support\LocalRuntime::databaseAvailable();
    $profile = null;
    if ($database && in_array(env('DB_HOST', '127.0.0.1'), ['127.0.0.1', 'localhost', '::1'], true)) {
        $profile = \App\Core\Database::connection()->query('SELECT @@basedir AS base_dir, @@datadir AS data_dir, @@port AS port, @@version AS version')->fetch();
    }
    echo json_encode([
        'instance' => \App\Support\LocalRuntime::instance(),
        'php_ready' => PHP_VERSION_ID >= 80200,
        'missing_extensions' => $missing,
        'environment' => is_file(BASE_PATH.'/.env'),
        'dependencies' => class_exists(\Dompdf\Dompdf::class),
        'database' => $database,
        'database_local' => in_array(env('DB_HOST', '127.0.0.1'), ['127.0.0.1', 'localhost', '::1'], true),
        'database_port' => (int)env('DB_PORT', '3306'),
        'mysql_profile' => $profile,
    ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
} catch (Throwable) {
    echo '{"error":"No se pudo leer la configuración local de Refugio."}';
    exit(1);
}
