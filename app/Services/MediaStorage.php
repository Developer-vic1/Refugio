<?php
 declare(strict_types=1);
 namespace App\Services;
 use DomainException;
 final class MediaStorage {
   public static function upload(string $field,string $bucket):string {
      if(!in_array($bucket,['animales','historias','refugio'],true)) throw new DomainException('Categoría de archivo inválida');
      $upload=$_FILES[$field]??null;
      if(!is_array($upload) || ($upload['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK) throw new DomainException('Selecciona una imagen válida');
      if(!is_uploaded_file((string)$upload['tmp_name'])) throw new DomainException('Archivo no válido');
      $size=(int)$upload['size']; if($size<100 || $size>5*1024*1024) throw new DomainException('La imagen debe pesar entre 100 bytes y 5 MB');
      $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
      $ext=match($mime){'image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp',default=>null};
      if($ext===null) throw new DomainException('Solo JPG, PNG o WEBP');
      $dim=@getimagesize($upload['tmp_name']);
      if(!$dim || $dim[0]<120 || $dim[1]<120 || $dim[0]*$dim[1]>24000000) throw new DomainException('Dimensiones de la imagen no válidas');
      $name=bin2hex(random_bytes(16)).'.'.$ext;
      $dir=BASE_PATH.'/storage/app/public/'.$bucket;
      if(!is_dir($dir) && !mkdir($dir,0755,true) && !is_dir($dir)) throw new \RuntimeException('Carpeta de imágenes no disponible');
      if(!move_uploaded_file($upload['tmp_name'],$dir.'/'.$name)) throw new \RuntimeException('No se pudo guardar la imagen');
      @chmod($dir.'/'.$name,0644);
      return $bucket.'/'.$name;
   }
   public static function delete(string $path):void {
      if(!preg_match('~^(animales|historias|refugio)/[a-f0-9]{32}\.(jpg|png|webp)$~D',$path)) return;
      $file=BASE_PATH.'/storage/app/public/'.$path;
      if(is_file($file)) @unlink($file);
   }
 }
