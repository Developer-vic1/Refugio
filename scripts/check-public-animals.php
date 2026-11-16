<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';

use App\Core\Database;
use App\Repositories\AnimalRepository;
use App\Services\ContentVisibility;
use App\Services\ReviewMedia;
use App\Support\AnimalFilters;

function ensure(bool $condition, string $label): void { if (!$condition) throw new RuntimeException($label); }
$repo = new AnimalRepository();
$db = Database::connection();
$originalServer = $_SERVER;
$originalEnv = [];
foreach (['APP_ENV','PUBLIC_CONTENT_ENABLED','LOCAL_REVIEW_MODE'] as $key) $originalEnv[$key] = getenv($key);
try {
    putenv('APP_ENV=local');putenv('PUBLIC_CONTENT_ENABLED=false');putenv('LOCAL_REVIEW_MODE=true');
    $_SERVER['REMOTE_ADDR']='127.0.0.1';$_SERVER['HTTP_HOST']='127.0.0.1:8000';
    ensure(ContentVisibility::localReview(), 'Revisión local disponible');
    $_SERVER['REMOTE_ADDR']='192.0.2.2';
    ensure(!ContentVisibility::visible(), 'Sin revisión desde otra dirección');
    $_SERVER['REMOTE_ADDR']='127.0.0.1';$_SERVER['HTTP_HOST']='example.com';
    ensure(!ContentVisibility::visible(), 'Sin revisión en host externo');
    $_SERVER['HTTP_HOST']='localhost';$_SERVER['HTTP_X_FORWARDED_FOR']='192.0.2.2';
    ensure(!ContentVisibility::visible(), 'Sin revisión por proxy');
    unset($_SERVER['HTTP_X_FORWARDED_FOR']);
    putenv('APP_ENV=production');
    ensure(!ContentVisibility::visible(), 'Sin revisión en producción');
    putenv('APP_ENV=local');
    $total = (int)$db->query('SELECT COUNT(*) FROM animales WHERE archivado=0')->fetchColumn();
    $first = $repo->search(AnimalFilters::fromQuery([]));
    ensure($first['total'] === $total && count($first['items']) <= 12, 'Total y límite SQL');
    if ($total > 12) {
        $second = $repo->search(AnimalFilters::fromQuery(['pagina'=>'2']));
        ensure(!array_intersect(array_column($first['items'],'id'), array_column($second['items'],'id')), 'Sin repetición entre páginas');
    }
    foreach ($repo->filterOptions()['estado_adopcion'] as $state) {
        $stmt=$db->prepare('SELECT COUNT(*) FROM animales WHERE archivado=0 AND estado_adopcion=?');$stmt->execute([$state]);
        $result=$repo->search(AnimalFilters::fromQuery(['estado'=>$state,'pagina'=>'99999']));
        ensure($result['total'] === (int)$stmt->fetchColumn(), 'Correspondencia con estado real');
        ensure($result['page'] <= $result['pages'], 'Página fuera de rango ajustada');
        foreach ($result['items'] as $item) ensure($item['estado_adopcion']===$state, 'Filtro aplicado antes de paginar');
    }
    foreach (["%' OR 1=1 --", '%', '_'] as $term) {
        $result=$repo->search(AnimalFilters::fromQuery(['q'=>$term]));
        ensure($result['total'] <= $total, 'Consulta parametrizada');
        if ($term === '%') {
            $literal = (int)$db->query("SELECT COUNT(*) FROM animales WHERE archivado=0 AND (INSTR(nombre,'%')>0 OR INSTR(especie,'%')>0 OR INSTR(raza,'%')>0 OR INSTR(resumen,'%')>0)")->fetchColumn();
            ensure($result['total'] === $literal, 'Comodín de usuario tratado literalmente');
        }
    }
    $filters=AnimalFilters::fromQuery(['q'=>['inválido'],'especie'=>[],'pagina'=>[],'orden'=>'SQL','sexo'=>'otro']);
    ensure($filters->get('q')==='' && $filters->get('orden')==='recientes' && $filters->page===1, 'Entrada malformada normalizada');
    ensure(ReviewMedia::filename('../animal_001_01.svg') === null && ReviewMedia::safeSvg('../animal_001_01.svg') === null, 'Traversal bloqueado');
    $f=AnimalFilters::fromQuery(['especie'=>'gato','sexo'=>'hembra','pagina'=>2]);
    ensure(str_contains($f->href(3),'especie=gato') && str_contains($f->href(3),'pagina=3'), 'Filtros preservados al paginar');
    echo "Catálogo: lectura SQL, estados, páginas, entradas malformadas y aislamiento local correctos.\n";
} finally {
    $_SERVER=$originalServer;
    foreach ($originalEnv as $key=>$value) $value === false ? putenv($key) : putenv($key.'='.$value);
}
