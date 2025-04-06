<?php
namespace Handlers;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class HomeHandler {
    public static function handle(ServerRequestInterface $request) {
        $htmlPath = __DIR__ . '/../../public/index.html';

        if (!file_exists($htmlPath)) {
            return new Response(500, ['Content-Type' => 'text/plain'], 'Error: No se encontró index.html');
        }

        $html = file_get_contents($htmlPath);
        return new Response(
            200,
            ['Content-Type' => 'text/html'],
            $html
        );
    }
}
