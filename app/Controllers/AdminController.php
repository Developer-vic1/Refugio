<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Database;
use PDO;
final class AdminController {
    public function login():void {if (Auth::check()) \redirect('/admin');\page('admin/login',['title'=>'Ingreso administrativo']);}
    public function authenticate():void {
        $email=trim((string)($_POST['usuario']??''));$password=(string)($_POST['contrasena']??'');
        if (strlen($email)>150 || strlen($password)>1024) {\flash('Credenciales no válidas');\redirect('/admin/login');}
        $ip=(string)($_SERVER['REMOTE_ADDR']??'0.0.0.0');$db=Database::connection();
        $q=$db->prepare('SELECT COUNT(*) FROM intentos_login WHERE ((identificador=:u) OR ip=:ip) AND exitoso=0 AND creado_en>DATE_SUB(NOW(),INTERVAL 15 MINUTE)');
        $q->execute(['u'=>$email,'ip'=>$ip]);
        if ((int)$q->fetchColumn()>=10){http_response_code(429);\page('pages/status',['title'=>'Acceso limitado','message'=>'Demasiados intentos. Intenta más tarde.']);return;}
        $q=$db->prepare('SELECT id,nombre_completo,contrasena_hash FROM administradores WHERE (usuario=:u OR correo=:c) AND activo=1 LIMIT 1');
        $q->execute(['u'=>$email,'c'=>$email]);$admin=$q->fetch();
        $valid=$admin && Auth::verifyPbkdf2($password,(string)$admin['contrasena_hash']);
        $log=$db->prepare('INSERT INTO intentos_login(identificador,ip,exitoso) VALUES(?,?,?)');$log->execute([$email,$ip,$valid?1:0]);
        if (!$valid){\flash('Usuario o contraseña incorrectos');\redirect('/admin/login');}
        Auth::login($admin);
        $db->prepare('UPDATE administradores SET ultimo_acceso=NOW() WHERE id=?')->execute([(int)$admin['id']]);
        \redirect('/admin');
    }
    public function dashboard():void {
        $db=Database::connection();
        $sql=[
            'Animales'=>'SELECT COUNT(*) FROM animales',
            'Solicitudes de adopción pendientes'=>"SELECT COUNT(*) FROM solicitudes_adopcion WHERE estado='pendiente'",
            'Aportes por verificar'=>"SELECT COUNT(*) FROM aportes WHERE estado='pendiente'",
            'Voluntariado pendiente'=>"SELECT COUNT(*) FROM solicitudes_voluntariado WHERE estado='pendiente'",
        ];$metrics=[];foreach($sql as $label=>$query)$metrics[$label]=(int)$db->query($query)->fetchColumn();
        \page('admin/dashboard',['title'=>'Panel administrativo','metrics'=>$metrics]);
    }
    public function listing(string $key):void {
        $allowed=[
            'animales'=>['animales',['id','nombre','especie','estado_salud','estado_adopcion']],
            'adopciones'=>['solicitudes_adopcion',['id','nombre_completo','animal_id','estado','creado_en']],
            'campanas'=>['campanas',['id','titulo','tipo','estado','meta_monto']],
            'aportes'=>['aportes',['id','campana_id','monto','medio','estado']],
            'donaciones-materiales'=>['donaciones_especie',['id','producto','cantidad_ofrecida','estado','creado_en']],
            'inventario'=>['inventario_productos',['id','nombre','categoria','unidad','stock_minimo']],
            'historias'=>['historias',['id','animal_id','titulo','publicada','fecha_evento']],
            'voluntariado'=>['actividades_voluntariado',['id','nombre','fecha','cupos','estado']],
            'transparencia'=>['reportes_transparencia',['id','titulo','periodo_inicio','periodo_fin','publicado']],
            'gastos'=>['gastos',['id','concepto','monto','estado','fecha_gasto']],
            'configuracion'=>['refugio_configuracion',['id','nombre_plataforma','nombre_refugio','donaciones_habilitadas']],
            'auditoria'=>['auditoria_administrativa',['id','administrador_id','accion','tabla_afectada','creado_en']],
        ];
        if(!isset($allowed[$key])) {\page('pages/status',['title'=>'No encontrado','message'=>'Módulo inexistente'],404);return;}
        [$table,$columns]=$allowed[$key];
        // Identificadores SQL proceden exclusivamente de la lista blanca anterior.
        $rows=Database::connection()->query('SELECT '.implode(',',$columns).' FROM '.$table.' ORDER BY id DESC LIMIT 50')->fetchAll();
        \page('admin/listing',['title'=>'Módulo: '.ucfirst(str_replace('-',' ',$key)),'columns'=>$columns,'rows'=>$rows,'key'=>$key]);
    }
    public function logout():void { Auth::logout();\redirect('/admin/login'); }
}
