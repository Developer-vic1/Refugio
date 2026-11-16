<?php
declare(strict_types=1);
namespace App\Services;
final class PublicInitiatives
{
    public static function all(): array { return require BASE_PATH.'/config/initiatives.php'; }
    public static function find(string $slug): ?array { return self::all()[$slug] ?? null; }
    public static function options(): array
    {
        $options=[['value'=>'','label'=>'Quiero conversar primero']];
        foreach(self::all() as $slug=>$item)$options[]=['value'=>$slug,'label'=>$item['title']];
        return $options;
    }
}
