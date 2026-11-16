<?php
declare(strict_types=1);
namespace App\Support;

/** Iconos propios de trazo uniforme. No requieren JavaScript ni recursos remotos. */
final class Icons
{
    public static function render(string $name): string
    {
        $paths = [
            'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
            'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4m10-4v4M3 11h18m-11 4h.01m4 0h.01M8 18h.01"/>',
            'refresh' => '<path d="M20 7a9 9 0 1 0 1 9M20 3v5h-5"/>',
            'message' => '<path d="M21 11a9 9 0 0 1-9 9H4l-3 3 2-7a9 9 0 1 1 18-5Z"/><path d="M7 10h10M7 14h6"/>',
            'copy' => '<rect x="8" y="8" width="13" height="13" rx="2"/><path d="M16 8V3H3v13h5"/>',
            'leaf' => '<path d="M20 3C7 1 1 9 6 16s17 1 14-13ZM4 21l12-13"/>',
            'home' => '<path d="m2 11 10-9 10 9M5 9v12h14V9M9 21v-7h6v7"/>',
            'shield' => '<path d="m12 2 9 4v6c0 5-6 9-9 10-3-1-9-5-9-10V6l9-4Z"/><path d="m8 12 3 3 5-6"/>',
            'chevron-left' => '<path d="m15 6-6 6 6 6"/>',
            'chevron-right' => '<path d="m9 6 6 6-6 6"/>',
            'paw' => '<ellipse cx="8" cy="6" rx="2" ry="3"/><ellipse cx="16" cy="6" rx="2" ry="3"/><ellipse cx="4" cy="11" rx="2" ry="2.5"/><ellipse cx="20" cy="11" rx="2" ry="2.5"/><path d="M6 19c0-3 3-7 6-7s6 4 6 7c0 4-4 1-6 1s-6 3-6-1Z"/>',
            'grid' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
            'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/>',
            'flag' => '<path d="M4 22V3c5-4 11 4 16 0v12c-5 4-11-4-16 0"/>',
            'coins' => '<ellipse cx="9" cy="6" rx="6" ry="3"/><path d="M3 6v5c0 4 12 4 12 0V6M3 11v5c0 3 7 4 10 2M17 9c5 0 6 5 1 6m-1-1v6c4 0 5-2 5-4v-4"/>',
            'box' => '<path d="m3 7 9-4 9 4v10l-9 4-9-4V7Zm0 0 9 4 9-4M12 11v10M7 5l10 4"/>',
            'book' => '<path d="M3 4h6l3 2 3-2h6v15h-6l-3 2-3-2H3V4Zm9 2v15"/>',
            'users' => '<circle cx="9" cy="7" r="4"/><path d="M2 21v-3a7 7 0 0 1 14 0v3M17 3a4 4 0 0 1 0 8m2 3a6 6 0 0 1 3 5v2"/>',
            'chart' => '<path d="M3 3v18h18M7 16v-4m5 4V7m5 9v-7"/>',
            'settings' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3M5 5l2 2m10 10 2 2M5 19l2-2M17 7l2-2"/>',
            'palette' => '<path d="M12 3a9 9 0 1 0 0 18h2a2 2 0 0 0 0-4h-1a2 2 0 0 1 0-4h4a4 4 0 0 0 4-4c0-4-5-6-9-6Z"/><path d="M7 9h.01M11 6h.01M16 7h.01M6 14h.01"/>',
            'history' => '<path d="M3 11a9 9 0 1 1 2 7M3 4v7h7m2-5v6l4 2"/>',
            'arrow-up-right' => '<path d="M7 17 17 7H7m10 0v10"/>',
            'arrow-left' => '<path d="m12 5-7 7 7 7M5 12h15"/>',
            'arrow-right' => '<path d="m12 5 7 7-7 7M19 12H4"/>',
            'expand' => '<path d="M8 3H3v5m13-5h5v5M3 16v5h5m8 0h5v-5M3 3l6 6m12-6-6 6M3 21l6-6m12 6-6-6"/>',
            'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
            'close' => '<path d="m6 6 12 12M6 18 18 6"/>',
            'lock' => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 5v2"/>',
            'eye' => '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
            'search' => '<circle cx="10" cy="10" r="7"/><path d="m15 15 6 6"/>',
            'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>',
            'alert-circle' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v6m0 4h.01"/>',
            'alert-triangle' => '<path d="m12 3 10 18H2L12 3Zm0 6v5m0 3h.01"/>',
            'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10h.01"/>',
            'loader' => '<path d="M21 12a9 9 0 1 1-9-9"/>',
        ];
        $body = $paths[$name] ?? $paths['info'];
        return '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $body . '</svg>';
    }
}
