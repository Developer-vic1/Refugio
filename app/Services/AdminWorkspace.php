<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Database;

final class AdminWorkspace
{
    private static ?array $summary = null;

    public static function summary(): array
    {
        if (self::$summary !== null) return self::$summary;
        $sql = "SELECT
            (SELECT COUNT(*) FROM animales WHERE archivado=0) animales,
            (SELECT COUNT(*) FROM animales WHERE archivado=0 AND estado_salud='pendiente_evaluacion') salud,
            (SELECT COUNT(*) FROM solicitudes_adopcion WHERE estado IN ('pendiente','en_revision')) adopciones,
            (SELECT COUNT(*) FROM aportes WHERE estado='pendiente') aportes,
            (SELECT COUNT(*) FROM solicitudes_voluntariado WHERE estado='pendiente') voluntariado,
            (SELECT COUNT(*) FROM donaciones_especie WHERE estado IN ('solicitada','coordinando','parcialmente_recibida')) materiales,
            (SELECT COUNT(*) FROM gastos WHERE estado='pendiente') gastos,
            (SELECT COUNT(*) FROM v_inventario_existencias WHERE activo=1 AND existencia_actual<stock_minimo) inventario";
        $counts = array_map('intval', Database::connection()->query($sql)->fetch());
        $counts['pendientes'] = $counts['adopciones'] + $counts['aportes'] + $counts['voluntariado'] + $counts['materiales'] + $counts['gastos'] + $counts['salud'] + $counts['inventario'];
        return self::$summary = $counts;
    }

    public static function dashboard(): array
    {
        $db = Database::connection();
        $agenda = $db->query("SELECT a.id,a.nombre,a.fecha,a.hora_inicio,a.cupos,
            (SELECT COUNT(*) FROM solicitudes_voluntariado s WHERE s.actividad_id=a.id AND s.estado='aprobada') inscritos
            FROM actividades_voluntariado a WHERE a.estado='abierta' AND a.fecha>=CURDATE()
            ORDER BY a.fecha,a.hora_inicio,a.id LIMIT 5")->fetchAll();
        // Resumen operativo sin nombres, contactos ni motivos personales de solicitantes.
        $recent = $db->query("SELECT 'adopciones' modulo,id,estado,creado_en FROM solicitudes_adopcion WHERE estado IN ('pendiente','en_revision')
            UNION ALL SELECT 'aportes',id,estado,creado_en FROM aportes WHERE estado='pendiente'
            UNION ALL SELECT 'voluntariado',id,estado,creado_en FROM solicitudes_voluntariado WHERE estado='pendiente'
            ORDER BY creado_en DESC,id DESC LIMIT 6")->fetchAll();
        $stock = $db->query('SELECT producto_id,nombre,unidad,stock_minimo,existencia_actual FROM v_inventario_existencias WHERE activo=1 AND existencia_actual<stock_minimo ORDER BY (existencia_actual-stock_minimo),producto_id LIMIT 5')->fetchAll();
        return ['counts'=>self::summary(),'agenda'=>$agenda,'recent'=>$recent,'stock'=>$stock];
    }
}
