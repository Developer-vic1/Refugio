<?php
declare(strict_types=1);
namespace App\Core;
use PDO;
final class Auth {
    public static function check(): bool {
        if (empty($_SESSION['admin_id'])) return false;
        if (time() - (int)($_SESSION['last_seen'] ?? 0) > 1800) {
            self::logout();return false;
        }
        // Invalidar sesiones si la cuenta fue desactivada.
        $q = Database::connection()->prepare('SELECT activo FROM administradores WHERE id=:id');
        $q->execute(['id'=>(int)$_SESSION['admin_id']]);
        if ((int)$q->fetchColumn() !== 1) { self::logout(); return false; }
        $_SESSION['last_seen'] = time();
        return true;
    }
    public static function login(array $admin): void {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$admin['id'];
        $_SESSION['admin_name'] = (string)$admin['nombre_completo'];
        $_SESSION['last_seen'] = time();
    }
    public static function logout(): void {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time()-3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            session_destroy();
        }
    }
    public static function verifyPbkdf2(string $password,string $stored): bool {
        $parts = explode('$',$stored);
        if (count($parts)!==4 || $parts[0]!=='pbkdf2_sha256') return false;
        $iterations = filter_var($parts[1], FILTER_VALIDATE_INT);
        if ($iterations===false || $iterations<100000 || $iterations>1500000) return false;
        $salt = base64_decode($parts[2],true);
        $hash = base64_decode($parts[3],true);
        if ($salt===false || strlen($salt)!==16 || $hash===false || strlen($hash)!==32) return false;
        return hash_equals($hash, hash_pbkdf2('sha256',$password,$salt,$iterations,32,true));
    }
}
