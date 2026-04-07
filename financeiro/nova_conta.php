<?php
$page_title = 'Nova Conta';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$pacientes = $db->query("SELECT id, nome FROM pacientes WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$formasPagamento = $db->query("SELECT id, descricao FROM formas_pagamento WHERE ativo = 1 ORDER BY descricao")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'paciente_id' => $_POST['paciente_id'],
        'descricao' => $_POST['descricao'],
        'valor_total' => parseDinheiro($_POST['valor_total']),
        'data_vencimento' => $_POST['data_vencimento'],
        'observacoes' => $_POST['observacoes'] ?? ''
    ];
    
    try {
        $sql = "INSERT INTO contas_receber (paciente_id, descricao, valor_total, data_vencimento, observacoes) 
                VALUES (:paciente_id, :descricao, :valor_total, :data_vencimento, :observacoes)";
        $stmt = $db->prepare($sql);
        $stmt->execute($dados);
        
        redirecionar('contas_receber.php', 'Conta a receber cadastrada com sucesso!');
    } catch (Exception $e) {
        $erro = 'Erro ao salvar: ' . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Nova Conta a Receber</h1>
    <div class="breadcrumb">
        <a href="../dashboard/index.php">Início</a> / 
        <a href="contas_receber.php">Contas a Receber</a> / Nova
    </div>
</div>

<?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?php echo $erro; ?></div>
<?php endif; ?>

<div class="card">
    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Paciente *</label>
                <select name="paciente_id" class="form-control" required>
                    <option value="">Selecione</option>
                    <?php foreach ($pacientes as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Descrição *</label>
                <input type="text" name="descricao" class="form-control" required placeholder="Ex: Consulta, Tratamento, etc.">
            </div>
            
            <div class="form-group">
                <label>Valor Total *</label>
                <input type="text" name="valor_total" class="form-control" required placeholder="R$ 0,00" id="valor">
            </div>
            
            <div class="form-group">
                <label>Data de Vencimento *</label>
                <input type="date" name="data_vencimento" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Observações</label>
                <textarea name="observacoes" class="form-control" rows="3"></textarea>
            </div>
        </div>
        
        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
            <a href="contas_receber.php" class="btn btn-secondary">Cancelar</a>
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
