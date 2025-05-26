<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function __invoke($request, $handler)
    {
        if (!isset($_SESSION['usuario_id'])) {
            // Redireciona para o login
            header('Location: /login');
            exit;
        }

        return $handler->handle($request);
    }
}