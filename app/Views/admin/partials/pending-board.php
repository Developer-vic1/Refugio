<section class="workspace-priorities" id="pendientes" aria-labelledby="pending-title">
    <div class="panel-head"><div><span class="micro-label">LO QUE SIGUE</span><h2 id="pending-title">Cada pendiente merece atención.</h2></div><span class="admin-tag"><?= (int)$workspace['counts']['pendientes'] ?> registros por atender</span></div>
    <div class="workspace-priority-grid">
    <?php foreach ([['salud','paw','Evaluaciones de salud','/admin/animales','Animales sin evaluación inicial.'],['adopciones','heart','Adopciones','/admin/adopciones','Solicitudes nuevas o en revisión.'],['aportes','coins','Aportes','/admin/aportes','Requieren verificar el ingreso real.'],['voluntariado','users','Voluntariado','/admin/voluntariado','Postulaciones pendientes.'],['materiales','box','Donaciones materiales','/admin/donaciones-materiales','Entrega o recepción por coordinar.'],['gastos','chart','Gastos','/admin/gastos','Registros sin confirmación.'],['inventario','box','Stock bajo','/admin/inventario','Productos por debajo del mínimo.']] as [$key,$glyph,$label,$href,$help]): ?>
        <a class="workspace-priority" href="<?= e($href) ?>"><?= icon($glyph) ?><div><h3><?= e($label) ?></h3><p><?= e($help) ?></p></div><strong><?= (int)$workspace['counts'][$key] ?></strong><?= icon('arrow-up-right') ?></a>
    <?php endforeach ?>
    </div>
</section>
