<?php
declare(strict_types=1);
namespace App\Services;

/** Solo recursos de revisión local; nunca sirve SVG subidos por el público. */
final class ReviewMedia
{
    public static function filename(string $path): ?string
    {
        return preg_match('~^(?:uploads/)?animales/(animal_[0-9]{3}_[0-9]{2}\.svg)$~D', $path, $match) ? $match[1] : null;
    }

    public static function safeSvg(string $filename): ?string
    {
        if (!preg_match('~^animal_[0-9]{3}_[0-9]{2}\.svg$~D', $filename)) return null;
        $file = BASE_PATH.'/storage/app/review/animales/'.$filename;
        if (!is_file($file) || filesize($file) > 65536) return null;
        $source = file_get_contents($file);
        if ($source === false || preg_match('~<!|<\?~', $source)) return null;
        $previous = libxml_use_internal_errors(true);
        try {
            $document = new \DOMDocument();
            if (!$document->loadXML($source, LIBXML_NONET) || $document->documentElement?->localName !== 'svg') return null;
            $tags = ['svg','g','path','rect','circle','ellipse','line','polyline','polygon','text','title','desc'];
            $attributes = ['xmlns','viewBox','role','aria-label','width','height','x','y','x1','y1','x2','y2','cx','cy','r','rx','ry','d','points','fill','stroke','stroke-width','opacity','transform','font-family','font-size','font-weight','text-anchor'];
            foreach ($document->getElementsByTagName('*') as $element) {
                if (!in_array($element->localName, $tags, true) || $element->namespaceURI !== 'http://www.w3.org/2000/svg') return null;
                foreach ($element->attributes as $attribute) {
                    if (!in_array($attribute->name, $attributes, true) || preg_match('~url\s*\(|javascript:|data:~i', $attribute->value)) return null;
                }
            }
            return $document->saveXML($document->documentElement) ?: null;
        } finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
    }
}
