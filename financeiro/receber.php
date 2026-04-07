<?php
$page_title = 'Receber Pagamento';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    redirecionar('contas_receber.php', 'Conta não especificada', 'danger');
}

// Buscar conta
$stmt = $db->prepare("
    SELECT c.*, p.nome as paciente_nome 
    FROM contas_receber c 
    JOIN pacientes p ON c.paciente_id = p.id 
    WHERE c.id = ?
");
$stmt->execute([$id]);
$conta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$conta) {
    redirecionar('contas_receber.php', 'Conta não encontrada', 'danger');
}

if ($conta['status'] === 'Recebido') {
    redirecionar('contas_receber.php', 'Esta conta já foi recebida', 'warning');
}

$formasPagamento = $db->query("SELECT id, descricao FROM formas_pagamento WHERE ativo = 1 ORDER BY descricao")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $forma_pagamento_id = $_POST['forma_pagamento_id'];
    $data_recebimento = $_POST['data_recebimento'] ?? date('Y-m-d');
    $observacoes = $_POST['observacoes'] ?? '';
    
    try {
        $db->beginTransaction();
        
        // Atualizar conta
        $stmt = $db->prepare("
            UPDATE contas_receber 
            SET status = 'Recebido', 
                valor_recebido = valor_total,
                data_recebimento = ?,
                forma_pagamento_id = ?,
                observacoes = CONCAT(observacoes, ' | Recebimento: ', ?)
            WHERE id = ?
        ");
        $stmt->execute([$data_recebimento, $forma_pagamento_id, $observacoes, $id]);
        
        // Registrar no caixa
        $stmt = $db->prepare("
            INSERT INTO caixa (tipo, descricao, valor, data_movimento, categoria, forma_pagamento_id, conta_receber_id, usuario_id)
            VALUES ('entrada', ?, ?, ?, 'Recebimento', ?, ?, ?)
        ");
        $stmt->execute([
            'Recebimento: ' . $conta['descricao'] . ' - ' . $conta['paciente_nome'],
            $conta['valor_total'],
            $data_recebimento,
            $forma_pagamento_id,
            $id,
            $_SESSION['user_id'] ?? null
        ]);
        
        $db->commit();
        
        redirecionar('contas_receber.php', 'Pagamento recebido com sucesso!');
    } catch (Exception $e) {
        $db->rollBack();
        $erro = 'Erro ao processar: ' . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Receber Pagamento</h1>
    <div class="breadcrumb">
        <a href="../dashboard/index.php">Início</a> / 
        <a href="contas_receber.php">Contas a Receber</a> / Receber
    </div>
</div>

<?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?php echo $erro; ?></div>
<?php endif; ?>

<div class="card" style="max-width: 600px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <p><strong>Paciente:</strong> <?php echo htmlspecialchars($conta['paciente_nome']); ?></p>
        <p><strong>Descrição:</strong> <?php echo htmlspecialchars($conta['descricao']); ?></p>
        <p><strong>Valor:</strong> <span style="font-size: 1.5rem; color: #28a745;"><?php echo formatarDinheiro($conta['valor_total']); ?></span></p>
        <p><strong>Vencimento:</strong> <?php echo formatarData($conta['data_vencimento']); ?></p>
    </div>
    
    <form method="POST">
        <div class="form-group">
            <label>Forma de Pagamento *</label>
            <select name="forma_pagamento_id" class="form-control" required>
                <option value="">Selecione</option>
                <?php foreach ($formasPagamento as $fp): ?>
                    <option value="<?php echo $fp['id']; ?>"><?php echo htmlspecialchars($fp['descricao']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Data do Recebimento *</label>
            <input type="date" name="data_recebimento" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
        </div>
        
        <div class="form-group">
            <label>Observações</label>
            <textarea name="observacoes" class="form-control" rows="2"></textarea>
        </div>
        
        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
            <a href="contas_receber.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-check"></i> Confirmar Recebimento</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
