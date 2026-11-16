<?php
declare(strict_types=1);
namespace App\Repositories;
use App\Core\Repository;
use PDO;
final class AnimalRepository extends Repository {
  private const PHOTO = "(SELECT f.ruta FROM animal_fotografias f WHERE f.animal_id=a.id ORDER BY f.es_principal DESC, f.orden ASC, f.id ASC LIMIT 1) AS foto";
  public function catalog(string $term='',string $species='',int $limit=24):array {
      $where=['a.archivado=0','a.estado_adopcion IN (\'disponible\',\'en_evaluacion\',\'no_disponible\')'];$params=[];
      if ($term!=='') { $where[]='(a.nombre LIKE :term OR a.especie LIKE :species_term)';$params['term']='%'.$term.'%';$params['species_term']='%'.$term.'%'; }
      if ($species!=='') {$where[]='a.especie=:species';$params['species']=$species;}
      $sql='SELECT a.id,a.nombre,a.especie,a.edad_aproximada_meses,a.resumen,a.estado_adopcion,'.self::PHOTO.' FROM animales a WHERE '.implode(' AND ',$where).' ORDER BY a.creado_en DESC,a.id DESC LIMIT '.(int)$limit;
      return $this->all($sql,$params);
  }
  public function find(int $id):array|false {
      return $this->one('SELECT a.id,a.nombre,a.especie,a.raza,a.sexo,a.tamano,a.edad_aproximada_meses,a.resumen,a.historia,a.estado_salud,a.estado_adopcion,a.fecha_rescate,'.self::PHOTO.' FROM animales a WHERE a.id=:id AND a.archivado=0',['id'=>$id]);
  }
  public function photos(int $id):array {return $this->all('SELECT ruta,texto_alternativo FROM animal_fotografias WHERE animal_id=:id ORDER BY es_principal DESC, orden,id',['id'=>$id]);}
  public function available(int $id):bool {return (bool)$this->one("SELECT id FROM animales WHERE id=:id AND estado_adopcion='disponible' AND archivado=0",['id'=>$id]);}
}
