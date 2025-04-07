<?php
namespace App\Controllers;

use React\Http\Message\Response;

class HomeController
{
    public function __invoke()
    {
        return new Response(
            200,
            ['Content-Type' => 'text/html'],
            file_get_contents(__DIR__ . '/../../public/index.html')
        );
    }
}