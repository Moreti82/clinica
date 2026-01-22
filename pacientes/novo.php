<?php
// Se futuramente precisar de dados do banco (combos), já está preparado
require_once __DIR__ . '/../config/conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Paciente</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Novo Paciente</h2>

    <form action="salvar.php" method="POST">
        <label for="nome">Nome *</label>
        <input type="text" name="nome" id="nome" required>

        <label for="cpf">CPF</label>
        <input type="text" name="cpf" id="cpf">

        <label for="telefone">Telefone</label>
        <input type="text" name="telefone" id="telefone">

        <label for="email">E-mail</label>
        <input type="email" name="email" id="email">

        <label for="data_nascimento">Data de Nascimento</label>
        <input type="date" name="data_nascimento" id="data_nascimento">

        <button type="submit">Salvar</button>
        <a href="listar.php" class="btn">Cancelar</a>
    </form>
</div>

</body>
</html>
