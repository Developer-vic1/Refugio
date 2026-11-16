<?php
declare(strict_types=1);
namespace App\Repositories;
use App\Core\Repository;
final class CampaignRepository extends Repository {
    public function active():array {return $this->all("SELECT campana_id AS id,titulo,tipo,meta_monto,monto_confirmado,monto_utilizado,saldo_campana FROM v_campanas_resumen WHERE estado='activa' ORDER BY campana_id DESC LIMIT 30");}
    public function find(int $id):array|false {return $this->one("SELECT campana_id AS id,titulo,tipo,meta_monto,monto_confirmado,monto_utilizado,saldo_campana FROM v_campanas_resumen WHERE campana_id=:id AND estado='activa'",['id'=>$id]);}
    public function donationsEnabled():bool { $c=$this->one('SELECT donaciones_habilitadas FROM refugio_configuracion WHERE id=1');return $c && (int)$c['donaciones_habilitadas']===1; }
}
