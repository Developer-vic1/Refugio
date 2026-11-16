<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';

use App\Core\Database;
use App\Repositories\AnimalRepository;
use App\Services\AdoptionPreparation;
use App\Services\PreparedPdf;

function assertPdf(bool $condition,string $message):void {if(!$condition)throw new RuntimeException($message);}
$input=[
    'nombre_completo'=>'Persona de prueba',
    'edad'=>'30','telefono'=>'+591 00000000','correo'=>'prueba@example.test',
    'motivo'=>'Quiero ofrecer un hogar tranquilo y un compromiso que pueda sostener cada día.',
    'experiencia_previa'=>'He compartido el cuidado cotidiano de animales y quiero seguir aprendiendo con el equipo.',
    'condiciones_hogar'=>'Vivimos en un espacio seguro, con una zona tranquila para descansar y tiempo para adaptarnos juntos.',
    'disponibilidad_responsabilidades'=>'Puedo organizar los paseos y el cuidado diario, acompañar los controles veterinarios y coordinar apoyo cuando haga falta.',
    'informacion_adicional'=>'Quisiera conversar sobre la adaptación y las necesidades de este compañero.',
    'acepta_privacidad'=>'1',
];
$valid=AdoptionPreparation::validate($input);
assertPdf(!$valid['errors'],'Datos sintéticos válidos');
foreach (['17','121','18.5'] as $age) assertPdf(isset(AdoptionPreparation::validate(array_replace($input,['edad'=>$age]))['errors']['edad']),'Límite de edad');
assertPdf(isset(AdoptionPreparation::validate(array_replace($input,['nombre_completo'=>['dato']]))['errors']['nombre_completo']),'No aceptar campos de tipo array');
assertPdf(isset(AdoptionPreparation::validate(array_replace($input,['acepta_privacidad'=>'']))['errors']['acepta_privacidad']),'Privacidad explícita');
$long=AdoptionPreparation::validate(array_replace($input,['motivo'=>str_repeat('a',100000)]));
assertPdf(isset($long['errors']['motivo']) && mb_strlen($long['values']['motivo'])===1500,'Entrada extensa acotada sin perder los demás campos');
$repo=new AnimalRepository();
$available=$repo->catalog('','',1);
assertPdf((bool)$available,'Perfil disponible para la verificación');
$animal=$repo->find((int)$available[0]['id']);
$directory=BASE_PATH.'/output/pdf';
if(!is_dir($directory))mkdir($directory,0755,true);
$before=(int)Database::connection()->query('SELECT COUNT(*) FROM solicitudes_adopcion')->fetchColumn();
$bytes=PreparedPdf::adoption($animal,$valid['values']);
assertPdf(str_starts_with($bytes,'%PDF-') && strlen($bytes)>10000,'Documento PDF real');
file_put_contents($directory.'/adopcion-ejemplo.pdf',$bytes);
$limitInput=$input;
foreach (['motivo','experiencia_previa','condiciones_hogar','disponibilidad_responsabilidades'] as $key) $limitInput[$key]=mb_substr(str_repeat('Cuidado, paciencia y tiempo compartido. ',70),0,1460).' FIN-'.$key;
$limitInput['informacion_adicional']=mb_substr(str_repeat('Preguntas para conversar con calma. ',40),0,950).' FIN-INFORMACION';
$bounded=AdoptionPreparation::validate($limitInput);
assertPdf(!$bounded['errors'],'Respuestas largas dentro de los límites');
file_put_contents($directory.'/adopcion-respuestas-largas.pdf',PreparedPdf::adoption($animal,$bounded['values']));
$after=(int)Database::connection()->query('SELECT COUNT(*) FROM solicitudes_adopcion')->fetchColumn();
assertPdf($before===$after,'Preparar PDF no inserta una solicitud');
file_put_contents($directory.'/test-input.json',json_encode(['animal_id'=>$animal['id'],'input'=>$input],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
echo "PDF: validación, límites, archivos reales y ausencia de escrituras correctos. Perfil de prueba: ".$animal['id'].".\n";
