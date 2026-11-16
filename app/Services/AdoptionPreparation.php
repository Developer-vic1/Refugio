<?php
declare(strict_types=1);
namespace App\Services;

final class AdoptionPreparation
{
    public const FIELDS = [
        'nombre_completo'=>['label'=>'Nombre completo','max'=>120],
        'edad'=>['label'=>'Edad','max'=>3],
        'telefono'=>['label'=>'Teléfono','max'=>30],
        'correo'=>['label'=>'Correo electrónico','max'=>150],
        'motivo'=>['label'=>'Por qué quieres adoptar','max'=>1500],
        'experiencia_previa'=>['label'=>'Tu experiencia con animales','max'=>1500],
        'condiciones_hogar'=>['label'=>'El hogar que puedes ofrecer','max'=>1500],
        'disponibilidad_responsabilidades'=>['label'=>'Tiempo y responsabilidades','max'=>1500],
        'informacion_adicional'=>['label'=>'Algo más que quieras compartir','max'=>1000,'optional'=>true],
    ];

    public static function validate(array $input): array
    {
        $values = $errors = [];
        foreach (self::FIELDS as $name=>$field) {
            $raw = $input[$name] ?? '';
            $value = is_string($raw) ? trim($raw) : '';
            if (!preg_match('//u', $value)) $value = '';
            $values[$name] = mb_substr($value, 0, $field['max']);
            if ($value === '' && empty($field['optional'])) $errors[$name] = 'Completa este campo para continuar.';
            elseif (mb_strlen($value) > $field['max']) $errors[$name] = 'Usa como máximo '.$field['max'].' caracteres.';
        }
        $age = filter_var($values['edad'], FILTER_VALIDATE_INT);
        if ($age === false || $age < 18 || $age > 120) $errors['edad'] = 'Indica una edad entre 18 y 120 años.';
        if (!filter_var($values['correo'], FILTER_VALIDATE_EMAIL)) $errors['correo'] = 'Escribe un correo válido, como nombre@ejemplo.com.';
        $phone = $values['telefono'];
        $digits = preg_replace('/\D/', '', $phone);
        if (!preg_match('/^\+?[0-9 ()-]+$/D', $phone) || strlen($digits) < 7 || strlen($digits) > 15) $errors['telefono'] = 'Incluye entre 7 y 15 dígitos; puedes usar el prefijo de tu país.';
        $values['acepta_privacidad'] = ($input['acepta_privacidad'] ?? null) === '1' ? '1' : '';
        if ($values['acepta_privacidad'] !== '1') $errors['acepta_privacidad'] = 'Lee la información de privacidad y marca la casilla para preparar tu documento.';
        return ['values'=>$values,'errors'=>$errors];
    }
}
