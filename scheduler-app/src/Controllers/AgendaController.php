<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Agenda;

class AgendaController
{
    protected $agendaModel;

    public function __construct()
    {
        $this->agendaModel = new Agenda();
    }

    // POST /registrar
    public function adicionar(Request $request): array
    {
        $dados = $request->getParsedBody();

        if (!isset($_SESSION['usuario_id'])){
            return ['success' => false, 'status' => 409, 'message' => 'Usuário não está ativo nesta sessão.'];
        }

        $sucesso = $this->agendaModel->criar(
            $dados['cliente_id'],
            $_SESSION['usuario_id'],
            $dados['data_inicial'],
            $dados['data_final'],
            $dados['titulo'],
            $dados['descricao']
            
        );

        if ($sucesso) {
            return ['success' => true, 'status' => 201, 'message' => 'Agenda criada com sucesso!'];
        }
        
        return ['success' => false, 'status' => 409, 'message' => 'Erro ao criar cliente'];
    }

    public function remover($id): array
    {
        $sucesso = $this->agendaModel->remover(
            $id,
        );

        if ($sucesso['success']) {
            return ['success' => true, 'status' => 201, 'message' => 'Agenda removida com sucesso!'];
        }
        
        return ['success' => false, 'status' => 409, 'message' => 'Erro ao remover agenda'];
    }

    public function atualizar(Request $request,$id): array
    {
        $dados = $request->getParsedBody();

        if (!isset($_SESSION['usuario_id'])){
            return ['success' => false, 'status' => 409, 'message' => 'Usuário não está ativo nesta sessão.'];
        }

        $sucesso = $this->agendaModel->criar(
            $id,
            $dados['cliente_id'],
            $dados['data_inicial'],
            $dados['data_final'],
            $dados['titulo'],
            $dados['descricao']
        );

        if ($sucesso['success']) {
            return ['success' => true, 'status' => 201, 'message' => 'Agenda atualizada com sucesso!'];
        }
        
        return ['success' => false, 'status' => 409, 'message' => $sucesso['message']];
    }

    public function getAgendas(): array
    {
        $filtros['usuario_id'] = $_SESSION['usuario_id'];
        $agendas = $this->agendaModel->getAgendas($filtros);

        if ($agendas['success']) {
            return $agendas;
        }
        
        return ['success' => false, 'status' => 409, 'message' => $agendas['message']];
    }

    public function getAgendasFiltradas($filtros): array
    {
        $agendas = $this->agendaModel->getAgendas(
            $filtros
        );

        if ($agendas['success']) {
            return $agendas;
        }
        
        return ['success' => false, 'status' => 409, 'message' => $agendas['message']];
    }
}