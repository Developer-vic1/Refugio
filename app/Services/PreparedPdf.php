<?php
declare(strict_types=1);
namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

final class PreparedPdf
{
    public static function adoption(array $animal, array $values): string
    {
        $sections = [
            ['title'=>'Una persona detrás de esta decisión','layout'=>'facts','fields'=>['nombre_completo','edad','telefono','correo']],
            ['title'=>'El vínculo que imaginas','fields'=>['motivo','experiencia_previa']],
            ['title'=>'Un hogar para acompañar su camino','new_page'=>true,'fields'=>['condiciones_hogar','disponibilidad_responsabilidades','informacion_adicional']],
        ];
        $document = [
            'title'=>'Una nueva historia, con cuidado.',
            'subtitle'=>'Ficha de preparación para adopción',
            'subject'=>$animal['nombre'],
            'reference'=>strtoupper(bin2hex(random_bytes(4))),
            'date'=>date('d/m/Y · H:i'),
            'image'=>self::animalImage($animal['foto']),
            'logo'=>'data:image/svg+xml;base64,'.base64_encode((string)file_get_contents(BASE_PATH.'/public/assets/img/cursor-patita.svg')),
        ];
        $labels = array_map(fn($field)=>$field['label'], AdoptionPreparation::FIELDS);
        return self::render($document, $sections, $values, $labels);
    }

    public static function render(array $document, array $sections, array $values, array $labels): string
    {
        if (!class_exists(Dompdf::class)) throw new \RuntimeException('El generador de documentos no está instalado.');
        $temporary = BASE_PATH.'/storage/framework/pdf';
        if (!is_dir($temporary) && !mkdir($temporary,0700,true) && !is_dir($temporary)) throw new \RuntimeException('No se pudo preparar el documento.');
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);
        $options->set('isJavascriptEnabled', false);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('tempDir', $temporary);
        $options->set('fontCache', $temporary);
        $options->setChroot([BASE_PATH.'/public/assets/img',BASE_PATH.'/storage/app/public']);
        $pdf = new Dompdf($options);
        ob_start();
        try { require BASE_PATH.'/app/Views/pdf/prepared-document.php'; $html=(string)ob_get_clean(); }
        catch (\Throwable $error) { ob_end_clean();throw $error; }
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4');
        $pdf->render();
        $font = $pdf->getFontMetrics()->getFont('DejaVu Sans');
        $pdf->getCanvas()->page_text(455,810,'{PAGE_NUM} / {PAGE_COUNT}',$font,8,[.32,.40,.37]);
        return $pdf->output();
    }

    private static function animalImage(?string $path): ?string
    {
        if (!is_string($path) || !preg_match('~^animales/([a-f0-9]{32}\.(jpg|png|webp))$~D', $path, $match)) return null;
        $file = BASE_PATH.'/storage/app/public/'.$path;
        if (!is_file($file) && $match[2] === 'webp') $file=BASE_PATH.'/public/assets/img/companions-art/'.$match[1];
        if (!is_file($file) || filesize($file) > 5*1024*1024) return null;
        $dimensions=@getimagesize($file);
        if (!$dimensions || $dimensions[0]*$dimensions[1] > 24000000 || !in_array($dimensions['mime'] ?? '', ['image/jpeg','image/png','image/webp'],true)) return null;
        $source = @imagecreatefromstring((string)file_get_contents($file));
        if (!$source) return null;
        $width=imagesx($source);$height=imagesy($source);$scale=min(1,520/$width);
        $image=imagecreatetruecolor(max(1,(int)round($width*$scale)),max(1,(int)round($height*$scale)));
        imagecopyresampled($image,$source,0,0,0,0,imagesx($image),imagesy($image),$width,$height);
        ob_start();imagejpeg($image,null,82);$bytes=ob_get_clean();
        imagedestroy($source);imagedestroy($image);
        return 'data:image/jpeg;base64,'.base64_encode($bytes);
    }
}
