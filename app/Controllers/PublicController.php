<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Repositories\AnimalRepository;
use App\Repositories\ContentRepository;
use App\Repositories\CampaignRepository;
final class PublicController {
    private function enabled():bool {return \env('PUBLIC_CONTENT_ENABLED','false') === 'true';}
    public function home():void { $animals=$this->enabled()?(new AnimalRepository())->catalog('', '',4):[];\page('public/home',['title'=>'Inicio','animals'=>$animals]); }
    public function about():void { $config=(new ContentRepository())->config();\page('pages/text',['title'=>'Nosotros','lead'=>$config['historia']??'Conoce nuestra misión y trabajo por el bienestar animal.']); }
    public function adoptionInfo():void { \page('pages/text',['title'=>'Adopciones responsables','lead'=>'Conoce a los animales disponibles. Cada solicitud es evaluada por el refugio antes de formalizar la adopción.','link'=>'/animales','linkLabel'=>'Explorar animales']); }
    public function donationInfo():void { \page('pages/text',['title'=>'Cómo colaborar','lead'=>'Puedes conocer las campañas publicadas o proponer una donación material. Los aportes monetarios requieren confirmación administrativa.','link'=>'/campanas','linkLabel'=>'Ver campañas','secondLink'=>'/donaciones/materiales','secondLabel'=>'Ofrecer donación material']); }
    public function contact():void { $config=(new ContentRepository())->config();\page('public/contact',['title'=>'Contacto','config'=>$config]); }
    public function privacy():void {\page('pages/text',['title'=>'Privacidad','lead'=>'Esta sección debe completarse con la política de privacidad oficial del refugio antes de publicar el sistema. Los formularios recopilan datos para gestionar solicitudes y deben tener una finalidad, responsable y plazo de conservación documentados.']);}
    public function animals():void { $query=trim((string)($_GET['q']??'')); $species=trim((string)($_GET['especie']??''));$animals=$this->enabled()?(new AnimalRepository())->catalog(substr($query,0,80),substr($species,0,50)):[];\page('public/animals',['title'=>'Nuestros animales','animals'=>$animals,'query'=>$query,'species'=>$species]); }
    public function animal(string $id):void { $animal=$this->enabled()?(new AnimalRepository())->find((int)$id):false;if (!$animal){\page('pages/status',['title'=>'Animal no disponible','message'=>'No encontramos una ficha pública para este animal.'],404);return;}\page('public/animal',['title'=>$animal['nombre'],'animal'=>$animal]); }
    public function campaigns():void {$items=$this->enabled()?(new CampaignRepository())->active():[];\page('public/campaigns',['title'=>'Campañas','items'=>$items]);}
    public function campaign(string $id):void {$item=$this->enabled()?(new CampaignRepository())->find((int)$id):false;if(!$item){\page('pages/status',['title'=>'Campaña no disponible','message'=>'No hay una campaña pública para este enlace.'],404);return;}\page('public/campaign',['title'=>$item['titulo'],'item'=>$item]);}
    public function stories():void {$items=$this->enabled()?(new ContentRepository())->stories():[];\page('public/stories',['title'=>'Historias','items'=>$items]);}
    public function story(string $id):void {$item=$this->enabled()?(new ContentRepository())->story((int)$id):false;if(!$item){\page('pages/status',['title'=>'Historia no encontrada','message'=>'Esta historia no está publicada.'],404);return;}\page('pages/detail',['title'=>$item['titulo'],'item'=>$item]);}
    public function volunteers():void {$items=$this->enabled()?(new ContentRepository())->activities():[];\page('public/volunteers',['title'=>'Voluntariado','items'=>$items]);}
    public function activity(string $id):void {$item=$this->enabled()?(new ContentRepository())->activity((int)$id):false;if(!$item){\page('pages/status',['title'=>'Actividad no disponible','message'=>'Esta actividad no admite nuevas postulaciones.'],404);return;}\page('public/activity',['title'=>$item['nombre'],'item'=>$item]);}
    public function transparency():void {$repo=new ContentRepository();$enabled=$this->enabled();\page('public/transparency',['title'=>'Transparencia','totals'=>$enabled?$repo->transparency():[],'reports'=>$enabled?$repo->reports():[]]);}
    public function report(string $id):void {$item=$this->enabled()?(new ContentRepository())->report((int)$id):false;if(!$item){\page('pages/status',['title'=>'Informe no disponible','message'=>'Este informe no está publicado.'],404);return;}\page('pages/detail',['title'=>$item['titulo'],'item'=>$item]);}
}
