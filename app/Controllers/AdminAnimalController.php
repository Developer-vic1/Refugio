<?php
 declare(strict_types=1);
 namespace App\Controllers;
 use App\Core\Database;
 use App\Services\MediaStorage;
 use DomainException;
 use PDO;
 final class AdminAnimalController {
   private function find(int $id):array|false {$st=Database::connection()->prepare('SELECT * FROM animales WHERE id=?');$st->execute([$id]);return $st->fetch(PDO::FETCH_ASSOC);}
   private function data():array {
      $name=trim((string)($_POST['nombre']??''));$species=trim((string)($_POST['especie']??''));$summary=trim((string)($_POST['resumen']??''));
      if($name===''||mb_strlen($name)>80||$species===''||mb_strlen($species)>50||$summary===''||mb_strlen($summary)>255) throw new DomainException('Completa nombre, especie y resumen dentro de los límites');
      $sex=(string)($_POST['sexo']??'desconocido');$health=(string)($_POST['estado_salud']??'pendiente_evaluacion');$adoption=(string)($_POST['estado_adopcion']??'no_disponible');$size=(string)($_POST['tamano']??'no_determinado');
      if(!in_array($sex,['macho','hembra','desconocido'],true)||!in_array($health,['pendiente_evaluacion','en_tratamiento','en_recuperacion','estable'],true)||!in_array($adoption,['no_disponible','disponible','en_evaluacion','adoptado'],true)||!in_array($size,['pequeno','mediano','grande','no_determinado'],true)) throw new DomainException('Estado inválido');
      $age=trim((string)($_POST['edad_aproximada_meses']??''));
      if($age!==''&&(!ctype_digit($age)||(int)$age>1200))throw new DomainException('Edad en meses inválida');
      $date=trim((string)($_POST['fecha_rescate']??''));
      if($date!==''&&(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)||strtotime($date)===false))throw new DomainException('Fecha no válida');
      return ['nombre'=>$name,'especie'=>$species,'raza'=>self::optional('raza',80),'sexo'=>$sex,'edad_aproximada_meses'=>$age===''?null:(int)$age,'tamano'=>$size,'fecha_rescate'=>$date===''?null:$date,'lugar_rescate'=>self::optional('lugar_rescate',150),'resumen'=>$summary,'historia'=>self::optional('historia',50000),'estado_salud'=>$health,'estado_adopcion'=>$adoption];
   }
   private static function optional(string $k,int $max):?string{$s=trim((string)($_POST[$k]??''));if(mb_strlen($s)>$max)throw new DomainException('Campo demasiado largo: '.$k);return $s===''?null:$s;}
   public function index():void {$rows=Database::connection()->query('SELECT a.*, (SELECT ruta FROM animal_fotografias f WHERE f.animal_id=a.id ORDER BY es_principal DESC, orden, id LIMIT 1) foto FROM animales a ORDER BY a.id DESC LIMIT 100')->fetchAll();\page('admin/animals',['title'=>'Animales','rows'=>$rows]);}
   public function create():void {\page('admin/animal-form',['title'=>'Registrar animal','animal'=>[],'photos'=>[]]);}
   public function edit(string $id):void {$animal=$this->find((int)$id);if(!$animal){http_response_code(404);return;}$st=Database::connection()->prepare('SELECT * FROM animal_fotografias WHERE animal_id=? ORDER BY es_principal DESC,orden,id');$st->execute([(int)$id]);\page('admin/animal-form',['title'=>'Editar animal','animal'=>$animal,'photos'=>$st->fetchAll()]);}
   public function store():void {
      try{$data=$this->data();$data['creado_por']=(int)$_SESSION['admin_id'];$fields=array_keys($data);$stmt=Database::connection()->prepare('INSERT INTO animales (`'.implode('`,`',$fields).'`) VALUES (:'.implode(',:',$fields).')');$stmt->execute($data);$id=(int)Database::connection()->lastInsertId();\App\Services\AuditService::record('crear_animal','animales',$id);\flash('Animal registrado.');\redirect('/admin/animales/'.$id.'/editar');}
      catch(DomainException $ex){\flash($ex->getMessage(), 'error');\redirect('/admin/animales/nuevo');}
   }
   public function update(string $id):void {if(!$this->find((int)$id)){http_response_code(404);return;}
      try{$data=$this->data();$pairs=[];foreach(array_keys($data) as $key)$pairs[]='`'.$key.'`=:'.$key;$data['id']=(int)$id;Database::connection()->prepare('UPDATE animales SET '.implode(',',$pairs).' WHERE id=:id')->execute($data);\App\Services\AuditService::record('actualizar_animal','animales',(int)$id);\flash('Ficha actualizada.');}
      catch(DomainException $ex){\flash($ex->getMessage(), 'error');}\redirect('/admin/animales/'.$id.'/editar');
   }
   public function archive(string $id):void {if(!$this->find((int)$id)){http_response_code(404);return;}Database::connection()->prepare('UPDATE animales SET archivado=1 WHERE id=?')->execute([(int)$id]);\App\Services\AuditService::record('archivar_animal','animales',(int)$id);\flash('Animal archivado sin borrar su historial.');\redirect('/admin/animales');}
   public function upload(string $id):void {
      if(!$this->find((int)$id)){http_response_code(404);return;}
      $path=null;
      try {
         $path=MediaStorage::upload('foto','animales');$db=Database::connection();$db->beginTransaction();
         $st=$db->prepare('SELECT COUNT(*) FROM animal_fotografias WHERE animal_id=?');$st->execute([(int)$id]);$isMain=(int)$st->fetchColumn()===0?1:0;
         $desc=trim((string)($_POST['texto_alternativo']??''));if($desc==='')$desc='Fotografía del animal';
         $st=$db->prepare('INSERT INTO animal_fotografias(animal_id,ruta,texto_alternativo,es_principal) VALUES(?,?,?,?)');$st->execute([(int)$id,$path,mb_substr($desc,0,150),$isMain]);\App\Services\AuditService::record('subir_fotografia','animal_fotografias',(int)$db->lastInsertId());$db->commit();\flash('Fotografía añadida al animal.');
      } catch(\Throwable $e){if(isset($db)&&$db->inTransaction())$db->rollBack();if($path)MediaStorage::delete($path);\flash($e instanceof DomainException?$e->getMessage():'No se pudo adjuntar la fotografía.', 'error');}
      \redirect('/admin/animales/'.$id.'/editar');
   }
 }
