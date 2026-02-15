<?php
require_once '../config/conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Profissional</title>
    <link rel="stylesheet" href="../assets/css/style-novo-profissional.css">
</head>
<body>

<div class="container">

    <h2>Cadastrar Profissional</h2>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="mensagem-alerta" style="color:green;">✅ Profissional cadastrado com sucesso!</div>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
        <div class="mensagem-alerta" style="color:red;">❌ Erro ao cadastrar profissional.</div>
    <?php endif; ?>

    <form action="salvar.php" method="POST">

        <div>
            <label>Nome:</label>
            <input type="text" name="nome" required>
        </div>

        <div>
            <label>CRO:</label>
            <input type="text" name="cro" required>
        </div>

        <div>
            <label>Especialidade:</label>
            <input type="text" name="especialidade">
        </div>

        <div>
            <label>Telefone:</label>
            <input type="text" name="telefone">
        </div>

        <div>
            <label>Ativo:</label>
            <select name="ativo">
                <option value="1">Sim</option>
                <option value="0">Não</option>
            </select>
        </div>

        <br>

        <button type="submit">Salvar</button>
        <a class="btn-voltar" href="profissionais.php">Voltar</a>

    </form>

</div>
        <script src="../assets/js/novo-profissional.js"></script>
</body>
</html>
