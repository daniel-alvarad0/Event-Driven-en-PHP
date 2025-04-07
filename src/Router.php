<?php

namespace App;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use React\Http\Message\Response;
use App\Controllers\HomeController;
use App\Controllers\ContactController;
use App\Controllers\DataController;
use App\Controllers\DataPageController;
use Psr\Http\Message\ServerRequestInterface;
use React\Filesystem\Filesystem;
use React\Filesystem\FilesystemInterface;
use React\Promise\PromiseInterface;

use function FastRoute\simpleDispatcher;

class Router
{
    private $dispatcher;
    private $dataController;
    private $publicDir;

    /**
     * Constructor del router
     * 
     * @param DataController $dataController Controlador para operaciones CRUD
     */
    public function __construct(DataController $dataController)
    {
        $this->dataController = $dataController;
        $this->publicDir = __DIR__ . '/../public';

        $this->dispatcher = simpleDispatcher(function (RouteCollector $r) {
            // Rutas básicas
            $r->get('/', new HomeController());
            $r->get('/contact', new ContactController());
            $r->get('/data-page', new DataPageController());

            // Rutas para operaciones CRUD
            $r->get('/data', [$this->dataController, 'getAll']);
            $r->post('/data', [$this->dataController, 'create']);
            $r->get('/data/{id:\d+}', [$this->dataController, 'getById']);
            $r->put('/data/{id:\d+}', [$this->dataController, 'update']);
            $r->delete('/data/{id:\d+}', [$this->dataController, 'delete']);
        });
    }

    /**
     * Maneja las solicitudes HTTP
     * 
     * @param ServerRequestInterface $request Solicitud HTTP
     * @return Response|PromiseInterface Respuesta HTTP
     */
    public function __invoke(ServerRequestInterface $request)
    {
        return $this->handle($request);
    }

    /**
     * Maneja las solicitudes HTTP
     * 
     * @param ServerRequestInterface $request Solicitud HTTP
     * @return Response|PromiseInterface Respuesta HTTP
     */
    public function handle(ServerRequestInterface $request)
    {
        $path = $request->getUri()->getPath();

        // Verificar si es una solicitud de archivo estático
        if ($this->isStaticFile($path)) {
            return $this->serveStaticFile($path);
        }

        $routeInfo = $this->dispatcher->dispatch(
            $request->getMethod(),
            $path
        );

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return new Response(404, ['Content-Type' => 'text/plain'], '404 No Encontrado');
            case Dispatcher::METHOD_NOT_ALLOWED:
                return new Response(405, ['Content-Type' => 'text/plain'], '405 Método No Permitido');
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                if (in_array($request->getMethod(), ['POST', 'PUT'])) {
                    $body = json_decode((string)$request->getBody(), true);
                    return $handler($vars + ['data' => $body ?? []]);
                }

                return $handler($vars);
        }
    }

    // Verificar si la ruta corresponde a un archivo estático
    private function isStaticFile(string $path): bool
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return in_array($extension, ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico']);
    }

    // Servir un archivo estático
    private function serveStaticFile(string $path): PromiseInterface
    {
        $filePath = $this->publicDir . $path;

        if (!file_exists($filePath)) {
            return \React\Promise\resolve(new Response(404, ['Content-Type' => 'text/plain'], '404 Archivo no encontrado'));
        }

        $content = file_get_contents($filePath);
        $contentType = $this->getContentType($path);

        return \React\Promise\resolve(new Response(200, ['Content-Type' => $contentType], $content));
    }

    // Obtener el tipo de contenido basado en la extensión del archivo
    private function getContentType(string $path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $contentTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];

        return $contentTypes[$extension] ?? 'application/octet-stream';
    }
}
