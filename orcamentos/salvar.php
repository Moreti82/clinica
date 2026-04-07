<?php
require_once '../includes/functions.php';
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('listar.php', 'Acesso inválido', 'danger');
}

// Dados do orçamento
$paciente_id = $_POST['paciente_id'];
$profissional_id = $_POST['profissional_id'];
$valor_total = $_POST['valor_total'];
$desconto = parseDinheiro($_POST['desconto']);
$valor_final = $_POST['valor_final'];
$validade_dias = $_POST['validade_dias'] ?? 30;
$observacoes = $_POST['observacoes'] ?? '';

try {
    $db->beginTransaction();
    
    // Inserir orçamento
    $stmt = $db->prepare("
        INSERT INTO orcamentos (paciente_id, profissional_id, valor_total, desconto, valor_final, validade_dias, observacoes)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$paciente_id, $profissional_id, $valor_total, $desconto, $valor_final, $validade_dias, $observacoes]);
    
    $orcamento_id = $db->lastInsertId();
    
    // Inserir itens
    if (!empty($_POST['itens'])) {
        $stmt = $db->prepare("
            INSERT INTO orcamento_itens (orcamento_id, procedimento_id, quantidade, valor_unitario, valor_total, dente)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($_POST['itens'] as $item) {
            $procedimento_id = $item['procedimento_id'];
            $quantidade = $item['quantidade'];
            $valor_unitario = parseDinheiro($item['valor_unitario']);
            $valor_total_item = $quantidade * $valor_unitario;
            $dente = $item['dente'] ?? null;
            
            $stmt->execute([$orcamento_id, $procedimento_id, $quantidade, $valor_unitario, $valor_total_item, $dente]);
        }
    }
    
    $db->commit();
    
    redirecionar("visualizar.php?id=$orcamento_id", 'Orçamento criado com sucesso!');
} catch (Exception $e) {
    $db->rollBack();
    redirecionar('novo.php', 'Erro ao salvar: ' . $e->getMessage(), 'danger');
}
