<?php
declare(strict_types=1);
namespace App\Services;
use DomainException;
final class PublicCoordination
{
    public const HELP=['materiales'=>'Materiales y artículos de cuidado','alimento'=>'Alimento y utensilios','aporte'=>'Consultar sobre un aporte económico','otra'=>'Otra idea para colaborar'];
    public const ROLES=['cuidado'=>'Acompañamiento y cuidado','organizacion'=>'Organización y logística','comunicacion'=>'Comunicación y difusión','otra'=>'Compartir otra habilidad'];
    public const TIMES=['por_coordinar'=>'Lo conversamos','manana'=>'Mañanas','tarde'=>'Tardes','fin_semana'=>'Fines de semana'];
    public const UNITS=['unidades'=>'unidades','kg'=>'kg','bolsas'=>'bolsas','cajas'=>'cajas','litros'=>'litros','otro'=>'otra unidad'];
    private static function text(array $input,string $key,int $max=600): string
    {
        $value=$input[$key]??'';
        if(!is_string($value) || !preg_match('//u',$value) || mb_strlen($value)>$max)throw new DomainException('Revisa la extensión y el contenido de tu mensaje.');
        return trim($value);
    }
    public static function prepare(array $input): array
    {
        $kind=self::text($input,'kind',20);
        if(!in_array($kind,['ayuda','voluntariado'],true))throw new DomainException('Elige una forma de colaborar.');
        $type=self::text($input,'tipo',30);
        $types=$kind==='ayuda'?self::HELP:self::ROLES;
        if(!isset($types[$type]))throw new DomainException('Selecciona una opción para continuar.');
        $lines=['Hola, quisiera '.($kind==='ayuda'?'coordinar una ayuda para ':'sumar mi tiempo a ').SiteIdentity::name().'.','Me interesa: '.$types[$type].'.'];
        $initiative=self::text($input,'linea',80);
        if($initiative!==''){
            $item=PublicInitiatives::find($initiative);
            if(!$item)throw new DomainException('Selecciona una iniciativa disponible.');
            $lines[]='Iniciativa: '.$item['title'].'.';
        }
        $quantity=self::text($input,'cantidad',15);
        if($kind==='ayuda' && $type!=='aporte' && $quantity!==''){
            if(!is_numeric($quantity) || !is_finite((float)$quantity) || (float)$quantity<=0 || (float)$quantity>100000)throw new DomainException('Indica una cantidad mayor que cero y hasta 100000.');
            $unit=self::text($input,'unidad',20);
            if(!isset(self::UNITS[$unit]))throw new DomainException('Selecciona una unidad.');
            $lines[]='Cantidad orientativa: '.$quantity.' '.self::UNITS[$unit].'.';
        }
        if($kind==='voluntariado'){
            $time=self::text($input,'franja',30);
            if(!isset(self::TIMES[$time]))throw new DomainException('Selecciona tu disponibilidad.');
            $lines[]='Disponibilidad: '.self::TIMES[$time].'.';
        }
        $date=self::text($input,'fecha',10);
        if($date!==''){
            $parsed=\DateTimeImmutable::createFromFormat('!Y-m-d',$date);
            if(!$parsed || $parsed->format('Y-m-d')!==$date || $parsed<new \DateTimeImmutable('today') || $parsed>new \DateTimeImmutable('+2 years'))throw new DomainException('Elige una fecha orientativa a partir de hoy y dentro de los próximos dos años.');
            $lines[]='Fecha orientativa: '.$parsed->format('d/m/Y').'.';
        }
        $detail=self::text($input,'detalle',600);
        if($detail!=='')$lines[]=$detail;
        $lines[]='¿Podemos conversar para acordar los siguientes pasos?';
        $message=implode("\n\n",$lines);
        return ['title'=>'Tu mensaje está preparado','message'=>$message,'whatsapp'=>SiteIdentity::whatsapp($message),'back'=>$kind==='ayuda'?'/donaciones#coordinar':'/voluntariado#sumarme'];
    }
}
