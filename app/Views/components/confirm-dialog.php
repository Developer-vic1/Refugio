<dialog class="confirm-dialog" data-confirm-dialog aria-labelledby="confirm-title" aria-describedby="confirm-message">
    <form method="dialog">
        <span class="dialog-icon"><?= icon('alert-triangle') ?></span>
        <h2 id="confirm-title">Confirmar operación</h2>
        <p id="confirm-message" data-dialog-message></p>
        <div class="dialog-actions"><button value="cancel" class="btn btn-outline" autofocus>Cancelar</button><button value="confirm" class="btn btn-danger">Confirmar</button></div>
    </form>
</dialog>
