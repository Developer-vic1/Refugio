<?php
declare(strict_types=1);
namespace App\Services;

final class ContentVisibility
{
    public static function published(): bool { return \env('PUBLIC_CONTENT_ENABLED', 'false') === 'true'; }

    public static function localReview(): bool
    {
        if (self::published() || \env('APP_ENV', 'production') !== 'local' || \env('LOCAL_REVIEW_MODE', 'true') !== 'true') return false;
        if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1', '::ffff:127.0.0.1'], true)) return false;
        if (isset($_SERVER['HTTP_FORWARDED']) || isset($_SERVER['HTTP_X_FORWARDED_FOR'])) return false;
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
        return (bool)preg_match('~^(localhost|127\.0\.0\.1|\[::1\]|refugio\.test)(:\d{1,5})?$~D', $host);
    }

    public static function visible(): bool { return self::published() || self::localReview(); }
}
