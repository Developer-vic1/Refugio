<?php
 declare(strict_types=1);
 namespace App\Controllers;
 use App\Core\Database;
 final class AdminSettingsController {
   public function index():void {$settings=Database::connection()->query('SELECT * FROM refugio_configuracion WHERE id=1')->fetch()?:[];\page('admin/settings',['title'=>'Configuración del refugio','settings'=>$settings]);}
   public function update():void {
      $keys=['nombre_refugio'=>120,'frase_institucional'=>255,'descripcion_breve'=>2000,'mision'=>5000,'historia'=>10000,'telefono'=>30,'whatsapp'=>30,'correo_contacto'=>150,'direccion_publica'=>255,'horario_atencion'=>255,'facebook_url'=>255,'instagram_url'=>255,'tiktok_url'=>255,'banco_nombre'=>100,'banco_titular'=>120,'banco_numero_cuenta'=>50,'banco_tipo_cuenta'=>40,'banco_instrucciones'=>2000];
      $data=[];foreach($keys as $key=>$max){$v=trim((string)($_POST[$key]??''));if(mb_strlen($v)>$max){\flash('Texto demasiado largo: '.$key, 'error');\redirect('/admin/configuracion');}$data[$key]=$v===''?null:$v;}
      $enabled=($_POST['donaciones_habilitadas']??'')==='1'?1:0;
      if($enabled && (!$data['banco_nombre']||!$data['banco_titular']||!$data['banco_numero_cuenta'])){\flash('Configura el banco y los datos de recepción antes de habilitar donaciones.', 'error');\redirect('/admin/configuracion');}
      $data['donaciones_habilitadas']=$enabled;$pairs=[];foreach(array_keys($data) as $k)$pairs[]='`'.$k.'`=:'.$k;
      $data['id']=1;Database::connection()->prepare('UPDATE refugio_configuracion SET '.implode(',',$pairs).' WHERE id=:id')->execute($data);
      \App\Services\AuditService::record('actualizar_configuracion','refugio_configuracion',1);
      \flash('Configuración actualizada.');\redirect('/admin/configuracion');
   }
 }
