<?php
// cadastro.php: CONTROLE da Livraria Entre Páginas.
// Recebe os dados, conversa com o banco e chama a tela.

// A sessão precisa começar antes de qualquer HTML
session_start();

require __DIR__ . '/banco.php';

// Descobre quem está logado.
// Se estiver vazio, a pessoa é visitante.
$logado = $_SESSION['funcionario'] ?? '';


// A vitrine é pública, mas as alterações exigem login.
// POST inclui cadastrar e excluir.
// "editar" aparece na URL quando alguém tenta editar.
if (
    $logado === ''
    && (
        $_SERVER['REQUEST_METHOD'] === 'POST'
        || isset($_GET['editar'])
    )
) {
    header('Location: funcionario.php');
    exit;
}


// Confere a capa antes de guardar.
// Retorna uma mensagem de erro ou uma string vazia.
function conferirImagem(array $arquivo): string
{
    if (
        $arquivo['error'] === UPLOAD_ERR_INI_SIZE
        || $arquivo['size'] > 2 * 1024 * 1024
    ) {
        return 'A capa passou de 2 MB. Diminua e tente novamente.';
    }

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        return 'A capa não chegou inteira. Tente novamente.';
    }

    if ($arquivo['size'] === 0) {
        return 'A capa chegou vazia.';
    }

    $info = getimagesize($arquivo['tmp_name']);

    if (
        $info === false
        || !in_array(
            $info[2],
            [
                IMAGETYPE_JPEG,
                IMAGETYPE_PNG,
                IMAGETYPE_WEBP
            ]
        )
    ) {
        return 'A capa precisa ser uma imagem JPG, PNG ou WEBP.';
    }

    return '';
}


// Guarda a capa com um nome criado pelo sistema.
function guardarImagem(array $arquivo): string
{
    $extensoes = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp'
    ];

    $tipo = getimagesize($arquivo['tmp_name'])[2];

    $nome = bin2hex(random_bytes(8))
        . '.'
        . $extensoes[$tipo];

    if (!is_dir(__DIR__ . '/uploads')) {
        mkdir(__DIR__ . '/uploads');
    }

    move_uploaded_file(
        $arquivo['tmp_name'],
        __DIR__ . '/uploads/' . $nome
    );

    return $nome;
}


// Apaga uma capa da pasta uploads.
function apagarImagem(string $nome): void
{
    $caminho = __DIR__
        . '/uploads/'
        . basename($nome);

    if ($nome !== '' && is_file($caminho)) {
        unlink($caminho);
    }
}


// Excluir um livro.
// Esse trecho só é alcançado se houver login,
// porque a tranca anterior barra visitantes.
if (isset($_POST['excluir'])) {
    $id = (int) $_POST['excluir'];

    $produto = buscarProduto($id);

    if ($produto) {
        apagarImagem($produto['imagem'] ?? '');
    }

    excluirProduto($id);

    header('Location: cadastro.php');
    exit;
}


// A página começa sem erro e sem livro em edição.
$erro = '';
$produto = null;


// O formulário administrativo foi enviado?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $preco = (float) ($_POST['preco'] ?? 0);

    $imagem = '';

    $temFoto =
        isset($_FILES['imagem'])
        && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE;

    if ($temFoto) {
        $erro = conferirImagem($_FILES['imagem']);
    }

    if ($erro === '') {
        if ($temFoto) {
            $imagem = guardarImagem($_FILES['imagem']);
        }

        if ($id > 0) {
            // Edição:
            // sem capa nova, mantém a anterior;
            // com capa nova, apaga a anterior.
            $antigo = buscarProduto($id);
            $fotoAntiga = $antigo['imagem'] ?? '';

            if ($imagem === '') {
                $imagem = $fotoAntiga;
            } else {
                apagarImagem($fotoAntiga);
            }

            atualizarProduto(
                $id,
                $nome,
                $preco,
                $imagem
            );
        } else {
            inserirProduto(
                $nome,
                $preco,
                $imagem
            );
        }

        header('Location: cadastro.php');
        exit;
    }

    // Se a capa for recusada, mantém os dados digitados.
    $produto = [
        'id' => $id ?: '',
        'nome' => $nome,
        'preco' => $preco
    ];
}


// Busca o livro escolhido para edição.
if (isset($_GET['editar'])) {
    $produto = buscarProduto(
        (int) $_GET['editar']
    );
}


// A listagem continua pública.
$produtos = listarProdutos();


// Chama a tela.
// Ela também recebe $logado.
require __DIR__ . '/tela.php';