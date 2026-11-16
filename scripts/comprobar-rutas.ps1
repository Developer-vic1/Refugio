param([string]$BaseUrl = 'http://refugio.test')
$checks = @('/', '/animales', '/adopciones', '/donaciones', '/campanas', '/historias', '/voluntariado', '/transparencia', '/admin/login', '/admin/tema', '/ruta-inexistente')
foreach ($route in $checks) {
    try {
        $result = Invoke-WebRequest -Uri "$BaseUrl$route" -UseBasicParsing -MaximumRedirection 0 -ErrorAction Stop
        Write-Host "$route  HTTP $($result.StatusCode)"
    } catch {
        $resp = $_.Exception.Response
        if ($resp) { Write-Host "$route  HTTP $([int]$resp.StatusCode)" }
        else { Write-Warning "$route : $($_.Exception.Message)" }
    }
}
