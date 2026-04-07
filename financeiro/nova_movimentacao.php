<?php
$page_title = 'Nova Movimentação';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$formasPagamento = $db->query("SELECT id, descricao FROM formas_pagamento WHERE ativo = 1 ORDER BY descricao")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'tipo' => $_POST['tipo'],
        'descricao' => $_POST['descricao'],
        'valor' => parseDinheiro($_POST['valor']),
        'data_movimento' => $_POST['data_movimento'],
        'categoria' => $_POST['categoria'],
        'forma_pagamento_id' => $_POST['forma_pagamento_id'] ?: null,
        'observacoes' => $_POST['observacoes'] ?? '',
        'usuario_id' => $_SESSION['user_id'] ?? null
    ];
    
    try {
        $sql = "INSERT INTO caixa (tipo, descricao, valor, data_movimento, categoria, forma_pagamento_id, observacoes, usuario_id)
                VALUES (:tipo, :descricao, :valor, :data_movimento, :categoria, :forma_pagamento_id, :observacoes, :usuario_id)";
        $stmt = $db->prepare($sql);
        $stmt->execute($dados);
        
        redirecionar('caixa.php?data=' . $dados['data_movimento'], 'Movimentação registrada com sucesso!');
    } catch (Exception $e) {
        $erro = 'Erro ao salvar: ' . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Nova Movimentação de Caixa</h1>
    <div class="breadcrumb">
        <a href="../dashboard/index.php">Início</a> / 
        <a href="caixa.php">Caixa</a> / Nova
    </div>
</div>

<?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?php echo $erro; ?></div>
<?php endif; ?>

<div class="card" style="max-width: 600px;">
    <form method="POST">
        <div class="form-group">
            <label>Tipo *</label>
            <select name="tipo" class="form-control" required>
                <option value="entrada">Entrada (+)</option>
                <option value="saida">Saída (-)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Descrição *</label>
            <input type="text" name="descricao" class="form-control" required placeholder="Ex: Pagamento de material, Suprimento de caixa, etc.">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Valor *</label>
                <input type="text" name="valor" class="form-control" required placeholder="R$ 0,00" id="valor">
            </div>
            
            <div class="form-group">
                <label>Data *</label>
                <input type="date" name="data_movimento" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Categoria</label>
                <select name="categoria" class="form-control">
                    <option value="">Selecione</option>
                    <option value="Material">Material</option>
                    <option value="Equipamento">Equipamento</option>
                    <option value="Serviço">Serviço</option>
                    <option value="Salário">Salário</option>
                    <option value="Taxas">Taxas</option>
                    <option value="Outros">Outros</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Forma de Pagamento</label>
                <select name="forma_pagamento_id" class="form-control">
                    <option value="">Selecione</option>
                    <?php foreach ($formasPagamento as $fp): ?>
                        <option value="<?php echo $fp['id']; ?>"><?php echo htmlspecialchars($fp['descricao']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Observações</label>
            <textarea name="observacoes" class="form-control" rows="2"></textarea>
        </div>
        
        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
            <a href="caixa.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Salvar</button>
        </div>
    </form>
</div>

<script>
document.getElementById('valor').addEventListener('blur', function(e) {
    let valor = e.target.value.replace(/[^\d]/g, '');
    if (valor) {
        valor = (parseInt(valor) / 100).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
        e.target.value = valor;
    }
});
</script>

<?php include '../includes/footer.php'; ?>
