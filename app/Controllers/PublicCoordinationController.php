<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Services\PublicCoordination;
use DomainException;
final class PublicCoordinationController
{
    public function prepare(): void
    {
        header('Cache-Control: no-store, private');
        $json=str_contains($_SERVER['HTTP_ACCEPT']??'','application/json');
        try {
            $data=PublicCoordination::prepare($_POST);
            if($json){header('Content-Type: application/json; charset=utf-8');echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);}
            else \page('public/coordinate-review',$data+['title'=>'Tu siguiente paso']);
        } catch(DomainException $error) {
            if($json){http_response_code(422);header('Content-Type: application/json; charset=utf-8');echo json_encode(['message'=>$error->getMessage()],JSON_UNESCAPED_UNICODE);}
            else \page('pages/status',['title'=>'Ajustemos tu mensaje','message'=>$error->getMessage()],422);
        }
    }
}
