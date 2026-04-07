<?php
require_once '../includes/functions.php';
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('../pacientes/listar.php', 'Acesso inválido', 'danger');
}

$paciente_id = $_POST['paciente_id'] ?? null;
$agendamento_id = $_POST['agendamento_id'] ?? null;

if (!$paciente_id) {
    redirecionar('../pacientes/listar.php', 'Paciente não especificado', 'danger');
}

$dados = [
    'paciente_id' => $paciente_id,
    'profissional_id' => $_POST['profissional_id'] ?? null,
    'agendamento_id' => $agendamento_id ?: null,
    'data_atendimento' => $_POST['data_atendimento'] ?? date('Y-m-d'),
    'hora_atendimento' => $_POST['hora_atendimento'] ?: null,
    'queixa' => $_POST['queixa'] ?? '',
    'diagnostico' => $_POST['diagnostico'] ?? '',
    'procedimentos_realizados' => $_POST['procedimentos_realizados'] ?? '',
    'prescricao' => $_POST['prescricao'] ?? '',
    'observacoes' => $_POST['observacoes'] ?? ''
];

// Validação
if (empty($dados['profissional_id'])) {
    redirecionar("novo.php?paciente_id=$paciente_id", 'Selecione o profissional', 'danger');
}

try {
    $sql = "INSERT INTO prontuarios (
        paciente_id, profissional_id, agendamento_id, data_atendimento, hora_atendimento,
        queixa, diagnostico, procedimentos_realizados, prescricao, observacoes
    ) VALUES (
        :paciente_id, :profissional_id, :agendamento_id, :data_atendimento, :hora_atendimento,
        :queixa, :diagnostico, :procedimentos_realizados, :prescricao, :observacoes
    )";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($dados);
    
    // Se veio de um agendamento, atualizar status para Concluído
    if ($agendamento_id) {
        $stmt = $db->prepare("UPDATE agendamentos SET status = 'Concluído' WHERE id = ?");
        $stmt->execute([$agendamento_id]);
    }
    
    redirecionar("index.php?paciente_id=$paciente_id", 'Atendimento registrado com sucesso!');
} catch (Exception $e) {
    redirecionar("novo.php?paciente_id=$paciente_id", 'Erro ao salvar: ' . $e->getMessage(), 'danger');
}
