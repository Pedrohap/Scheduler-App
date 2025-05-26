<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class Agenda
{
    private $conn;
    private $table = 'tb_agendas';

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Retorna as agendas baseados em filtros dinamicamente aplicados
    public function getAgendas($filtros){
        // Base da query
        $sql = "SELECT * FROM tb_agendas WHERE 1=1";
        $params = [];

        // Filtros opcionais
        if (!empty($filtros['cliente_id'])) {
            $sql .= " AND cliente_id = :cliente_id";
            $params[':cliente_id'] = $filtros['cliente_id'];
        }

        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }

        if (!empty($filtros['data_inicial'])) {
            $sql .= " AND data_inicial >= :data_inicial";
            $params[':data_inicial'] = $filtros['data_inicial'];
        }

        if (!empty($filtros['data_final'])) {
            $sql .= " AND data_final <= :data_final";
            $params[':data_final'] = $filtros['data_final'];
        }

        if (!empty($filtros['titulo'])) {
            $sql .= " AND titulo LIKE :titulo";
            $params[':titulo'] = '%' . $filtros['titulo'] . '%';
        }

        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute($params);
            $agendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($usuarios) {
                return ['success' => true, 'data' => $agendas];
            } else {
                return ['success' => false, 'error' => 'NOT_FOUND', 'message' => 'Clientes não encontrado vinculados a este usuário.'];
            }

        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao buscar cliente: {$e->getMessage()}"];
        }
    }

    // Buscar por ID
    public function buscarPorId($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        try {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario) {
                return ['success' => true, 'data' => $usuario];
            } else {
                return ['success' => false, 'error' => 'NOT_FOUND', 'message' => 'Agenda não encontrado com este ID.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao buscar Agenda: {$e->getMessage()}"];
        }
    }

    // Criar nova agenda
    public function criar($cliente_id, $usuario_id, $data_inicial, $data_final, $titulo, $descricao)
    {
        $query = "INSERT INTO {$this->table} (cliente_id, usuario_id, data_inicial, data_final, titulo, descricao)
                  VALUES (:cliente_id, :usuario_id, :data_inicial, :data_final, :titulo, :descricao)";

        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':cliente_id', $cliente_id);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':data_inicial', $data_inicial);
            $stmt->bindParam(':data_final', $data_final);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descricao', $descricao);


            if ($stmt->execute()) {
                return ['success' => true];
            }
        } catch (PDOException $e) {
            // Verificação de violação de restrição de integridade.
            if ($e->getCode() == '23000' || str_contains($e->getMessage(), '1062')) {
                // A violação foi de uma chave única (duplicidade)
                // Retorna um array ou um objeto de erro para quem chamou a função
                return ['success' => false, 'error' => 'DUPLICATE_ENTRY', 'message' => 'O nome de usuário ou e-mail já está em uso.'];
            } else {
                // Foi algum outro erro de banco de dados
                // Você pode querer logar o erro real: error_log($e->getMessage());
                return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Ocorreu um erro ao criar o usuário.\n{$e->getMessage()}"];
            }
        }
    }

    public function atualizar($id, $cliente_id, $data_inicial, $data_final, $titulo, $descricao)
    {
        $query = "UPDATE {$this->table}
                SET cliente_id = :cliente_id,
                    data_inicial = :data_inicial,
                    data_final = :data_final,
                    titulo = :titulo,
                    descricao = :descricao
                WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':cliente_id', $cliente_id);
            $stmt->bindParam(':data_inicial', $data_inicial);
            $stmt->bindParam(':data_final', $data_final);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descricao', $descricao);

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => 'NO_CHANGES', 'message' => 'Nenhuma alteração foi feita.'];
                }
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao atualizar o cliente.\n{$e->getMessage()}"];
        }
    }

    public function remover($id)
    {
        $query = "DELETE FROM {$this->table} 
                WHERE id = :id";
        try{
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => 'NOT_FOUND', 'message' => 'aGENDA não encontrada ou já removido.'];
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' || str_contains($e->getMessage(), 'foreign key')) {
                // Erro de integridade referencial (por exemplo, outras tabelas dependem desse usuário)
                return ['success' => false, 'error' => 'FOREIGN_KEY_CONSTRAINT', 'message' => 'Esta agenda não pode ser removido pois está relacionado a outros registros.'];
            } else {
                return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao remover agenda.\n{$e->getMessage()}"];
            }
        }
    }
}
