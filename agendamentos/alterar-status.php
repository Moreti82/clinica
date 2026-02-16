<?php
require_once '../config/conexao.php';

$agendamentoId = $_POST['agendamento_id'] ?? null;
$novoStatus    = $_POST['status'] ?? null;

$statusPermitidos = ['Agendado','Confirmado','Cancelado','Concluído'];

header('Content-Type: application/json'); // importante

if ($agendamentoId && in_array($novoStatus, $statusPermitidos)) {
    $stmt = $db->prepare("UPDATE agendamentos SET status = ? WHERE id = ?");
    $ok = $stmt->execute([$novoStatus, $agendamentoId]);

    echo json_encode(['sucesso' => $ok]);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados inválidos']);
}
