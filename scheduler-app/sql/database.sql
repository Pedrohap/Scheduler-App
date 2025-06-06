CREATE TABLE tb_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_usuario VARCHAR(100) NOT NULL UNIQUE,
    nome_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE tb_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome_completo VARCHAR (100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(100) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE tb_agendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    usuario_id INT NOT NULL,
    data_inicial DATETIME NOT NULL,
    data_final DATETIME NOT NULL,
    titulo VARCHAR(250) NOT NULL,
    descricao VARCHAR (500) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES tb_clientes(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE VIEW vw_card_agendas AS
SELECT
    a.id,
    a.data_inicial,
    a.data_final,
    a.titulo,
    a.descricao,
    a.created_at,
    a.updated_at,
    a.usuario_id,
    a.cliente_id,
    c.nome_completo AS nome_cliente,
    c.email AS email_cliente,
    c.telefone AS telefone_cliente
FROM tb_agendas a
JOIN tb_clientes c ON a.cliente_id = c.id;