<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class Cliente
{
    private $conn;
    private $table = 'tb_clientes';

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Buscar por email
    public function buscarPorEmail($email)
    {
        $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        try {
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($cliente) {
                return ['success' => true, 'data' => $cliente];
            } else {
                return ['success' => false, 'error' => 'NOT_FOUND', 'message' => 'Cliente não encontrado com este e-mail.'];
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
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($cliente) {
                return ['success' => true, 'data' => $cliente];
            } else {
                return ['success' => false, 'error' => 'NOT_FOUND', 'message' => 'Cliente não encontrado com este ID.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao buscar cliente: {$e->getMessage()}"];
        }
    }

    // Buscar Clientes por usuario ID
    public function buscarPorUsuarioId($usuario_id)
    {
        $query = "SELECT * FROM {$this->table} WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        try {
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($clientes) {
                return ['success' => true, 'data' => $clientes];
            } else {
                return ['success' => false, 'error' => 'NOT_FOUND', 'message' => 'Clientes não encontrado vinculados a este usuário.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao buscar clientes: {$e->getMessage()}"];
        }
    }

    // Buscar Clientes por usuario ID e nome parecido
    public function buscarPorUsuarioENome($usuario_id, $nome_incompleto)
    {
        $query = "SELECT * FROM {$this->table} 
          WHERE usuario_id = :usuario_id 
            AND nome_completo LIKE :nome_incompleto 
          ORDER BY nome_completo ASC 
          LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        $nome_like = '%' . $nome_incompleto . '%';
        $stmt->bindParam(':nome_incompleto', $nome_like);

        try {
            $stmt->execute();
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($clientes) {
                return ['success' => true, 'data' => $clientes];
            } else {
                return ['success' => false, 'error' => 'NOT_FOUND', 'message' => 'Clientes não encontrados vinculados a este usuário. '];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao buscar clientes: {$e->getMessage()}"];
        }
    }

    // Criar novo usuário
    public function criar($usuario_id, $nome_completo, $email, $telefone)
    {
        $query = "INSERT INTO {$this->table} (usuario_id, nome_completo, email, telefone)
                  VALUES (:usuario_id, :nome_completo, :email, :telefone)";

        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':nome_completo', $nome_completo);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefone', $telefone);

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

    public function atualizar($id, $nome_completo, $email, $telefone)
    {
        $query = "UPDATE {$this->table}
                SET nome_completo = :nome_completo,
                    email = :email,
                    telefone = :telefone
                WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome_completo', $nome_completo);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefone', $telefone);

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => 'NO_CHANGES', 'message' => 'Nenhuma alteração foi feita.'];
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' || str_contains($e->getMessage(), '1062')) {
                return ['success' => false, 'error' => 'DUPLICATE_ENTRY', 'message' => 'O e-mail já está em uso por outro usuário.'];
            } else {
                return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao atualizar o cliente.\n{$e->getMessage()}"];
            }
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
                    return ['success' => false, 'error' => 'NOT_FOUND', 'message' => 'Cliente não encontrado ou já removido.'];
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' || str_contains($e->getMessage(), 'foreign key')) {
                // Erro de integridade referencial (por exemplo, outras tabelas dependem desse usuário)
                return ['success' => false, 'error' => 'FOREIGN_KEY_CONSTRAINT', 'message' => 'Este usuário não pode ser removido pois está relacionado a outros registros.'];
            } else {
                return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao remover o usuário.\n{$e->getMessage()}"];
            }
        }
    }
}
