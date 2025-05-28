# 📅 Scheduler

Aplicação web para gerenciamento de agendas vinculadas a clientes, com autenticação de usuários.

## ✨ Funcionalidades

- Cadastro de usuários com autenticação
- Cadastro de clientes
- Criação, edição e exclusão de agendas
- Agendas vinculadas a clientes e protegidas por usuário (isolamento de dados)
- Filtro dinâmico de agendas por palavra-chave
- Interface simples e direta

## 🛠️ Tecnologias Utilizadas

- PHP 8 com Slim Framework
- Twig (template engine)
- MySQL
- HTML + CSS (C/ Bootstrap + assets publicos) + JavaScript
- Docker (ambiente de desenvolvimento incluso)
- Composer (gerenciador de dependências PHP)

## 🚀 Como rodar o projeto

### 🐳 Usando Docker (recomendado)

1. Suba os containers:

```bash
docker-compose up -d
```

2. Acesse o container `scheduler-app` e instale as dependências PHP:

```bash
docker exec -it scheduller-app-scheduler-1 bash
composer install
```
> Dica: o nome do container acima pode variar dependendo do seu `docker-compose.yml`.

3. Crie o banco de dados e tabelas executando o script `database.sql` no seu cliente MySQL.

> Dica: o banco pode estar acessível via localhost:3306 dependendo do seu `docker-compose.yml`.

---

### 💻 Rodando localmente sem Docker

1. Crie um ambiente local com PHP, MySQL e Composer.
2. Clone o projeto e rode:

```bash
composer install
```

3. Configure a conexão com o banco no arquivo:

```
src/Config/Database.php
```

4. Execute o script `database.sql` no seu MySQL para criar as tabelas necessárias.

5. Certifique-se de que seu servidor local (Apache, Nginx, etc) está apontando para a pasta `public/` como raiz do projeto.
> Dica: Para Apache, o projeto inclui um arquivo `.htaccess` com as regras de rewrite necessárias. Para outros servidores, ajuste a configuração conforme a [documentação oficial do Slim Framework](https://www.slimframework.com/docs/v4/start/web-servers.html).
---

## 🔐 Acesso

* O sistema não possui usuário padrão. O primeiro passo ao acessar a aplicação é cadastrar um novo usuário.

---

## 📁 Estrutura do Projeto

```
.
├── docker/              # Pasta com dockerfile para a criação da imagem do container
├── public/              # Pasta pública (index.php, assets)
├── src/
│   ├── Controllers/     # Lógica das rotas
│   ├── Models/          # Conexão com o banco e lógica de dados
│   ├── Middleware/      # Middlewares personalizados
│   └── Config/          # Configurações de banco
├── views/               # Templates Twig
├── database.sql         # Script para criar o banco de dados
├── docker-compose.yml   # Ambiente Docker
└── README.md
```

---

## 📌 Observações

* O filtro dinâmico foi implementado para agendas, mas não deu tempo para clientes.
* A sessão é gerenciada manualmente e as rotas são protegidas por middleware.
* O projeto não utiliza jQuery por escolha técnica e alinhamento com os objetivos do desafio.
