<?php
declare(strict_types=1);
namespace App\Controllers;
final class AdminModuleController {
    public function animales():void {(new AdminController())->listing('animales');}
    public function adopciones():void {(new AdminController())->listing('adopciones');}
    public function campanas():void {(new AdminController())->listing('campanas');}
    public function aportes():void {(new AdminController())->listing('aportes');}
    public function donacionesMateriales():void {(new AdminController())->listing('donaciones-materiales');}
    public function inventario():void {(new AdminController())->listing('inventario');}
    public function historias():void {(new AdminController())->listing('historias');}
    public function voluntariado():void {(new AdminController())->listing('voluntariado');}
    public function transparencia():void {(new AdminController())->listing('transparencia');}
    public function gastos():void {(new AdminController())->listing('gastos');}
    public function configuracion():void {(new AdminController())->listing('configuracion');}
    public function auditoria():void {(new AdminController())->listing('auditoria');}
}
