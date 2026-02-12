<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");


// Se futuramente precisar de dados do banco (combos), já está preparado
require_once __DIR__ . '/../config/conexao.php';
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Paciente</title>
    <link rel="stylesheet" href="../assets/css/style-novo-paciente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body id="novo-paciente-body">

<div id="novo-paciente-container">
    <h2 id="novo-paciente-title"><i class="fa-solid fa-user-plus"></i> Novo Paciente</h2>

    <form id="novo-paciente-form" action="salvar.php" method="POST">
        
        <div class="form-group">
            <label for="novo-paciente-nome">Nome <span style="color:red;">*</span></label>
            <input type="text" name="nome" id="novo-paciente-nome" required>
        </div>

        <div class="form-group">
            <label for="novo-paciente-cpf">CPF <span style="color:red;">*</span></label>
            <input type="text" name="cpf" id="novo-paciente-cpf" required>
        </div>

        <div class="form-group">
            <label for="novo-paciente-telefone">Telefone</label>
            <input type="text" name="telefone" id="novo-paciente-telefone">
        </div>

        <div class="form-group">
            <label for="novo-paciente-email">E-mail</label>
            <input type="email" name="email" id="novo-paciente-email">
        </div>

        <div class="form-group">
            <label for="novo-paciente-data">Data de Nascimento</label>
            <input type="date" name="data_nascimento" id="novo-paciente-data">
        </div>

        <div class="form-group">
            <label for="novo-paciente-endereco">Endereço</label>
            <input type="text" name="endereco" id="novo-paciente-endereco">
        </div>

        <!-- Observações ocupa duas colunas -->
        <div class="form-group observacoes">
            <label for="novo-paciente-observacoes">Observações</label>
            <textarea name="observacoes" id="novo-paciente-observacoes" rows="4"></textarea>
        </div>

        <div id="novo-paciente-actions">
            <button type="submit" class="novo-paciente-btn salvar-btn">
                <i class="fa-solid fa-floppy-disk"></i> Salvar
            </button>
            <a href="listar.php" class="novo-paciente-btn cancelar-btn">
                <i class="fa-solid fa-xmark"></i> Cancelar
            </a>
        </div>
    </form>
</div>

</body>
</html>
