<?php
// banco.php: o MODELO.
// Todo o SQL da Livraria Entre Páginas fica aqui.

function conectar(): PDO
{
    $pdo = new PDO('sqlite:' . __DIR__ . '/mini-loja.sqlite');

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Tabela usada para os livros
    $pdo->exec('CREATE TABLE IF NOT EXISTS produtos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        preco REAL NOT NULL
    )');

    // Coluna da capa dos livros
    $colunas = $pdo->query('PRAGMA table_info(produtos)')
        ->fetchAll(PDO::FETCH_COLUMN, 1);

    if (!in_array('imagem', $colunas)) {
        $pdo->exec(
            'ALTER TABLE produtos ADD COLUMN imagem TEXT'
        );
    }

    // Tabela dos funcionários.
    // A senha guardada aqui será o hash, não a senha pura.
    $pdo->exec('CREATE TABLE IF NOT EXISTS funcionarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        senha TEXT NOT NULL
    )');

    return $pdo;
}


// ==================================================
// LIVROS
// ==================================================


// R - Read
function listarProdutos(): array
{
    $pdo = conectar();

    return $pdo->query(
        'SELECT id, nome, preco, imagem
         FROM produtos
         ORDER BY nome'
    )->fetchAll(PDO::FETCH_ASSOC);
}


// C - Create
function inserirProduto(
    string $nome,
    float $preco,
    string $imagem
): void
{
    $pdo = conectar();

    $stmt = $pdo->prepare(
        'INSERT INTO produtos (nome, preco, imagem)
         VALUES (:nome, :preco, :imagem)'
    );

    $stmt->execute([
        ':nome' => $nome,
        ':preco' => $preco,
        ':imagem' => $imagem
    ]);
}


// D - Delete
function excluirProduto(int $id): void
{
    $pdo = conectar();

    $stmt = $pdo->prepare(
        'DELETE FROM produtos
         WHERE id = :id'
    );

    $stmt->execute([
        ':id' => $id
    ]);
}


// Busca um livro pelo id
function buscarProduto(int $id): ?array
{
    $pdo = conectar();

    $stmt = $pdo->prepare(
        'SELECT id, nome, preco, imagem
         FROM produtos
         WHERE id = :id'
    );

    $stmt->execute([
        ':id' => $id
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}


// U - Update
function atualizarProduto(
    int $id,
    string $nome,
    float $preco,
    string $imagem
): void
{
    $pdo = conectar();

    $stmt = $pdo->prepare(
        'UPDATE produtos
         SET nome = :nome,
             preco = :preco,
             imagem = :imagem
         WHERE id = :id'
    );

    $stmt->execute([
        ':nome' => $nome,
        ':preco' => $preco,
        ':imagem' => $imagem,
        ':id' => $id
    ]);
}


// ==================================================
// FUNCIONÁRIOS
// ==================================================


// Grava um funcionário.
// A senha já chega transformada em hash.
function inserirFuncionario(
    string $nome,
    string $email,
    string $hash
): void
{
    $pdo = conectar();

    $stmt = $pdo->prepare(
        'INSERT INTO funcionarios (nome, email, senha)
         VALUES (:nome, :email, :senha)'
    );

    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $hash
    ]);
}


// Procura um funcionário pelo e-mail.
// Se não encontrar, devolve null.
function buscarFuncionario(string $email): ?array
{
    $pdo = conectar();

    $stmt = $pdo->prepare(
        'SELECT id, nome, email, senha
         FROM funcionarios
         WHERE email = :email'
    );

    $stmt->execute([
        ':email' => $email
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}