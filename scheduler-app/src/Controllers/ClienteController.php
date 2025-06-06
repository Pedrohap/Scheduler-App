<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Cliente;

class ClienteController
{
    protected $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
    }

    // POST /registrar
    public function adicionar(Request $request): array
    {
        $dados = $request->getParsedBody();

        if (!isset($_SESSION['usuario_id'])){
            return ['success' => false, 'status' => 409, 'message' => 'Usuário não está ativo nesta sessão.'];
        }

        $sucesso = $this->clienteModel->criar(
            $_SESSION['usuario_id'],
            $dados['nome_completo'],
            $dados['email'],
            $dados['telefone']
        );

        if ($sucesso) {
            return ['success' => true, 'status' => 201, 'message' => 'Cliente criado com sucesso!'];
        }
        
        return ['success' => false, 'status' => 409, 'message' => 'Erro ao criar cliente'];
    }

    public function remover($id): array
    {
        $sucesso = $this->clienteModel->remover(
            $id,
        );

        if ($sucesso['success']) {
            return ['success' => true, 'status' => 201, 'message' => 'Cliente removido com sucesso!'];
        }
        
        return ['success' => false, 'status' => 409, 'message' => 'Erro ao remover cliente'];
    }

    public function atualizar(Request $request,$id): array
    {
        $dados = $request->getParsedBody();

        if (!isset($_SESSION['usuario_id'])){
            return ['success' => false, 'status' => 409, 'message' => 'Usuário não está ativo nesta sessão.'];
        }

        $sucesso = $this->clienteModel->criar(
            $id,
            $dados['nome_completo'],
            $dados['email'],
            $dados['telefone']
        );

        if ($sucesso['success']) {
            return ['success' => true, 'status' => 201, 'message' => 'Cliente criado com sucesso!'];
        }
        
        return ['success' => false, 'status' => 409, 'message' => $sucesso['message']];
    }

    public function getClientesPorUsuario(): array
    {
        $clientes = $this->clienteModel->buscarPorUsuarioId(
            $_SESSION['usuario_id']
        );

        if ($clientes['success']) {
            return $clientes;
        }
        
        return ['success' => false, 'status' => 409, 'message' => $clientes['message']];
    }

    public function getClientePorId($id): array
    {
        $cliente = $this->clienteModel->buscarPorId(
            $id
        );

        if ($cliente['success']) {
            return $cliente;
        }
        
        return ['success' => false, 'status' => 409, 'message' => $cliente['message']];
    }

    public function getClientePorNomeLike($nome_incompleto): array
    {
        $clientes = $this->clienteModel->buscarPorUsuarioENome(
            $_SESSION['usuario_id'],
            $nome_incompleto
        );

        if ($clientes['success']) {
            return $clientes;
        }
        
        return ['success' => false, 'status' => 409, 'message' => $clientes['message']];
    }
}