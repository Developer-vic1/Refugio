<?php
declare(strict_types=1);
namespace App\Core;
final class Router {
    private array $routes = [];
    public function get(string $path, array $handler, bool $auth = false): void { $this->add('GET',$path,$handler,$auth); }
    public function post(string $path, array $handler, bool $auth = false): void { $this->add('POST',$path,$handler,$auth); }
    private function add(string $method,string $path,array $handler,bool $auth): void {
        $regex = preg_replace('/\\{([a-z][a-z0-9_]*)\\}/', '(?P<$1>[a-zA-Z0-9._-]+)', $path);
        $this->routes[] = [$method,$path,'~^'.$regex.'/?$~D',$handler,$auth];
    }
    public function dispatch(string $method,string $uri): void {
        $path = rawurldecode(parse_url($uri,PHP_URL_PATH) ?: '/');
        if (str_contains($path, "\0") || str_contains($path, '..')) { http_response_code(400); exit('Ruta inválida'); }
        foreach ($this->routes as [$verb,$name,$pattern,$handler,$auth]) {
            if ($verb !== $method || !preg_match($pattern,$path,$matches)) continue;
            if ($auth && !Auth::check()) \redirect('/admin/login');
            if ($method === 'POST') \verify_csrf();
            $controller = new $handler[0]();
            $args = array_values(array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY));
            $controller->{$handler[1]}(...$args);
            return;
        }
        \page('pages/status',['title'=>'Página no encontrada','message'=>'La dirección solicitada no existe.'],404);
    }
}
