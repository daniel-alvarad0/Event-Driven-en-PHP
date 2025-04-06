<?php
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

use Handlers\HomeHandler;
use Handlers\ContactHandler;
use Handlers\DataHandler;

class Router {
    public static function route(ServerRequestInterface $request) {
        $path = $request->getUri()->getPath();

        return match ($path) {
            '/' => HomeHandler::handle($request),
            '/contact' => ContactHandler::handle($request),
            '/data' => DataHandler::handle($request),
            '/style.css' => self::serveStaticFile('/../public/style.css', 'text/css'),
            '/js/script.js' => self::serveStaticFile('/../public/js/script.js', 'application/javascript'),
            default => new Response(404, ['Content-Type' => 'text/plain'], '404 - Página no encontrada')
        };
    }

    private static function serveStaticFile(string $relativePath, string $contentType) {
        $path = __DIR__ . $relativePath;

        if (!file_exists($path)) {
            return new Response(404, ['Content-Type' => 'text/plain'], 'Archivo no encontrado');
        }

        return new Response(200, ['Content-Type' => $contentType], file_get_contents($path));
    }
}
