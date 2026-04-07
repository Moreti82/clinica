<?php
$page_title = 'Novo Paciente';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Novo Paciente</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / <a href="listar.php">Pacientes</a> / Novo</div>
</div>

<div class="card" style="max-width: 800px;">
    <form action="salvar.php" method="POST">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            
            <div class="form-group">
                <label>Nome *</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>CPF *</label>
                <input type="text" name="cpf" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Telefone</label>
                <input type="text" name="telefone" class="form-control">
            </div>
            
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Data de Nascimento</label>
                <input type="date" name="data_nascimento" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Endereço</label>
                <input type="text" name="endereco" class="form-control">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Observações</label>
                <textarea name="observacoes" class="form-control" rows="4"></textarea>
            </div>
        </div>
        
        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Salvar</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>