<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class Usuario
{
    private $conn;
    private $table = 'tb_usuarios';

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Buscar por email
    public function buscarPorNomeUsuario($nome_usuario)
    {
        $query = "SELECT * FROM {$this->table} WHERE nome_usuario = :nome_usuario LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nome_usuario', $nome_usuario);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario) {
                return ['success' => true, 'data' => $usuario];
            } else {
                return ['success' => false, 'error' => 'NOT_FOUND', 'message' => 'Usuário não encontrado com este nome de usuário.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao buscar usuário: {$e->getMessage()}"];
        }
    }

    // Buscar por nome de usuario
    public function buscarPorEmail($email)
    {
        $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        try {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario) {
                return ['success' => true, 'data' => $usuario];
            } else {
                return ['success' => false, 'error' => 'NOT_FOUND', 'message' => 'Usuário não encontrado com este e-mail.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao buscar usuário: {$e->getMessage()}"];
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
                return ['success' => false, 'error' => 'NOT_FOUND', 'message' => 'Usuário não encontrado com este ID.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'DATABASE_ERROR', 'message' => "Erro ao buscar usuário: {$e->getMessage()}"];
        }
    }

    // Criar novo usuário
    public function criar($nome_usuario, $nome_completo, $email, $senha)
    {
        $query = "INSERT INTO {$this->table} (nome_usuario, nome_completo, email, senha)
                  VALUES (:nome_usuario, :nome_completo, :email, :senha)";

        try {
            $stmt = $this->conn->prepare($query);

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt->bindParam(':nome_usuario', $nome_usuario);
            $stmt->bindParam(':nome_completo', $nome_completo);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senhaHash);

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
}
