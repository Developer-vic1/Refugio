<?php
declare(strict_types=1);
namespace App\Support;

final class Ui
{
    public static function noticeType(string $type): string
    {
        return in_array($type, ['success', 'error', 'warning', 'info', 'progress'], true) ? $type : 'info';
    }

    public static function noticeIcon(string $type): string
    {
        return ['success' => 'check-circle', 'error' => 'alert-circle', 'warning' => 'alert-triangle', 'progress' => 'loader'][$type] ?? 'info';
    }

    public static function localHref(string $href): string
    {
        return preg_match('~^/(?!/)[^\x00-\x20\\\\]*$~D', $href) ? $href : '/';
    }
}
