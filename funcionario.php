<?php
// funcionario.php: CONTROLE da área do funcionário.

// A sessão precisa começar antes de qualquer HTML
session_start();

require __DIR__ . '/banco.php';


// Clicou em "Sair"?
if (isset($_GET['sair'])) {
    $_SESSION = [];
    session_destroy();

    header('Location: funcionario.php');
    exit;
}


$erro = '';
$aviso = '';


// Algum formulário foi enviado?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';
    $nome = trim($_POST['nome'] ?? '');

    $email = strtolower(
        trim($_POST['email'] ?? '')
    );

    $senha = $_POST['senha'] ?? '';


    // ==========================================
    // ENTRAR
    // ==========================================

    if ($acao === 'entrar') {

        $funcionario = buscarFuncionario($email);

        if (
            $funcionario
            && password_verify(
                $senha,
                $funcionario['senha']
            )
        ) {
            session_regenerate_id(true);

            // O crachá do funcionário
            $_SESSION['funcionario_id']
                = $funcionario['id'];

            $_SESSION['funcionario']
                = $funcionario['nome'];

            header('Location: cadastro.php');
            exit;
        }

        // Não diz qual dos dois estava errado
        $erro = 'E-mail ou senha errados.';
    }


    // ==========================================
    // CADASTRAR FUNCIONÁRIO
    // ==========================================

    if ($acao === 'cadastrar') {

        if ($nome === '') {

            $erro = 'Escreva o seu nome.';

        } elseif (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $erro = 'Esse e-mail não parece um e-mail.';

        } elseif (strlen($senha) < 8) {

            $erro =
                'A senha precisa de 8 caracteres ou mais.';

        } elseif (buscarFuncionario($email)) {

            $erro =
                'Esse e-mail já tem cadastro.';

        } else {

            inserirFuncionario(
                $nome,
                $email,
                password_hash(
                    $senha,
                    PASSWORD_DEFAULT
                )
            );

            $aviso =
                'Cadastro feito, '
                . $nome
                . '! Agora é só entrar.';
        }
    }
}


// Quem está logado?
// Vazio significa que ninguém entrou ainda.
$logado = $_SESSION['funcionario'] ?? '';


// Mostra a página
require __DIR__ . '/tela-funcionario.php';