<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Database;
use App\Core\Auth;
final class MediaController {
    public function serve(string $bucket,string $filename):void {
        if (!in_array($bucket,['animales','historias','refugio'],true) ||
            !preg_match('~^[a-f0-9]{32}\.(jpg|png|webp)$~D',$filename)) {
            http_response_code(404);return;
        }
        $file=BASE_PATH.'/storage/app/public/'.$bucket.'/'.$filename;
        if (!is_file($file)){http_response_code(404);return;}
        $allowed=Auth::check();
        if(!$allowed && \env('PUBLIC_CONTENT_ENABLED','false')==='true') {
            $path=$bucket.'/'.$filename;
            $query=match($bucket) {
                'animales'=>'SELECT 1 FROM animal_fotografias f JOIN animales a ON a.id=f.animal_id WHERE f.ruta=? AND a.archivado=0 LIMIT 1',
                'historias'=>'SELECT 1 FROM historia_fotografias f JOIN historias h ON h.id=f.historia_id JOIN animales a ON a.id=h.animal_id WHERE f.ruta=? AND h.publicada=1 AND a.archivado=0 LIMIT 1',
                'refugio'=>'SELECT 1 FROM refugio_fotografias WHERE ruta=? LIMIT 1',
            };
            $stmt=Database::connection()->prepare($query);
            $stmt->execute([$path]);$allowed=(bool)$stmt->fetchColumn();
        }
        if(!$allowed){http_response_code(404);return;}
        $mime=match(pathinfo($file,PATHINFO_EXTENSION)){'jpg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp',default=>'application/octet-stream'};
        header('Content-Type: '.$mime);
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=3600');
        header('Content-Length: '.filesize($file));
        readfile($file);
    }
}
