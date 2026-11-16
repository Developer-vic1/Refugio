<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__).'/bootstrap.php';

use App\Core\Database;

// Importación explícita de imágenes locales; sin alteraciones de esquema.
if (env('APP_ENV', 'production') !== 'local') throw new RuntimeException('Esta importación solo se admite en local.');
$manifestPath = realpath($argv[1] ?? '');
$outputRoot = realpath(BASE_PATH.'/output/imagegen');
if (!$manifestPath || !$outputRoot || !str_starts_with($manifestPath, $outputRoot.DIRECTORY_SEPARATOR)) throw new RuntimeException('Manifiesto local no válido.');
$manifest = json_decode(ltrim((string)file_get_contents($manifestPath), "\xEF\xBB\xBF"), true, 512, JSON_THROW_ON_ERROR);
if (!is_array($manifest) || count($manifest) < 1 || count($manifest) > 500) throw new RuntimeException('Lote no válido.');
$apply = in_array('--apply', $argv, true);
$db = Database::connection();
$entries = [];
$seenAnimals = $seenPhotos = $seenImages = [];
foreach ($manifest as $item) {
    $animal = filter_var($item['animal_id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
    $photo = filter_var($item['photo_id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
    $relative = (string)($item['file'] ?? '');
    if (!$animal || !$photo || isset($seenAnimals[$animal]) || isset($seenPhotos[$photo]) || !preg_match('~^output/imagegen/portraits/animal-[0-9]{3}\.webp$~D', $relative)) throw new RuntimeException('Identificadores o ruta no válidos.');
    $source = realpath(BASE_PATH.'/'.$relative);
    $portraitsRoot = realpath(BASE_PATH.'/output/imagegen/portraits');
    if (!$source || !$portraitsRoot || !str_starts_with($source, $portraitsRoot.DIRECTORY_SEPARATOR) || filesize($source) > 5*1024*1024) throw new RuntimeException('Archivo de retrato no válido.');
    $dimensions = getimagesize($source);
    if (!$dimensions || ($dimensions['mime'] ?? '') !== 'image/webp' || $dimensions[0] < 300 || $dimensions[1] < 250 || $dimensions[0]*$dimensions[1] > 24000000) throw new RuntimeException('Dimensiones o formato no válidos.');
    $digest = hash_file('sha256', $source);
    if (!hash_equals((string)($item['sha256'] ?? ''), $digest) || isset($seenImages[$digest])) throw new RuntimeException('La imagen cambió o se repite dentro del lote.');
    $seenAnimals[$animal] = $seenPhotos[$photo] = $seenImages[$digest] = true;
    $entries[] = ['animal_id'=>$animal,'photo_id'=>$photo,'source'=>$source,'digest'=>$digest,'expected_route'=>(string)($item['expected_route'] ?? ''),'expected_alt'=>$item['expected_alt'] ?? null,'new_route'=>'animales/'.substr($digest,0,32).'.webp'];
}
usort($entries, fn($a,$b) => $a['photo_id'] <=> $b['photo_id']);
$select = $db->prepare('SELECT f.id,f.animal_id,f.ruta,f.texto_alternativo,a.nombre FROM animal_fotografias f JOIN animales a ON a.id=f.animal_id WHERE f.id=? AND f.animal_id=? AND a.archivado=0'.($apply ? ' FOR UPDATE' : ''));
$update = $db->prepare('UPDATE animal_fotografias SET ruta=?,texto_alternativo=? WHERE id=? AND animal_id=?');
$before = $pending = [];
$directory = BASE_PATH.'/public/assets/img/companions-art';
try {
    if ($apply) $db->beginTransaction();
    foreach ($entries as $item) {
        $select->execute([$item['photo_id'],$item['animal_id']]);
        $row = $select->fetch();
        $newAlt = $row ? 'Retrato artístico de '.$row['nombre'] : '';
        if ($row && $row['ruta'] === $item['new_route'] && $row['texto_alternativo'] === $newAlt) continue;
        if (!$row || $row['ruta'] !== $item['expected_route'] || $row['texto_alternativo'] !== $item['expected_alt']) throw new RuntimeException('Una ficha cambió durante la preparación; se cancela el lote completo.');
        $before[] = $row + ['new_route'=>$item['new_route']];
        $pending[] = $item + ['new_alt'=>$newAlt];
    }
    if (!$apply) { echo count($entries)." retratos válidos; ".count($pending)." nuevos. Simulación sin cambios. Usa --apply para importar.\n"; exit; }
    if (!$pending) { $db->commit(); echo "El lote ya está asociado; no se hicieron cambios.\n"; exit; }
    $backup = $outputRoot.'/animal-art-before-'.date('Ymd-His').'-'.bin2hex(random_bytes(3)).'.json';
    if (file_put_contents($backup, json_encode($before, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR), LOCK_EX) === false) throw new RuntimeException('No se pudo preservar el inventario anterior.');
    @chmod($backup,0600);
    if (!is_dir($directory) && !mkdir($directory,0755,true) && !is_dir($directory)) throw new RuntimeException('No se pudo preparar el almacenamiento.');
    foreach ($pending as $item) {
        $destination = $directory.'/'.basename($item['new_route']);
        if (is_file($destination)) {
            if (!hash_equals($item['digest'],hash_file('sha256',$destination))) throw new RuntimeException('Colisión de archivo; no se sobrescribe.');
        } elseif (!copy($item['source'],$destination)) throw new RuntimeException('No se pudo copiar un retrato.');
        if (!hash_equals($item['digest'],hash_file('sha256',$destination))) throw new RuntimeException('La copia de un retrato no coincide con su origen.');
        $update->execute([$item['new_route'],$item['new_alt'],$item['photo_id'],$item['animal_id']]);
        if ($update->rowCount() !== 1) throw new RuntimeException('No se actualizó una ficha; se revierte el lote.');
    }
    $db->commit();
    echo count($pending)." retratos asociados. Inventario anterior: ".$backup."\n";
} catch (Throwable $error) {
    if ($db->inTransaction()) $db->rollBack();
    throw $error;
}
