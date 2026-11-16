<?php
declare(strict_types=1);
namespace App\Support;

/** Identificación del servidor de desarrollo; nunca entrega configuración privada por HTTP. */
final class LocalRuntime
{
    public static function instance(): string
    {
        return substr(hash('sha256', strtolower(str_replace('\\', '/', BASE_PATH))), 0, 24);
    }

    public static function databaseAvailable(): bool
    {
        try {
            $host = \env('DB_HOST', '127.0.0.1');
            $socket = @stream_socket_client('tcp://'.($host === '::1' ? '[::1]' : $host).':'.\env('DB_PORT', '3306'), $errno, $error, 1);
            if ($socket === false) return false;
            fclose($socket);
            return \App\Core\Database::connection()->query('SELECT 1')->fetchColumn() !== false;
        } catch (\Throwable) {
            return false;
        }
    }
}
