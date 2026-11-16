<?php
declare(strict_types=1);
namespace App\Support;

final class AnimalPresenter
{
    public static function age(?int $months): string
    {
        if ($months === null) return 'Edad por confirmar';
        if ($months < 12) return $months === 0 ? 'Menos de un mes' : $months.' '.($months === 1 ? 'mes' : 'meses');
        $years = intdiv($months, 12);
        $remaining = $months % 12;
        return $years.' '.($years === 1 ? 'año' : 'años').($remaining ? ' y '.$remaining.' '.($remaining === 1 ? 'mes' : 'meses') : '');
    }
    public static function statusType(string $status): string { return match($status) {'disponible'=>'success','en_evaluacion'=>'warning','adoptado'=>'info',default=>'neutral'}; }
    public static function options(array $values, array $labels = [], string $all = 'Todas las opciones'): array
    {
        $options = [['value'=>'','label'=>$all]];
        foreach ($values as $value) $options[] = ['value'=>$value,'label'=>$labels[$value] ?? ucfirst($value)];
        return $options;
    }
}
