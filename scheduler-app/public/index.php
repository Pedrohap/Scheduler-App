<?php

session_start();



require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UsuarioController;
use App\Controllers\ClienteController;
use App\Controllers\AgendaController;

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

// Rotas agenda
$app->get('/agenda', function ($request, $response, $args) use ($twig) {
    return $twig->render($response, 'agendas.twig', [
            'title' => 'Minhas Agendas',
    ]);
})->add(new AuthMiddleware());

$app->get('/getAgendas', function ($request, $response, $args) use ($twig) {
    $controller = new AgendaController();
    $dados = $request->getParsedBody();

    $result = $controller->getAgendas();

    $payload = json_encode($result);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');

})->add(new AuthMiddleware());

$app->post('/addAgenda', function ($request, $response, $args) use ($twig) {
    $controller = new AgendaController();
    
    $dados = $request->getParsedBody();
    if (
        isset($dados['cliente_id'], $dados['data_inicial'], $dados['data_final'], $dados['titulo'], $dados['descricao']) &&
        !empty($dados['cliente_id']) &&
        !empty($dados['data_inicial']) &&
        !empty($dados['data_final']) &&
        !empty($dados['titulo']) &&
        !empty($dados['descricao'])

    ) {
        $result = $controller->adicionar($request);

        $data = [
            'message' => $result['message'],
            'alertType' => !empty($result['success']) ? 'success' : 'danger',
        ];
    } else {
        $data = [
            'message' => 'Um dos campos não foi preenchido corretamente.',
            'alertType' => 'warning'
        ];
    }

    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new AuthMiddleware());

$app->post('/deleteAgenda/{id}', function ($request, $response, $args) use ($twig) {
    $id = (int)$args['id'];
    $controller = new AgendaController();

    $result = $controller->remover($id);
    
    $data = [
            'message' => $result['message'],
            'alertType' => !empty($result['success']) ? 'success' : 'danger',
        ];

    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');

})->add(new AuthMiddleware());

$app->post('/updateAgenda/{id}', function ($request, $response, $args) use ($twig) {
    $controller = new AgendaController();
    $id = (int)$args['id'];
    $dados = $request->getParsedBody();

    if (
        isset($dados['cliente_id'], $dados['data_inicial'], $dados['data_final'], $dados['titulo'], $dados['descricao']) &&
        !empty($dados['cliente_id']) &&
        !empty($dados['data_inicial']) &&
        !empty($dados['data_final']) &&
        !empty($dados['titulo']) &&
        !empty($dados['descricao'])
    ) {
        $result = $controller->atualizar($request,$id);


        $data = [
            'message' => $result['message'],
            'alertType' => !empty($result['success']) ? 'success' : 'danger',
        ];
        
    } else {

        $data = [
            'message' => 'Um dos campos não foi preenchido corretamente.',
            'alertType' => 'warning'
        ];
    }
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
})->add(new AuthMiddleware());


// Rotas Cliente
$app->get('/getClientes', function ($request, $response, $args) use ($twig) {
    $controller = new ClienteController();

    $result = $controller->getClientesPorUsuario();

    return $result;
})->add(new AuthMiddleware());

$app->post('/addCliente', function ($request, $response, $args) use ($twig) {
    $controller = new ClienteController();
    
    $dados = $request->getParsedBody();
    if (
        isset($dados['nome_completo'], $dados['email'], $dados['telefone']) &&
        !empty($dados['nome_completo']) &&
        !empty($dados['email']) &&
        !empty($dados['telefone']) &&
        str_contains($dados['email'],"@")

    ) {
        $result = $controller->adicionar($request);

        $data = [
            'message' => $result['message'],
            'alertType' => !empty($result['success']) ? 'success' : 'danger',
        ];
    } else {
        $data = [
            'message' => 'Um dos campos não foi preenchido corretamente.',
            'alertType' => 'warning'
        ];
    }

    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new AuthMiddleware());

$app->post('/deleteCliente/{id}', function ($request, $response, $args) use ($twig) {
    $id = (int)$args['id'];
    $controller = new ClienteController();

    $result = $controller->remover($id);

    return $result;
})->add(new AuthMiddleware());

$app->post('/updateCliente/{id}', function ($request, $response, $args) use ($twig) {
    $controller = new ClienteController();
    $id = (int)$args['id'];
    $dados = $request->getParsedBody();

    if (
        isset($dados['nome_usuario'], $dados['nome_completo'], $dados['email'], $dados['senha']) &&
        !empty($dados['nome_usuario']) &&
        !empty($dados['nome_completo']) &&
        !empty($dados['email']) &&
        !empty($dados['senha']) &&
        str_contains($dados['email'],"@")
    ) {
        $result = $controller->atualizar($request,$id);

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
})->add(new AuthMiddleware());
$app->run();