<section class="wrap section narrow"><span class="eyebrow">ADOPCIÓN RESPONSABLE</span><h1>Solicitar adopción de <?= e($animal['nombre']) ?></h1><p>El envío no significa aprobación automática. El refugio evaluará tu solicitud.</p>
<form action="/adopciones/solicitar/<?= (int)$animal['id'] ?>" method="post" class="form-card">
<?= csrf_field() ?>
<label>Nombre completo <input required maxlength="120" name="nombre_completo" autocomplete="name"></label>
<div class="form-row"><label>Edad <input required min="18" max="120" type="number" name="edad"></label><label>Teléfono <input required maxlength="30" name="telefono" autocomplete="tel"></label></div>
<label>Correo electrónico <input required type="email" maxlength="150" name="correo" autocomplete="email"></label>
<label>¿Por qué deseas adoptar? <textarea required maxlength="2000" name="motivo"></textarea></label>
<label>Experiencia previa con animales <textarea required maxlength="2000" name="experiencia_previa"></textarea></label>
<label>Condiciones de tu hogar <textarea required maxlength="2000" name="condiciones_hogar"></textarea></label>
<label>Disponibilidad y responsabilidades <textarea required maxlength="2000" name="disponibilidad_responsabilidades"></textarea></label>
<label>Información adicional <textarea maxlength="2000" name="informacion_adicional"></textarea></label>
<label class="check"><input required type="checkbox" name="acepta_privacidad" value="1"> Leí y acepto la <a href="/privacidad" target="_blank">política de privacidad</a>.</label>
<button class="btn btn-primary" type="submit">Enviar solicitud →</button>
</form></section>
