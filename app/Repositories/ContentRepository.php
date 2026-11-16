<?php
declare(strict_types=1);
namespace App\Repositories;
use App\Core\Repository;
final class ContentRepository extends Repository {
   public function config():array {return $this->one('SELECT * FROM refugio_configuracion WHERE id=1') ?: [];}
   public function stories():array {return $this->all('SELECT h.id,h.titulo,h.descripcion,h.fecha_evento,a.nombre AS animal FROM historias h JOIN animales a ON a.id=h.animal_id WHERE h.publicada=1 AND a.archivado=0 ORDER BY h.fecha_evento DESC,h.id DESC LIMIT 30');}
   public function story(int $id):array|false {return $this->one('SELECT h.id,h.titulo,h.descripcion,h.fecha_evento,a.nombre AS animal FROM historias h JOIN animales a ON a.id=h.animal_id WHERE h.id=:id AND h.publicada=1 AND a.archivado=0',['id'=>$id]);}
   public function activities():array {return $this->all("SELECT id,nombre,descripcion,fecha,hora_inicio,cupos,lugar FROM actividades_voluntariado WHERE estado='abierta' AND fecha >= CURRENT_DATE ORDER BY fecha LIMIT 30");}
   public function activity(int $id):array|false {return $this->one("SELECT id,nombre,descripcion,fecha,hora_inicio,cupos,lugar,requisitos FROM actividades_voluntariado WHERE id=:id AND estado='abierta' AND fecha >= CURRENT_DATE",['id'=>$id]);}
   public function reports():array {return $this->all('SELECT id,titulo,resumen,periodo_inicio,periodo_fin FROM reportes_transparencia WHERE publicado=1 ORDER BY periodo_fin DESC LIMIT 30');}
   public function report(int $id):array|false {return $this->one('SELECT id,titulo,resumen,periodo_inicio,periodo_fin,uso_reportado FROM reportes_transparencia WHERE publicado=1 AND id=:id',['id'=>$id]);}
   public function transparency():array {return $this->one('SELECT ingresos_confirmados,gastos_confirmados,saldo_registrado FROM v_transparencia_general') ?: [] ;}
}
