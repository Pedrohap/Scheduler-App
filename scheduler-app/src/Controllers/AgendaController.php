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

    //Função auxiliar para ajuste das chaves de filtro na busca com filtros
    private function mapearFiltros(array $filtros): array
    {
        return [
            'cliente_id'   => $filtros['cliente_id_filtro'] ?? null,
            'data_inicial' => $filtros['data_inicio_filtro'] ?? null,
            'data_final'   => $filtros['data_final_filtro'] ?? null,
            'titulo'       => $filtros['titulo_filtro'] ?? null
        ];
    }

    public function adicionar(Request $request): array
    {
        $dados = $request->getParsedBody();

        if (!isset($_SESSION['usuario_id'])){
            return ['success' => false, 'status' => 409, 'message' => 'Usuário não está ativo nesta sessão.'];
        }

        // Converte as strings em objetos DateTime
        $dataInicial = new \DateTime($dados['data_inicial']);
        $dataFinal = new \DateTime($dados['data_final']);

        if ($dataFinal < $dataInicial){
            return ['success' => false, 'status' => 501, 'message' => 'Data Final não pode anteceder a Data Inicial'];
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

        // Converte as strings em objetos DateTime
        $dataInicial = new \DateTime($dados['data_inicial']);
        $dataFinal = new \DateTime($dados['data_final']);

        if ($dataFinal < $dataInicial){
            return ['success' => false, 'status' => 501, 'message' => 'Data Final não pode anteceder a Data Inicial'];
        }

        $sucesso = $this->agendaModel->atualizar(
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

    public function getAgendas(array $filtros = []): array
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
        $filtrosAjustados = $this->mapearFiltros($filtros);
        $filtrosAjustados['usuario_id'] = $_SESSION['usuario_id'];;
        
        $agendas = $this->agendaModel->getAgendas(
            $filtrosAjustados
        );

        if ($agendas['success']) {
            return $agendas;
        }
        
        return ['success' => false, 'status' => 409, 'message' => $agendas['message']];
    }
}