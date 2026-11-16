<?php
 declare(strict_types=1);
 namespace App\Controllers;
 use App\Services\ThemeService;
 final class ThemeController {
    public function index():void { \page('admin/theme',['title'=>'Identidad visual','settings'=>ThemeService::current()]); }
    public function save():void {
       try {ThemeService::save((string)($_POST['theme']??''),(string)($_POST['motion']??''));\App\Services\AuditService::record('actualizar_tema','configuracion_visual',0);\flash('Identidad visual actualizada correctamente.');}
       catch(\DomainException $ex) {\flash($ex->getMessage(), 'error');}
       \redirect('/admin/tema');
    }
 }
