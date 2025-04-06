<?php
namespace Handlers;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class ContactHandler {
    public static function handle(ServerRequestInterface $request) {
        $htmlPath = __DIR__ . '/../../public/contact.html';

        if (!file_exists($htmlPath)) {
            return new Response(500, ['Content-Type' => 'text/plain'], 'Error: No se encontró contact.html');
        }

        $html = file_get_contents($htmlPath);
        return new Response(
            200,
            ['Content-Type' => 'text/html'],
            $html
        );
    }
}
