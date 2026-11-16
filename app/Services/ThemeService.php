<?php
 declare(strict_types=1);
 namespace App\Services;
 final class ThemeService {
     private const THEMES = ['bosque','arena','lila'];
     private static function path(): string { return BASE_PATH.'/storage/app/private/visual-settings.json'; }
     public static function current(): array {
         $file=self::path(); $value=is_file($file)?json_decode((string)file_get_contents($file),true):null;
         $theme=is_array($value)?($value['theme']??'bosque'):'bosque';
         $motion=is_array($value)?($value['motion']??'equilibrada'):'equilibrada';
         return ['theme'=>in_array($theme,self::THEMES,true)?$theme:'bosque','motion'=>in_array($motion,['serena','equilibrada','expresiva'],true)?$motion:'equilibrada'];
     }
     public static function save(string $theme,string $motion): void {
         if(!in_array($theme,self::THEMES,true)||!in_array($motion,['serena','equilibrada','expresiva'],true)) throw new \DomainException('Opción visual inválida');
         $path=self::path(); $dir=dirname($path);
         if(!is_dir($dir) && !mkdir($dir,0750,true) && !is_dir($dir)) throw new \RuntimeException('No se pudo preparar el almacenamiento');
         $tmp=tempnam($dir,'cfg_');
         if($tmp===false) throw new \RuntimeException('No se pudo crear archivo temporal');
         try {
           if(file_put_contents($tmp,json_encode(['theme'=>$theme,'motion'=>$motion],JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR),LOCK_EX)===false) throw new \RuntimeException('No se pudo guardar la preferencia');
           if(!rename($tmp,$path)) throw new \RuntimeException('No se pudo actualizar el tema');
         } finally { if(file_exists($tmp)) unlink($tmp); }
     }
 }
