<?php
declare(strict_types=1);
namespace App\Services;
final class PublicJournal
{
    public static function all(): array { return require BASE_PATH.'/config/editorial.php'; }
    public static function find(string $slug): ?array { return self::all()[$slug] ?? null; }
}
