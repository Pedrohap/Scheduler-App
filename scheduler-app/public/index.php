<?php

session_start();



require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UsuarioController;
use App\Middleware\AuthMiddleware;
use App\Middleware\SessionGlobalMiddleware;

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

$app = AppFactory::create();

// Iniciando Twig
$twig = Twig::create(__DIR__ . '/../src/Views', ['cache' => false]);

// Adiciona middlewares
$app->add(TwigMiddleware::create($app, $twig));
$app->add(new SessionGlobalMiddleware($twig));

// Rota usando Twig
$app->get('/', function ($request, $response, $args) use ($twig) {
    return $twig->render($response, 'home.twig', [
        'title' => 'Início'
    ]);
});

$app->get('/login', function ($request, $response, $args) use ($twig) {
    return $twig->render($response, 'login.twig', [
        'title' => 'Login'
    ]);
});

$app->post('/login', function ($request, $response, $args) use ($twig) {
    $controller = new UsuarioController();

    $result = $controller->autenticar($request);

    if ($result['success']){
        $twig->getEnvironment()->addGlobal('session', $_SESSION);
        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }

    return $twig->render($response, 'login.twig', [
        'title' => 'Login',
        'message' => 'Email ou senha inválidos.',
        'alertType' => 'warning'
    ]);
});

$app->get('/logout', function ($request, $response, $args) use ($twig) {
    $controller = new UsuarioController();

    $controller->logout();


    return $response
        ->withHeader('Location', '/login')
        ->withStatus(302);

})->add(new AuthMiddleware());

$app->get('/registro', function ($request, $response, $args) use ($twig) {
    return $twig->render($response, 'registrar.twig', [
            'title' => 'Registro',
    ]);
});

$app->post('/registro', function ($request, $response, $args) use ($twig) {
    $controller = new UsuarioController();

    $dados = $request->getParsedBody();

    if (
        isset($dados['nome_usuario'], $dados['nome_completo'], $dados['email'], $dados['senha']) &&
        !empty($dados['nome_usuario']) &&
        !empty($dados['nome_completo']) &&
        !empty($dados['email']) &&
        !empty($dados['senha']) &&
        str_contains($dados['email'],"@")
    ) {
        $result = $controller->registrar($request);

        if ($result['success']){
            return $twig->render($response, 'login.twig', [
                'title' => 'Login',
                'message' => $result['message'],
                'alertType' => 'success'
            ]);
        }

        return $twig->render($response, 'registrar.twig', [
            'title' => 'Registro',
            'message' => 'Email ou senha inválidos.'
        ]);
    } else {
        return $twig->render($response, 'registrar.twig', [
            'title' => 'Registro',
            'message' => 'Um dos campos não foi preenchido corretamente.'
        ]);
    }


});

$app->run();