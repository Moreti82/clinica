<?php
$page_title = 'Novo Convênio';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome' => $_POST['nome'],
        'razao_social' => $_POST['razao_social'] ?? null,
        'cnpj' => $_POST['cnpj'] ?? null,
        'telefone' => $_POST['telefone'] ?? null,
        'email' => $_POST['email'] ?? null,
        'desconto_padrao' => $_POST['desconto_padrao'] ?: 0,
        'observacoes' => $_POST['observacoes'] ?? ''
    ];
    
    try {
        $sql = "INSERT INTO convenios (nome, razao_social, cnpj, telefone, email, desconto_padrao, observacoes)
                VALUES (:nome, :razao_social, :cnpj, :telefone, :email, :desconto_padrao, :observacoes)";
        $stmt = $db->prepare($sql);
        $stmt->execute($dados);
        
        redirecionar('index.php', 'Convênio cadastrado!');
    } catch (Exception $e) {
        $erro = 'Erro: ' . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Novo Convênio</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / <a href="index.php">Convênios</a> / Novo</div>
</div>

<?php if (isset($erro)): ?><div class="alert alert-danger"><?php echo $erro; ?></div><?php endif; ?>

<div class="card" style="max-width: 700px;">
    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Nome do Convênio *</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Razão Social</label>
                <input type="text" name="razao_social" class="form-control">
            </div>
            
            <div class="form-group">
                <label>CNPJ</label>
                <input type="text" name="cnpj" class="form-control">
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
                <label>Desconto Padrão (%)</label>
                <input type="number" name="desconto_padrao" class="form-control" step="0.01" min="0" max="100" value="0">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Observações</label>
                <textarea name="observacoes" class="form-control" rows="3"></textarea>
            </div>
        </div>
        
        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Salvar</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
