<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Database;
final class AuditService {
    public static function record(string $action,string $table,int $id=0,array $details=[]):void {
        $admin=(int)($_SESSION['admin_id']??0);
        if($admin<1)return;
        $ip=substr((string)($_SERVER['REMOTE_ADDR']??''),0,45);
        $sql='INSERT INTO auditoria_administrativa(administrador_id,accion,tabla_afectada,registro_id,detalle,ip) VALUES (?,?,?,?,?,?)';
        Database::connection()->prepare($sql)->execute([$admin,substr($action,0,60),substr($table,0,80),$id>0?$id:null,json_encode($details,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$ip?:null]);
    }
}
