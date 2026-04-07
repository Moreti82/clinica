<?php
$page_title = 'Novo Produto';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'codigo' => $_POST['codigo'] ?? null,
        'descricao' => $_POST['descricao'],
        'categoria' => $_POST['categoria'] ?? null,
        'unidade' => $_POST['unidade'],
        'quantidade_minima' => $_POST['quantidade_minima'] ?: 0,
        'quantidade_atual' => $_POST['quantidade_atual'] ?: 0,
        'fornecedor' => $_POST['fornecedor'] ?? null
    ];
    
    try {
        $sql = "INSERT INTO produtos (codigo, descricao, categoria, unidade, quantidade_minima, quantidade_atual, fornecedor)
                VALUES (:codigo, :descricao, :categoria, :unidade, :quantidade_minima, :quantidade_atual, :fornecedor)";
        $stmt = $db->prepare($sql);
        $stmt->execute($dados);
        
        redirecionar('index.php', 'Produto cadastrado com sucesso!');
    } catch (Exception $e) {
        $erro = 'Erro ao salvar: ' . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Novo Produto</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / <a href="index.php">Estoque</a> / Novo</div>
</div>

<?php if (isset($erro)): ?><div class="alert alert-danger"><?php echo $erro; ?></div><?php endif; ?>

<div class="card" style="max-width: 600px;">
    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Código</label>
                <input type="text" name="codigo" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Descrição *</label>
                <input type="text" name="descricao" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Categoria</label>
                <input type="text" name="categoria" class="form-control" placeholder="Ex: Material, Medicamento">
            </div>
            
            <div class="form-group">
                <label>Unidade</label>
                <select name="unidade" class="form-control">
                    <option value="un">Unidade</option>
                    <option value="cx">Caixa</option>
                    <option value="kg">Kg</option>
                    <option value="g">Grama</option>
                    <option value="ml">Ml</option>
                    <option value="l">Litro</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Qtd Mínima</label>
                <input type="number" name="quantidade_minima" class="form-control" step="0.01" value="0">
            </div>
            
            <div class="form-group">
                <label>Qtd Inicial</label>
                <input type="number" name="quantidade_atual" class="form-control" step="0.01" value="0">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Fornecedor</label>
                <input type="text" name="fornecedor" class="form-control">
            </div>
        </div>
        
        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Salvar</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
