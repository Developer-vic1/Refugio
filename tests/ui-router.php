<?php
$root = dirname(__DIR__);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/ui-check') { require $root.'/tests/fixtures/ui.php'; return true; }
if ($path === '/fixture-events.js') {
 header('Content-Type: application/javascript');
 echo "import '/assets/js/components/feedback.js'; let count=0; document.addEventListener('submit', event => { if(event.target.id!=='test-confirm'||event.defaultPrevented)return; event.preventDefault(); count++; document.getElementById('test-result').textContent='Envíos confirmados: '+count+'; acción: '+event.submitter?.value; });";
 return true;
}
if (str_starts_with($path, '/assets/') && !str_contains($path, '..')) return false;
http_response_code(404); return true;
