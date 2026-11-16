<?php
 declare(strict_types=1);
 namespace App\Controllers;
 use App\Core\Database;
 use DomainException;
 final class AdminVolunteerController {
    public function index():void {
       $db=Database::connection();
       $activities=$db->query('SELECT id,nombre,fecha,cupos,estado FROM actividades_voluntariado ORDER BY fecha DESC LIMIT 35')->fetchAll();
       $requests=$db->query('SELECT s.id,s.nombre_completo,s.correo,s.edad,s.estado,s.creado_en,a.nombre actividad FROM solicitudes_voluntariado s JOIN actividades_voluntariado a ON a.id=s.actividad_id ORDER BY s.id DESC LIMIT 80')->fetchAll();
       \page('admin/volunteer',['title'=>'Voluntariado','activities'=>$activities,'requests'=>$requests]);
    }
    public function newActivity():void {\page('admin/volunteer-form',['title'=>'Nueva actividad']);}
    public function store():void {
       try{
         $name=trim((string)($_POST['nombre']??''));$description=trim((string)($_POST['descripcion']??''));$where=trim((string)($_POST['lugar']??''));
         $date=(string)($_POST['fecha']??'');$time=(string)($_POST['hora_inicio']??'');$end=(string)($_POST['hora_fin']??'');
         $cupos=filter_var($_POST['cupos']??null,FILTER_VALIDATE_INT);
         $dateObj=\DateTimeImmutable::createFromFormat('!Y-m-d',$date);
         if(!$dateObj||$dateObj->format('Y-m-d')!==$date||!preg_match('/^\d{2}:\d{2}$/',$time)||($end!==''&&!preg_match('/^\d{2}:\d{2}$/',$end))||($end!==''&&$end<=$time)||$cupos===false||$cupos<1||$cupos>5000||$name===''||$description===''||$where===''||mb_strlen($name)>150||mb_strlen($where)>200)throw new DomainException('Revisa fechas, horarios, nombre, lugar y cupos de la actividad');
         $state=(string)($_POST['estado']??'cerrada');if(!in_array($state,['abierta','cerrada'],true))throw new DomainException('Estado inválido');
         $stmt=Database::connection()->prepare('INSERT INTO actividades_voluntariado(nombre,descripcion,fecha,hora_inicio,hora_fin,lugar,requisitos,cupos,estado,creado_por) VALUES(?,?,?,?,?,?,?,?,?,?)');
         $stmt->execute([$name,$description,$date,$time,$end?:null,$where,trim((string)($_POST['requisitos']??'')),$cupos,$state,(int)$_SESSION['admin_id']]);
         \App\Services\AuditService::record('crear_actividad','actividades_voluntariado',(int)Database::connection()->lastInsertId());
         \flash('Actividad creada.');\redirect('/admin/voluntariado');
       }catch(DomainException $e){\flash($e->getMessage());\redirect('/admin/voluntariado/nueva');}
    }
 }
