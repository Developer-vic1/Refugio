<?php
declare(strict_types=1);
namespace App\Repositories;
use App\Core\Repository;
use App\Support\AnimalFilters;
final class AnimalRepository extends Repository {
  private const PHOTO = "(SELECT f.ruta FROM animal_fotografias f WHERE f.animal_id=a.id ORDER BY f.es_principal DESC, f.orden ASC, f.id ASC LIMIT 1) AS foto";
  public function search(AnimalFilters $filters, int $pageSize = 12): array {
      $pageSize = max(1, min(48, $pageSize));
      $where = ['a.archivado=0'];
      $params = [];
      if ($filters->get('q') !== '') {
          $term = '%'.str_replace(['!','%','_'], ['!!','!%','!_'], $filters->get('q')).'%';
          $parts = [];
          foreach (['nombre','especie','raza','resumen'] as $column) {
              $parts[] = "a.$column LIKE :q_$column ESCAPE '!'";
              $params['q_'.$column] = $term;
          }
          $where[] = '('.implode(' OR ', $parts).')';
      }
      foreach (['especie'=>'especie','sexo'=>'sexo','tamano'=>'tamano','salud'=>'estado_salud','estado'=>'estado_adopcion'] as $key=>$column) {
          if ($filters->get($key) === '') continue;
          $where[] = "a.$column=:$key";
          $params[$key] = $filters->get($key);
      }
      $age = match($filters->get('edad')) {
          'cachorro'=>'a.edad_aproximada_meses < 12',
          'adulto'=>'a.edad_aproximada_meses BETWEEN 12 AND 95',
          'mayor'=>'a.edad_aproximada_meses >= 96',
          'sin_dato'=>'a.edad_aproximada_meses IS NULL',
          default=>null,
      };
      if ($age) $where[] = $age;
      $condition = implode(' AND ', $where);
      $total = (int)$this->one('SELECT COUNT(*) AS total FROM animales a WHERE '.$condition, $params)['total'];
      $pages = max(1, (int)ceil($total / $pageSize));
      $page = min($filters->page, $pages);
      $offset = ($page - 1) * $pageSize;
      $order = match($filters->get('orden')) {
          'nombre'=>'a.nombre ASC,a.id ASC',
          'menor_edad'=>'a.edad_aproximada_meses IS NULL,a.edad_aproximada_meses ASC,a.id ASC',
          'mayor_edad'=>'a.edad_aproximada_meses IS NULL,a.edad_aproximada_meses DESC,a.id ASC',
          default=>'a.creado_en DESC,a.id DESC',
      };
      $items = $this->all('SELECT a.id,a.nombre,a.especie,a.sexo,a.tamano,a.edad_aproximada_meses,a.resumen,a.estado_adopcion,a.estado_salud,'.self::PHOTO.' FROM animales a WHERE '.$condition.' ORDER BY '.$order.' LIMIT '.$pageSize.' OFFSET '.$offset, $params);
      return ['items'=>$items,'total'=>$total,'page'=>$page,'pages'=>$pages,'pageSize'=>$pageSize,'first'=>$total ? $offset + 1 : 0,'last'=>min($offset + $pageSize, $total)];
  }
  public function catalog(string $term='',string $species='',int $limit=24):array {
      return $this->search(AnimalFilters::fromQuery(['q'=>$term,'especie'=>$species,'estado'=>'disponible']), $limit)['items'];
  }
  public function filterOptions(): array {
      $result = array_fill_keys(['especie','sexo','tamano','estado_salud','estado_adopcion'], []);
      foreach ($this->all('SELECT DISTINCT especie,sexo,tamano,estado_salud,estado_adopcion FROM animales WHERE archivado=0') as $row) {
          foreach ($result as $key=>$_) if ($row[$key] !== null && $row[$key] !== '') $result[$key][$row[$key]] = $row[$key];
      }
      foreach ($result as &$values) { natcasesort($values); $values = array_values($values); }
      unset($values);
      return $result;
  }
  public function find(int $id):array|false {
      return $this->one('SELECT a.id,a.nombre,a.especie,a.raza,a.sexo,a.tamano,a.edad_aproximada_meses,a.resumen,a.historia,a.estado_salud,a.estado_adopcion,a.fecha_rescate,'.self::PHOTO.' FROM animales a WHERE a.id=:id AND a.archivado=0',['id'=>$id]);
  }
  public function photos(int $id):array {return $this->all('SELECT f.ruta,f.texto_alternativo FROM animal_fotografias f JOIN animales a ON a.id=f.animal_id WHERE f.animal_id=:id AND a.archivado=0 ORDER BY f.es_principal DESC,f.orden,f.id',['id'=>$id]);}
  public function available(int $id):bool {return (bool)$this->one("SELECT id FROM animales WHERE id=:id AND estado_adopcion='disponible' AND archivado=0",['id'=>$id]);}
}
