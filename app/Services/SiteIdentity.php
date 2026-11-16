<?php
declare(strict_types=1);
namespace App\Services;

final class SiteIdentity
{
    private static ?array $settings = null;
    public static function settings(): array { return self::$settings ??= require BASE_PATH.'/config/site.php'; }
    public static function name(): string { return (string)self::settings()['name']; }
    public static function brandParts(): array { return explode(' ',self::name(),2)+[0=>'',1=>'']; }
    public static function whatsapp(?string $message = null): string
    {
        $message ??= 'Hola, quiero conocer más sobre '.self::name().'.';
        return 'https://wa.me/'.self::settings()['whatsapp'].'?text='.rawurlencode($message);
    }
    public static function telephone(): string { return 'tel:+'.self::settings()['whatsapp']; }
    public static function phone(): string { return (string)self::settings()['phone_display']; }
}
