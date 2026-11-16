<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Repositories\AnimalRepository;
use App\Repositories\ContentRepository;
use App\Repositories\CampaignRepository;
use App\Services\Forms;
use DomainException;
final class FormController {
    private function enabled():bool {return \env('PUBLIC_CONTENT_ENABLED','false')==='true';}
    private function unavailable():void {\page('pages/status',['title'=>'No disponible','message'=>'Este formulario no está habilitado.'],404);}
    private function submit(callable $fn,string $message):void {
        try {$fn();\flash($message);\redirect('/confirmacion');}
        catch (DomainException $e) {\page('pages/status',['title'=>'Revisa el formulario','message'=>$e->getMessage()],422);}
    }
    public function adoption(string $id):void { $animal=$this->enabled()?(new AnimalRepository())->find((int)$id):false;
        if(!$animal || $animal['estado_adopcion']!=='disponible') {$this->unavailable();return;}
        \page('forms/adoption',['title'=>'Solicitar adopción de '.$animal['nombre'],'animal'=>$animal]); }
    public function adoptionSave(string $id):void {if (!$this->enabled() || \env('PUBLIC_FORMS_ENABLED','false')!=='true') {$this->unavailable();return;} $this->submit(fn()=>Forms::adoption((int)$id),'Tu solicitud de adopción fue recibida y está pendiente de revisión.');}
    public function volunteer(string $id):void { $item=$this->enabled()?(new ContentRepository())->activity((int)$id):false;if(!$item){$this->unavailable();return;}\page('forms/volunteer',['title'=>'Postulación: '.$item['nombre'],'item'=>$item]);}
    public function volunteerSave(string $id):void {if (!$this->enabled() || \env('PUBLIC_FORMS_ENABLED','false')!=='true') {$this->unavailable();return;} $this->submit(fn()=>Forms::volunteer((int)$id),'Tu postulación fue recibida.');}
    public function material():void { if (\env('PUBLIC_FORMS_ENABLED','false')!=='true') {$this->unavailable();return;}\page('forms/material',['title'=>'Donación material']);}
    public function materialSave():void { if (\env('PUBLIC_FORMS_ENABLED','false')!=='true') {$this->unavailable();return;} $this->submit(fn()=>Forms::material(),'Registramos tu ofrecimiento de donación material; el refugio se comunicará para coordinar.'); }
    public function contribute(string $id):void { $campaign=$this->enabled()?(new CampaignRepository())->find((int)$id):false;if (!$campaign || !(new CampaignRepository())->donationsEnabled()){$this->unavailable();return;}\page('forms/contribution',['title'=>'Aportar a campaña','item'=>$campaign]); }
    public function contributeSave(string $id):void { $repo=new CampaignRepository();if(!$this->enabled() || \env('PUBLIC_FORMS_ENABLED','false')!=='true' || !$repo->find((int)$id) || !$repo->donationsEnabled()){$this->unavailable();return;}
        $this->submit(fn()=>Forms::contribution((int)$id),'El aporte fue registrado como pendiente; todavía NO está confirmado ni sumado a la recaudación.'); }
    public function confirmation():void { $message=\get_flash();if ($message===null) \redirect('/');\page('pages/status',['title'=>'Formulario recibido','message'=>$message]); }
}
