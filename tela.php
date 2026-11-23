<?php
// tela.php: VISÃO da Livraria Entre Páginas.
// $logado informa se existe um funcionário na sessão.
?>
<!DOCTYPE html>

<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Entre Páginas</title>
</head>

<body>

    <p>
        <a href="index.html">
            ← Página inicial
        </a>
    </p>


    <h1>📚 Livraria Entre Páginas</h1>

    <p>
        Um cantinho para encontrar sua próxima leitura.
    </p>


    <?php if ($logado): ?>

        <p>
            Olá,
            <strong>
                <?= htmlspecialchars($logado) ?>
            </strong>!

            <a href="funcionario.php">
                Área do funcionário
            </a>

            <a href="funcionario.php?sair=1">
                Sair
            </a>
        </p>

    <?php else: ?>

        <p>
            Você está visitando nossa livraria.
            <a href="funcionario.php">Entre como funcionário</a>
            para administrar os livros.
        </p>

    <?php endif; ?>


    <hr>


    <?php if ($logado): ?>

        <h2>
            <?= $produto ? 'Editar livro' : 'Cadastrar novo livro' ?>
        </h2>


        <?php if ($erro !== ''): ?>

            <p style="color: firebrick;">
                <strong>Capa recusada:</strong>
                <?= htmlspecialchars($erro) ?>
            </p>

        <?php endif; ?>


        <form
            method="post"
            action="cadastro.php"
            enctype="multipart/form-data"
        >

            <input
                type="hidden"
                name="id"
                value="<?= $produto['id'] ?? '' ?>"
            >


            <p>
                <label for="nome">
                    Título do livro
                </label><br>

                <input
                    id="nome"
                    name="nome"
                    required
                    value="<?= htmlspecialchars($produto['nome'] ?? '') ?>"
                >
            </p>


            <p>
                <label for="preco">
                    Preço (R$)
                </label><br>

                <input
                    id="preco"
                    name="preco"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    value="<?= $produto['preco'] ?? '' ?>"
                >
            </p>


            <?php if (
                $produto
                && !empty($produto['imagem'])
            ): ?>

                <p>
                    <strong>Capa atual:</strong><br>

                    <img
                        src="uploads/<?= htmlspecialchars($produto['imagem']) ?>"
                        alt=""
                        width="100"
                    >
                </p>

            <?php endif; ?>


            <p>
                <label for="imagem">
                    <?= $produto
                        ? 'Escolher outra capa'
                        : 'Capa do livro'
                    ?>
                </label><br>

                <input
                    id="imagem"
                    name="imagem"
                    type="file"
                    accept="image/jpeg, image/png, image/webp"
                >
            </p>


            <p>
                <small>
                    Formatos aceitos: JPG, PNG ou WEBP.
                    Tamanho máximo: 2 MB.
                </small>
            </p>


            <button type="submit">
                <?= $produto
                    ? 'Salvar alterações'
                    : 'Cadastrar livro'
                ?>
            </button>


            <?php if ($produto): ?>

                <a href="cadastro.php">
                    Cancelar
                </a>

            <?php endif; ?>

        </form>


        <hr>

    <?php endif; ?>


    <h2>
        📖 Livros disponíveis
        (<?= count($produtos) ?>)
    </h2>


    <?php if (count($produtos) === 0): ?>

        <p>
            Nenhum livro cadastrado ainda.
        </p>

    <?php else: ?>

        <ul>

            <?php foreach ($produtos as $item): ?>

                <li>

                    <?php if ($item['imagem']): ?>

                        <img
                            src="uploads/<?= htmlspecialchars($item['imagem']) ?>"
                            alt=""
                            width="80"
                        >

                    <?php else: ?>

                        <span>📕 sem capa</span>

                    <?php endif; ?>


                    <strong>
                        <?= htmlspecialchars($item['nome']) ?>
                    </strong>

                    -
                    R$ <?= number_format(
                        $item['preco'],
                        2,
                        ',',
                        '.'
                    ) ?>


                    <?php if ($logado): ?>

                        <a href="cadastro.php?editar=<?= $item['id'] ?>">
                            Editar
                        </a>


                        <form
                            method="post"
                            action="cadastro.php"
                            style="display: inline"
                        >
                            <button
                                name="excluir"
                                value="<?= $item['id'] ?>"
                            >
                                Excluir
                            </button>
                        </form>

                    <?php endif; ?>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>


</body>

</html>