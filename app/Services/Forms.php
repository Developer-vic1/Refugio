<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Database;
use DomainException;
use PDOException;
final class Forms {
    private static function required(string $field,int $max=2000):string {
        $value = trim((string)($_POST[$field] ?? ''));
        if ($value==='' || (function_exists('mb_strlen') ? mb_strlen($value) : strlen($value))>$max) throw new DomainException('Revisa el campo: '.str_replace('_',' ',$field));
        return $value;
    }
    private static function email():string { $value = self::required('correo',150);if (!filter_var($value,FILTER_VALIDATE_EMAIL)) throw new DomainException('Correo electrónico inválido');return $value;}
    private static function agree():void {if (($_POST['acepta_privacidad']??'')!=='1') throw new DomainException('Debes aceptar la política de privacidad');}
    private static function age():int { $age=filter_var($_POST['edad']??null,FILTER_VALIDATE_INT);if ($age===false || $age<18 || $age>120) throw new DomainException('Para enviar este formulario debes ser mayor de edad');return $age; }
    public static function adoption(int $animalId):void {
        self::agree();$data=[
          'animal_id'=>$animalId,'nombre_completo'=>self::required('nombre_completo',120),
          'edad'=>self::age(),'correo'=>self::email(),'telefono'=>self::required('telefono',30),
          'motivo'=>self::required('motivo'),'experiencia_previa'=>self::required('experiencia_previa'),
          'condiciones_hogar'=>self::required('condiciones_hogar'),
          'disponibilidad_responsabilidades'=>self::required('disponibilidad_responsabilidades'),
          'informacion_adicional'=>trim((string)($_POST['informacion_adicional']??'')),
        ];
        $db=Database::connection();$db->beginTransaction();
        try {
            $stmt=$db->prepare("SELECT id FROM animales WHERE id=? AND archivado=0 AND estado_adopcion='disponible' FOR UPDATE");
            $stmt->execute([$animalId]);
            if (!$stmt->fetch()) throw new DomainException('Este animal ya no admite solicitudes.');
            $cols=array_keys($data);
            $sql='INSERT INTO solicitudes_adopcion ('.implode(',',$cols).',acepta_privacidad) VALUES (:'.implode(',:',$cols).',1)';
            $stmt=$db->prepare($sql);$stmt->execute($data);$db->commit();
        } catch (\Throwable $ex) {if($db->inTransaction())$db->rollBack();throw $ex;}
    }
    public static function volunteer(int $activityId):void {
        self::agree();$data=[
            'actividad_id'=>$activityId,'nombre_completo'=>self::required('nombre_completo',120),
            'edad'=>self::age(),'telefono'=>self::required('telefono',30),'correo'=>self::email(),
            'experiencia'=>trim((string)($_POST['experiencia']??'')),
            'disponibilidad'=>self::required('disponibilidad'),
            'observaciones'=>trim((string)($_POST['observaciones']??'')),
        ];
        $db=Database::connection();$db->beginTransaction();
        try {
            $stmt=$db->prepare("SELECT id,cupos FROM actividades_voluntariado WHERE id=? AND estado='abierta' AND fecha>=CURRENT_DATE FOR UPDATE");$stmt->execute([$activityId]);$activity=$stmt->fetch();
            if (!$activity) throw new DomainException('La actividad no está abierta.');
            $stmt=$db->prepare("SELECT COUNT(*) FROM solicitudes_voluntariado WHERE actividad_id=? AND estado='aprobada'");$stmt->execute([$activityId]);
            if ((int)$stmt->fetchColumn() >= (int)$activity['cupos']) throw new DomainException('La actividad ya no tiene cupos.');
            $cols=array_keys($data);$stmt=$db->prepare('INSERT INTO solicitudes_voluntariado ('.implode(',',$cols).',acepta_privacidad) VALUES (:'.implode(',:',$cols).',1)');$stmt->execute($data);$db->commit();
        } catch (\Throwable $ex){if($db->inTransaction())$db->rollBack();throw $ex;}
    }
    public static function material():void {
        self::agree();
        $data=[
          'producto'=>self::required('producto',150),'cantidad_ofrecida'=>(float)($_POST['cantidad_ofrecida']??0),
          'unidad'=>self::required('unidad',30),'donante_nombre'=>self::required('donante_nombre',120),
          'donante_contacto'=>self::required('donante_contacto',150),
          'disponibilidad_entrega'=>trim((string)($_POST['disponibilidad_entrega']??'')),
          'observaciones'=>trim((string)($_POST['observaciones']??'')),
        ];
        if (!is_numeric($_POST['cantidad_ofrecida']??null) || $data['cantidad_ofrecida']<=0) throw new DomainException('Cantidad incorrecta');
        $cols=array_keys($data);$stmt=Database::connection()->prepare('INSERT INTO donaciones_especie ('.implode(',',$cols).") VALUES (:".implode(',:',$cols).')');$stmt->execute($data);
    }
    public static function contribution(int $campaignId):void {
        self::agree();
        $amount=trim((string)($_POST['monto']??''));
        if (!preg_match('/^\\d{1,10}(?:\\.\\d{1,2})?$/',$amount) || (float)$amount<=0) throw new DomainException('Monto incorrecto');
        $method=self::required('medio',20);
        if (!in_array($method,['qr','transferencia','efectivo','otro'],true)) throw new DomainException('Medio de pago inválido');
        $sql='INSERT INTO aportes(campana_id,monto,medio,donante_nombre,donante_contacto,estado) VALUES (:campana,:monto,:medio,:donante_nombre,:donante_contacto,\'pendiente\')';
        $stmt=Database::connection()->prepare($sql);
        $stmt->execute(['campana'=>$campaignId,'monto'=>$amount,'medio'=>$method,'donante_nombre'=>self::required('donante_nombre',120),'donante_contacto'=>self::required('donante_contacto',150)]);
    }
}
