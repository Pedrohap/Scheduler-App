<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

class SessionGlobalMiddleware
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Handler $handler): ResponseInterface
    {
        $this->twig->getEnvironment()->addGlobal('session', $_SESSION ?? []);
        return $handler->handle($request);
    }
}
