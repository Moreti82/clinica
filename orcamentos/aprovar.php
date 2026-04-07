<?php
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$id = $_GET['id'] ?? null;
$acao = $_GET['acao'] ?? null;

if (!$id || !in_array($acao, ['aprovado', 'recusado'])) {
    redirecionar('listar.php', 'Ação inválida', 'danger');
}

// Buscar orçamento
$stmt = $db->prepare("SELECT * FROM orcamentos WHERE id = ?");
$stmt->execute([$id]);
$orcamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orcamento) {
    redirecionar('listar.php', 'Orçamento não encontrado', 'danger');
}

if ($orcamento['status'] !== 'Pendente') {
    redirecionar('listar.php', 'Este orçamento já foi ' . strtolower($orcamento['status']), 'warning');
}

try {
    $db->beginTransaction();
    
    // Atualizar status do orçamento
    $stmt = $db->prepare("UPDATE orcamentos SET status = ? WHERE id = ?");
    $stmt->execute([ucfirst($acao), $id]);
    
    // Se aprovado, criar conta a receber
    if ($acao === 'aprovado') {
        $stmt = $db->prepare("
            INSERT INTO contas_receber (paciente_id, descricao, valor_total, data_vencimento, observacoes, orcamento_id)
            VALUES (?, ?, ?, DATE('now', '+30 days'), ?, ?)
        ");
        $stmt->execute([
            $orcamento['paciente_id'],
            'Orçamento #' . $id,
            $orcamento['valor_final'],
            'Gerado automaticamente da aprovação do orçamento #' . $id,
            $id
        ]);
    }
    
    $db->commit();
    
    $mensagem = $acao === 'aprovado' ? 'Orçamento aprovado! Conta a receber gerada.' : 'Orçamento recusado.';
    redirecionar('listar.php', $mensagem);
} catch (Exception $e) {
    $db->rollBack();
    redirecionar('visualizar.php?id=' . $id, 'Erro: ' . $e->getMessage(), 'danger');
}
