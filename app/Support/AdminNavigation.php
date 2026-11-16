<?php
declare(strict_types=1);
namespace App\Support;

final class AdminNavigation
{
    public static function groups(): array
    {
        return [
            'General'=>[
                ['href'=>'/admin','icon'=>'grid','label'=>'Panel principal'],
                ['href'=>'/admin/pendientes','icon'=>'alert-circle','label'=>'Pendientes','count'=>'pendientes'],
            ],
            'Gestión del refugio'=>[
                ['href'=>'/admin/animales','icon'=>'paw','label'=>'Animales y fotografías'],
                ['href'=>'/admin/adopciones','icon'=>'heart','label'=>'Adopciones','count'=>'adopciones'],
                ['href'=>'/admin/historias','icon'=>'book','label'=>'Historias'],
            ],
            'Colaboración y recursos'=>[
                ['href'=>'/admin/campanas','icon'=>'flag','label'=>'Campañas'],
                ['href'=>'/admin/aportes','icon'=>'coins','label'=>'Aportes','count'=>'aportes'],
                ['href'=>'/admin/donaciones-materiales','icon'=>'box','label'=>'Donaciones materiales','count'=>'materiales'],
                ['href'=>'/admin/inventario','icon'=>'box','label'=>'Inventario','count'=>'inventario'],
                ['href'=>'/admin/voluntariado','icon'=>'users','label'=>'Voluntariado','count'=>'voluntariado'],
            ],
            'Control y reportes'=>[
                ['href'=>'/admin/transparencia','icon'=>'chart','label'=>'Transparencia'],
                ['href'=>'/admin/gastos','icon'=>'coins','label'=>'Gastos','count'=>'gastos'],
                ['href'=>'/admin/auditoria','icon'=>'history','label'=>'Auditoría'],
            ],
            'Sistema'=>[
                ['href'=>'/admin/configuracion','icon'=>'settings','label'=>'Configuración'],
                ['href'=>'/admin/tema','icon'=>'palette','label'=>'Identidad visual'],
            ],
        ];
    }

    public static function breadcrumbs(string $title): array
    {
        $items = [['label'=>'Panel','href'=>'/admin']];
        foreach (self::groups() as $links) foreach ($links as $link) {
            if ($link['href'] === '/admin' || !\nav_active($link['href'])) continue;
            $items[] = ['label'=>$link['label'],'href'=>$link['href']];
            break 2;
        }
        $path = rtrim((string)(parse_url($_SERVER['REQUEST_URI'] ?? '/admin', PHP_URL_PATH)), '/');
        if (count($items) === 1 && $path === '/admin') return [['label'=>'Panel principal']];
        if (($items[count($items)-1]['href'] ?? '') === $path) unset($items[count($items)-1]['href']);
        else $items[] = ['label'=>$title];
        return $items;
    }
}
