<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Repositories\AnimalRepository;
use App\Services\AdoptionPreparation;
use App\Services\ContentVisibility;
use App\Services\PreparedPdf;

final class AdoptionPreparationController
{
    private function animal(string $id): array|false
    {
        if (!ContentVisibility::visible() || !ctype_digit($id)) return false;
        $animal=(new AnimalRepository())->find((int)$id);
        return $animal && $animal['estado_adopcion']==='disponible' ? $animal : false;
    }

    public function form(string $id): void
    {
        $animal=$this->animal($id);
        if (!$animal) { \page('pages/status',['title'=>'Demos otro paso','message'=>'Este perfil no recibe nuevas fichas. Puedes explorar otros compañeros desde el catálogo.'],404);return; }
        header('Cache-Control: no-store');
        \page('forms/adoption',['title'=>'Preparar mi encuentro con '.$animal['nombre'],'animal'=>$animal,'values'=>[],'errors'=>[]]);
    }

    public function pdf(string $id): void
    {
        header('Cache-Control: no-store, private');
        $animal=$this->animal($id);
        if (!$animal) { $this->error('Este perfil ya no recibe nuevas fichas. Conserva tus respuestas y consulta al equipo.',409);return; }
        $validation=AdoptionPreparation::validate($_POST);
        if ($validation['errors']) {
            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) $this->error('Revisa los campos señalados.',422,$validation['errors']);
            else \page('forms/adoption',['title'=>'Revisa tu ficha','animal'=>$animal]+$validation,422);
            return;
        }
        try { $bytes=PreparedPdf::adoption($animal,$validation['values']); }
        catch (\Throwable $error) {
            error_log('[refugio-pdf] '.get_class($error));
            $message='No pudimos preparar el PDF. Tus respuestas siguen en el formulario; inténtalo de nuevo.';
            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) $this->error($message,503);
            else \page('forms/adoption',['title'=>'Preparemos tu ficha','animal'=>$animal,'values'=>$validation['values'],'errors'=>[],'formError'=>$message],503);
            return;
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="huella-serena-adopcion-'.date('Ymd-His').'.pdf"');
        header('Content-Length: '.strlen($bytes));
        echo $bytes;
    }

    private function error(string $message, int $status, array $errors = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['message'=>$message,'errors'=>$errors],JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
