<?php
 declare(strict_types=1);
 namespace App\Controllers;
 use App\Core\Database;
 use App\Services\ThemeService;
 use PDO;
 final class AdminWorkflowController {
   private function action(string $table,string $set,string $allowed,string $id):void {
     if(!ctype_digit($id)||!in_array($set,explode('|',$allowed),true)){http_response_code(422);return;}
     $db=Database::connection();$adminId=(int)($_SESSION['admin_id']??0);
     if($table==='solicitudes_adopcion'){
       $sql="UPDATE solicitudes_adopcion SET estado=?,revisado_por=?,revisado_en=NOW() WHERE id=? AND estado IN ('pendiente','en_revision')";
       $db->prepare($sql)->execute([$set,$adminId,(int)$id]);$back='/admin/adopciones';
     } elseif($table==='solicitudes_voluntariado'){
       // Bloqueo de la actividad para evitar sobreasignación simultánea.
       $db->beginTransaction();try {
         $st=$db->prepare('SELECT actividad_id FROM solicitudes_voluntariado WHERE id=? FOR UPDATE');$st->execute([(int)$id]);$activity=(int)$st->fetchColumn();
         if($activity===0)throw new \DomainException('Solicitud inexistente');
         $st=$db->prepare('SELECT cupos FROM actividades_voluntariado WHERE id=? FOR UPDATE');$st->execute([$activity]);$cupos=(int)$st->fetchColumn();
         $st=$db->prepare("SELECT COUNT(*) FROM solicitudes_voluntariado WHERE actividad_id=? AND estado='aprobada'");$st->execute([$activity]);$used=(int)$st->fetchColumn();
         if($set==='aprobada'&&$used >= $cupos) throw new \DomainException('No quedan cupos disponibles');
         $db->prepare("UPDATE solicitudes_voluntariado SET estado=?,revisado_por=?,revisado_en=NOW() WHERE id=? AND estado='pendiente'")->execute([$set,$adminId,(int)$id]);$db->commit();
       }catch(\Throwable $e){$db->rollBack();\flash($e->getMessage(), 'error');\redirect('/admin/voluntariado');} $back='/admin/voluntariado';
     } elseif($table==='aportes'){
       // Requiere declaración explícita de conciliación; el comprobante por sí solo no acredita un pago.
       if($set==='confirmado'&&($_POST['conciliado']??'')!=='1'){\flash('Debes verificar el ingreso real antes de confirmarlo.', 'error');\redirect('/admin/aportes');}
       $db->prepare("UPDATE aportes SET estado=?,verificado_por=?,verificado_en=NOW() WHERE id=? AND estado='pendiente'")->execute([$set,$adminId,(int)$id]);$back='/admin/aportes';
     } else {http_response_code(404);return;}
     \App\Services\AuditService::record('actualizar_estado',$table,(int)$id,['estado'=>$set]);\flash('Estado actualizado correctamente.');\redirect($back);
   }
   public function adoption(string $id):void{$status=(string)($_POST['estado']??'');$this->action('solicitudes_adopcion',$status,'en_revision|aprobada|rechazada',$id);}
   public function volunteer(string $id):void{$this->action('solicitudes_voluntariado',(string)($_POST['estado']??''),'aprobada|rechazada',$id);}
   public function contribution(string $id):void{$this->action('aportes',(string)($_POST['estado']??''),'confirmado|rechazado',$id);}
 }
