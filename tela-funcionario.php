<?php
// tela-funcionario.php: VISÃO da área do funcionário.
?>
<!DOCTYPE html>

<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Entre Páginas - Funcionários</title>
</head>

<body>

    <p>
        <a href="index.html">
            ← Página inicial
        </a>
    </p>


    <h1>📚 Área do funcionário</h1>

    <p>
        Administração da Livraria Entre Páginas.
    </p>


    <?php if ($erro): ?>

        <p style="color: firebrick;">
            <strong>Não deu:</strong>
            <?= htmlspecialchars($erro) ?>
        </p>

    <?php endif; ?>


    <?php if ($aviso): ?>

        <p style="color: green;">
            <?= htmlspecialchars($aviso) ?>
        </p>

    <?php endif; ?>


    <?php if ($logado): ?>

        <h2>Área administrativa</h2>

        <p>
            Olá,
            <strong>
                <?= htmlspecialchars($logado) ?>
            </strong>!
        </p>

        <p>
            <a href="cadastro.php">
                📖 Gerenciar livros
            </a>
        </p>

        <p>
            <a href="funcionario.php?sair=1">
                Sair
            </a>
        </p>


    <?php else: ?>


        <h2>Entrar</h2>

        <form
            method="post"
            action="funcionario.php"
        >

            <input
                type="hidden"
                name="acao"
                value="entrar"
            >


            <p>
                <label for="email-entrar">
                    E-mail
                </label><br>

                <input
                    id="email-entrar"
                    name="email"
                    type="email"
                    required
                >
            </p>


            <p>
                <label for="senha-entrar">
                    Senha
                </label><br>

                <input
                    id="senha-entrar"
                    name="senha"
                    type="password"
                    required
                >
            </p>


            <button type="submit">
                Entrar
            </button>

        </form>


    <?php endif; ?>


    <hr>


    <h2>Cadastrar funcionário</h2>

    <form
        method="post"
        action="funcionario.php"
    >

        <input
            type="hidden"
            name="acao"
            value="cadastrar"
        >


        <p>
            <label for="nome">
                Nome
            </label><br>

            <input
                id="nome"
                name="nome"
                required
            >
        </p>


        <p>
            <label for="email">
                E-mail
            </label><br>

            <input
                id="email"
                name="email"
                type="email"
                required
            >
        </p>


        <p>
            <label for="senha">
                Senha (8 caracteres ou mais)
            </label><br>

            <input
                id="senha"
                name="senha"
                type="password"
                minlength="8"
                required
            >
        </p>


        <button type="submit">
            Cadastrar funcionário
        </button>

    </form>


</body>

</html>