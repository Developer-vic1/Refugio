<?php
 declare(strict_types=1);
 namespace App\Controllers;
 use App\Core\Database;
 use App\Services\MediaStorage;
 use DomainException;
 use PDO;
 final class AdminPublishingController {
    private function animals():array {return Database::connection()->query('SELECT id,nombre FROM animales WHERE archivado=0 ORDER BY nombre LIMIT 400')->fetchAll();}
    private function find(string $table,int $id):array|false {
       if(!in_array($table,['historias','campanas'],true))return false;
       $st=Database::connection()->prepare('SELECT * FROM '.$table.' WHERE id=?');$st->execute([$id]);return $st->fetch(PDO::FETCH_ASSOC);
    }
    private static function date(string $s,bool $required=false):?string {
       if($s===''&&!$required)return null;
       $date=\DateTimeImmutable::createFromFormat('!Y-m-d',$s);
       if(!$date||$date->format('Y-m-d')!==$s)throw new DomainException('Fecha inválida');return $s;
    }
    private static function str(string $key,int $max,bool $req=true):?string {
       $v=trim((string)($_POST[$key]??''));if(mb_strlen($v)>$max||($req&&$v===''))throw new DomainException('Revisa el campo '.$key);return $v===''?null:$v;
    }
    public function stories():void {
       $rows=Database::connection()->query('SELECT h.*,a.nombre animal FROM historias h JOIN animales a ON a.id=h.animal_id ORDER BY h.id DESC LIMIT 80')->fetchAll();
       \page('admin/stories',['title'=>'Historias de rescate','rows'=>$rows]);
    }
    public function storyNew():void{\page('admin/story-form',['title'=>'Nueva historia','record'=>[],'animals'=>$this->animals(),'photos'=>[]]);}
    public function storyEdit(string $id):void{
       $row=$this->find('historias',(int)$id);if(!$row){http_response_code(404);return;}
       $st=Database::connection()->prepare('SELECT * FROM historia_fotografias WHERE historia_id=? ORDER BY orden,id');$st->execute([(int)$id]);
       \page('admin/story-form',['title'=>'Editar historia','record'=>$row,'animals'=>$this->animals(),'photos'=>$st->fetchAll()]);
    }
    public function storySave(string $id=''):void {
       try{
          $animal=filter_var($_POST['animal_id']??null,FILTER_VALIDATE_INT);
          if(!$animal||$animal<1)throw new DomainException('Selecciona un animal');
          $st=Database::connection()->prepare('SELECT COUNT(*) FROM animales WHERE id=?');$st->execute([$animal]);if(!(int)$st->fetchColumn())throw new DomainException('Animal inexistente');
          $state=(string)($_POST['estado_proceso']??'otro');$allowed=['rescate','primera_valoracion','traslado','tratamiento','recuperacion','disponible_adopcion','adopcion','otro'];
          if(!in_array($state,$allowed,true))throw new DomainException('Estado de historia inválido');
          $published=($_POST['publicada']??'')==='1'?1:0;
          if($published&&($_POST['contenido_verificado']??'')!=='1')throw new DomainException('Verifica los datos y la autorización antes de publicar');
          $record=['animal_id'=>$animal,'fecha_evento'=>self::date((string)($_POST['fecha_evento']??''),true),'titulo'=>self::str('titulo',150),'descripcion'=>self::str('descripcion',30000),'estado_proceso'=>$state,'publicada'=>$published,'publicada_en'=>$published?date('Y-m-d H:i:s'):null];
          $db=Database::connection();
          if($id!==''){if(!$this->find('historias',(int)$id)){http_response_code(404);return;}$set=[];foreach($record as $k=>$v)$set[]='`'.$k.'`=:'.$k;$record['id']=(int)$id;$db->prepare('UPDATE historias SET '.implode(',',$set).' WHERE id=:id')->execute($record);$next=(int)$id;}
          else{$record['creado_por']=(int)$_SESSION['admin_id'];$keys=array_keys($record);$db->prepare('INSERT INTO historias (`'.implode('`,`',$keys).'`) VALUES (:'.implode(',:',$keys).')')->execute($record);$next=(int)$db->lastInsertId();}
          \App\Services\AuditService::record('guardar_historia','historias',$next,['publicada'=>$published]);\flash('Historia guardada.');\redirect('/admin/historias/'.$next.'/editar');
       }catch(DomainException $e){\flash($e->getMessage(), 'error');\redirect($id!==''?'/admin/historias/'.$id.'/editar':'/admin/historias/nueva');}
    }
    public function storyPhoto(string $id):void {
       if(!$this->find('historias',(int)$id)){http_response_code(404);return;}$path=null;
       try{$path=MediaStorage::upload('foto','historias');$description=trim((string)($_POST['texto_alternativo']??''));
           Database::connection()->prepare('INSERT INTO historia_fotografias(historia_id,ruta,texto_alternativo) VALUES(?,?,?)')->execute([(int)$id,$path,mb_substr($description?:'Imagen de la historia',0,150)]);\flash('Fotografía guardada.');
       }catch(\Throwable $e){if($path)MediaStorage::delete($path);\flash($e instanceof DomainException?$e->getMessage():'No se pudo guardar la fotografía.', 'error');}
       \redirect('/admin/historias/'.$id.'/editar');
    }
    public function campaigns():void {$rows=Database::connection()->query('SELECT c.*,a.nombre animal FROM campanas c LEFT JOIN animales a ON a.id=c.animal_id ORDER BY c.id DESC LIMIT 100')->fetchAll();\page('admin/campaigns',['title'=>'Campañas','rows'=>$rows]);}
    public function campaignNew():void{\page('admin/campaign-form',['title'=>'Nueva campaña','record'=>[],'animals'=>$this->animals()]);}
    public function campaignEdit(string $id):void{$row=$this->find('campanas',(int)$id);if(!$row){http_response_code(404);return;}\page('admin/campaign-form',['title'=>'Editar campaña','record'=>$row,'animals'=>$this->animals()]);}
    public function campaignSave(string $id=''):void {
       try{
         $type=(string)($_POST['tipo']??'general');$state=(string)($_POST['estado']??'borrador');$category=(string)($_POST['categoria']??'otro');
         if(!in_array($type,['general','individual'],true)||!in_array($state,['borrador','activa','pausada','finalizada','cancelada'],true)||!in_array($category,['alquiler','alimentacion','servicios_basicos','limpieza','mantenimiento','infraestructura','atencion_veterinaria','otro'],true))throw new DomainException('Selecciona un tipo, estado y categoría válidos');
         $animal=null;if($type==='individual'){$animal=filter_var($_POST['animal_id']??null,FILTER_VALIDATE_INT);if(!$animal||$animal<1)throw new DomainException('La campaña individual debe estar asociada a un animal');$st=Database::connection()->prepare('SELECT COUNT(*) FROM animales WHERE id=?');$st->execute([$animal]);if(!(int)$st->fetchColumn())throw new DomainException('Animal inexistente');}
         $meta=trim((string)($_POST['meta_monto']??''));if($meta!==''&&(!preg_match('/^\d{1,10}(?:\.\d{1,2})?$/',$meta)||(float)$meta<=0))throw new DomainException('Meta monetaria inválida');
         $permanent=($_POST['es_permanente']??'')==='1'?1:0;
         $begin=self::date((string)($_POST['fecha_inicio']??''));$end=self::date((string)($_POST['fecha_fin']??''));if($begin&&$end&&$begin>$end)throw new DomainException('La fecha final debe ser posterior a la inicial');
         if($state==='activa'&&($_POST['contenido_verificado']??'')!=='1')throw new DomainException('Confirma la verificación de la campaña antes de activarla');
         $record=['tipo'=>$type,'animal_id'=>$animal,'titulo'=>self::str('titulo',150),'descripcion'=>self::str('descripcion',30000),'necesidad'=>self::str('necesidad',150),'categoria'=>$category,'meta_monto'=>$meta===''?null:$meta,'es_permanente'=>$permanent,'estado'=>$state,'fecha_inicio'=>$begin,'fecha_fin'=>$end];
         $db=Database::connection();
         if($id!==''){if(!$this->find('campanas',(int)$id)){http_response_code(404);return;}$set=[];foreach($record as $k=>$v)$set[]='`'.$k.'`=:'.$k;$record['id']=(int)$id;$db->prepare('UPDATE campanas SET '.implode(',',$set).' WHERE id=:id')->execute($record);$next=(int)$id;}
         else{$record['creado_por']=(int)$_SESSION['admin_id'];$keys=array_keys($record);$db->prepare('INSERT INTO campanas (`'.implode('`,`',$keys).'`) VALUES (:'.implode(',:',$keys).')')->execute($record);$next=(int)$db->lastInsertId();}
         \App\Services\AuditService::record('guardar_campana','campanas',$next,['estado'=>$state]);\flash('Campaña guardada.');\redirect('/admin/campanas/'.$next.'/editar');
       }catch(DomainException $e){\flash($e->getMessage(), 'error');\redirect($id!==''?'/admin/campanas/'.$id.'/editar':'/admin/campanas/nueva');}
    }
 }
