<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Usuario;

class UsuarioController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    // POST /login
    public function autenticar(Request $request): array
    {
        $dados = $request->getParsedBody();
        $loginData = $dados['loginData'];
        $senha = $dados['senha'];

        if (str_contains($loginData, "@")) {
            # Login por email
            $usuario = $this->usuarioModel->buscarPorEmail($loginData);
        } else {
            # Login por Username
            $usuario = $this->usuarioModel->buscarPorNomeUsuario($loginData);
        }

        if ($usuario['success'] && password_verify($senha, $usuario['data']['senha'])) {
            $_SESSION['usuario_id'] = $usuario['data']['id'];
            $_SESSION['usuario_nome'] = $usuario['data']['nome_completo'];

            return ['success' => true, 'status' => 302, 'message' => 'Login realizado com sucesso'];
        }

        // Falha no Login
        if ($usuario['error'] === 'DATABASE_ERROR'){
            return  ['success' => false, 'status' => 504,'message' => 'Erro no servidor de dados.'];
        }

        return  ['success' => false, 'status' => 401,'message' => 'Email ou senha inválidos.'];
    }

    // POST /registrar
    public function registrar(Request $request): array
    {
        $dados = $request->getParsedBody();

        $sucesso = $this->usuarioModel->criar(
            $dados['nome_usuario'],
            $dados['nome_completo'],
            $dados['email'],
            $dados['senha']
        );

        if ($sucesso) {
            return ['success' => true, 'status' => 201, 'message' => 'Usuário criado com sucesso!'];
        }
        
        return ['success' => false, 'status' => 409, 'message' => 'Erro ao criar usuário'];

    }

    // GET /logout
    public function logout(): array
    {
        session_destroy();
        return ['success' => true, 'status' => 200, 'message' => 'Logout Realizado com sucesso!'];
    }
}
