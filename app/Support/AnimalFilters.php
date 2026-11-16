<?php
declare(strict_types=1);
namespace App\Support;

final class AnimalFilters
{
    public const SEX = ['macho'=>'Macho','hembra'=>'Hembra','desconocido'=>'Por confirmar'];
    public const SIZE = ['pequeno'=>'Pequeño','mediano'=>'Mediano','grande'=>'Grande','no_determinado'=>'Por confirmar'];
    public const HEALTH = ['pendiente_evaluacion'=>'Por evaluar','en_tratamiento'=>'En tratamiento','en_recuperacion'=>'En recuperación','estable'=>'Estable'];
    public const STATUS = ['disponible'=>'Disponible','en_evaluacion'=>'En evaluación','no_disponible'=>'En cuidado','adoptado'=>'Adoptado'];
    public const AGE = ['cachorro'=>'Menos de un año','adulto'=>'De 1 a 7 años','mayor'=>'8 años o más','sin_dato'=>'Edad por confirmar'];
    public const SORT = ['recientes'=>'Llegadas recientes','nombre'=>'Nombre: A a Z','menor_edad'=>'Menor edad primero','mayor_edad'=>'Mayor edad primero'];

    public function __construct(public readonly array $values, public readonly int $page = 1) {}

    public static function fromQuery(array $query): self
    {
        $values = [];
        foreach (['q'=>80,'especie'=>50,'sexo'=>25,'tamano'=>25,'salud'=>30,'estado'=>25,'edad'=>20,'orden'=>20] as $name=>$length) {
            $values[$name] = is_string($query[$name] ?? null) ? mb_substr(trim($query[$name]), 0, $length) : '';
        }
        foreach (['sexo'=>self::SEX,'tamano'=>self::SIZE,'salud'=>self::HEALTH,'estado'=>self::STATUS,'edad'=>self::AGE,'orden'=>self::SORT] as $key=>$allowed) {
            if (!isset($allowed[$values[$key]])) $values[$key] = $key === 'orden' ? 'recientes' : '';
        }
        $page = filter_var($query['pagina'] ?? 1, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>100000]]) ?: 1;
        return new self($values, $page);
    }

    public function get(string $key): string { return $this->values[$key] ?? ''; }
    public function hasFilters(): bool { return count(array_filter($this->values, fn($value, $key) => $key !== 'orden' && $value !== '', ARRAY_FILTER_USE_BOTH)) > 0; }
    public function href(int $page = 1): string
    {
        $query = array_filter($this->values, fn($value) => $value !== '');
        if (($query['orden'] ?? '') === 'recientes') unset($query['orden']);
        if ($page > 1) $query['pagina'] = $page;
        return '/animales'.($query ? '?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986) : '');
    }
}
