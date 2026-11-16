<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Repositories\AnimalRepository;
use App\Repositories\ContentRepository;
use App\Repositories\CampaignRepository;
use App\Services\ContentVisibility;
use App\Support\AnimalFilters;
final class PublicController {
    private function enabled():bool {return \env('PUBLIC_CONTENT_ENABLED','false') === 'true';}
    public function home():void { $animals=ContentVisibility::visible()?(new AnimalRepository())->catalog('', '',4):[];\page('public/home',['title'=>'Inicio','animals'=>$animals,'reviewMode'=>ContentVisibility::localReview()]); }
    public function about():void { \page('public/about',['title'=>'Nuestra historia empieza aquí']); }
    public function adoptionInfo():void { \page('pages/text',['title'=>'Adopciones responsables','lead'=>'Conoce a los animales disponibles. Cada solicitud es evaluada por el refugio antes de formalizar la adopción.','link'=>'/animales','linkLabel'=>'Explorar animales']); }
    public function donationInfo():void { \page('pages/text',['title'=>'Cómo colaborar','lead'=>'Puedes conocer las campañas publicadas o proponer una donación material. Los aportes monetarios requieren confirmación administrativa.','link'=>'/campanas','linkLabel'=>'Ver campañas','secondLink'=>'/donaciones/materiales','secondLabel'=>'Ofrecer donación material']); }
    public function contact():void { \page('public/contact',['title'=>'Hablemos']); }
    public function privacy():void {\page('public/privacy',['title'=>'Tu información, con cuidado']);}
    public function animals():void {
        $filters = AnimalFilters::fromQuery($_GET);
        $repo = new AnimalRepository();
        $visible = ContentVisibility::visible();
        $result = $visible ? $repo->search($filters) : ['items'=>[],'total'=>0,'page'=>1,'pages'=>1,'first'=>0,'last'=>0];
        $options = $visible ? $repo->filterOptions() : array_fill_keys(['especie','sexo','tamano','estado_salud','estado_adopcion'], []);
        \page('public/animals',['title'=>'Un compañero, una historia','filters'=>$filters,'result'=>$result,'options'=>$options,'reviewMode'=>ContentVisibility::localReview()]);
    }
    public function animal(string $id):void {
        $repo = new AnimalRepository();
        $animal = ContentVisibility::visible() && ctype_digit($id) ? $repo->find((int)$id) : false;
        if (!$animal) {\page('pages/status',['title'=>'Animal no disponible','message'=>'No encontramos una ficha pública para este animal.'],404);return;}
        $photos = [];
        $records = $repo->photos((int)$id);
        $raster = array_filter($records, fn($photo) => str_starts_with(\asset($photo['ruta']), '/media/'));
        foreach (array_values($raster ?: $records) as $index=>$photo) {
            $photos[] = ['src'=>\asset($photo['ruta']),'alt'=>$photo['texto_alternativo'] ?: 'Vista '.($index+1).' de '.$animal['nombre']];
        }
        if (!$photos) $photos[] = ['src'=>\asset(null),'alt'=>'Perfil de '.$animal['nombre'].'; imagen pendiente'];
        \page('public/animal',['title'=>$animal['nombre'],'animal'=>$animal,'photos'=>$photos,'reviewMode'=>ContentVisibility::localReview()]);
    }
    public function campaigns():void {$items=$this->enabled()?(new CampaignRepository())->active():[];\page('public/campaigns',['title'=>'Campañas','items'=>$items]);}
    public function campaign(string $id):void {$item=$this->enabled()?(new CampaignRepository())->find((int)$id):false;if(!$item){\page('pages/status',['title'=>'Campaña no disponible','message'=>'No hay una campaña pública para este enlace.'],404);return;}\page('public/campaign',['title'=>$item['titulo'],'item'=>$item]);}
    public function stories():void {$items=$this->enabled()?(new ContentRepository())->stories():[];\page('public/stories',['title'=>'Historias','items'=>$items]);}
    public function story(string $id):void {$item=$this->enabled()?(new ContentRepository())->story((int)$id):false;if(!$item){\page('pages/status',['title'=>'Historia no encontrada','message'=>'Esta historia no está publicada.'],404);return;}\page('pages/detail',['title'=>$item['titulo'],'item'=>$item]);}
    public function volunteers():void {$items=$this->enabled()?(new ContentRepository())->activities():[];\page('public/volunteers',['title'=>'Voluntariado','items'=>$items]);}
    public function activity(string $id):void {$item=$this->enabled()?(new ContentRepository())->activity((int)$id):false;if(!$item){\page('pages/status',['title'=>'Actividad no disponible','message'=>'Esta actividad no admite nuevas postulaciones.'],404);return;}\page('public/activity',['title'=>$item['nombre'],'item'=>$item]);}
    public function transparency():void {$repo=new ContentRepository();$enabled=$this->enabled();\page('public/transparency',['title'=>'Transparencia','totals'=>$enabled?$repo->transparency():[],'reports'=>$enabled?$repo->reports():[]]);}
    public function report(string $id):void {$item=$this->enabled()?(new ContentRepository())->report((int)$id):false;if(!$item){\page('pages/status',['title'=>'Informe no disponible','message'=>'Este informe no está publicado.'],404);return;}\page('pages/detail',['title'=>$item['titulo'],'item'=>$item]);}
}
