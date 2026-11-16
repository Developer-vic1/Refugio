<?php
declare(strict_types=1);
const BASE_PATH = __DIR__;
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $relative = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require $file;
});

$envFile = BASE_PATH . '/.env';
if (is_file($envFile)) {
    $pairs = parse_ini_file($envFile, false, INI_SCANNER_RAW);
    if ($pairs === false) throw new RuntimeException('Archivo .env inválido');
    foreach ($pairs as $key => $value) {
        if (getenv((string)$key) === false) putenv($key . '=' . $value);
    }
}

function env(string $key, string $default = ''): string {
    $value = getenv($key);
    return $value === false ? $default : $value;
}
if (!function_exists('mb_strlen')) { function mb_strlen(string $s): int { return strlen($s); } }
if (!function_exists('mb_substr')) { function mb_substr(string $s,int $start,int $length): string { return substr($s,$start,$length); } }
function e(mixed $value): string { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path = ''): string { return '/' . ltrim($path, '/'); }
function asset(?string $dbPath): string {
    $path = (string)$dbPath;
    if (preg_match('~^(animales|historias|refugio)/([a-f0-9]{32}\.(?:jpg|png|webp))$~D', $path,$m)) return '/media/'.$m[1].'/'.$m[2];
    return '/assets/img/placeholder.svg';
}
function redirect(string $path, int $code = 303): never {
    header('Location: ' . url($path), true, $code); exit;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="'.e(csrf_token()).'">'; }
function verify_csrf(): void {
    $submitted = $_POST['_csrf'] ?? null;
    if (!is_string($submitted) || !hash_equals(csrf_token(), $submitted)) {
        http_response_code(419); exit('Formulario expirado. Actualiza la página.');
    }
}
function flash(string $message): void { $_SESSION['flash'] = $message; }
function get_flash(): ?string { $message = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $message; }
function page(string $view, array $data = [], int $status = 200): void {
    http_response_code($status);
    $file = BASE_PATH . '/app/Views/' . $view . '.php';
    if (!is_file($file)) throw new RuntimeException('Vista no encontrada: ' . $view);
    extract($data, EXTR_SKIP);
    ob_start(); require $file; $content = (string)ob_get_clean();
    require BASE_PATH . '/app/Views/layouts/main.php';
}
$tz = env('APP_TIMEZONE', 'America/La_Paz');
date_default_timezone_set($tz);
if (PHP_SAPI !== 'cli' || isset($_SERVER['REQUEST_METHOD'])) {
    ini_set('session.use_strict_mode', '1');
    session_name('refugio_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'httponly' => true,
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'samesite' => 'Lax'
    ]);
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
}
