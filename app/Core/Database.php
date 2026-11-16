<?php
declare(strict_types=1);
namespace App\Core;
use PDO;
final class Database {
    private static ?PDO $pdo = null;
    public static function connection(): PDO {
        if (self::$pdo === null) {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                \env('DB_HOST','127.0.0.1'), \env('DB_PORT','3306'), \env('DB_NAME','refugio'));
            self::$pdo = new PDO($dsn, \env('DB_USER'), \env('DB_PASSWORD'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false,
            ]);
        }
        return self::$pdo;
    }
}
