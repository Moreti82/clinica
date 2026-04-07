<?php
require_once '../includes/functions.php';
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('../pacientes/listar.php', 'Acesso inválido', 'danger');
}

$paciente_id = $_POST['paciente_id'] ?? null;
$dente = $_POST['dente'] ?? null;
$condicao = $_POST['condicao'] ?? 'Saudavel';
$procedimento_id = $_POST['procedimento_id'] ?: null;
$observacoes = $_POST['observacoes'] ?? '';

if (!$paciente_id || !$dente) {
    redirecionar('../pacientes/listar.php', 'Dados incompletos', 'danger');
}

// Verificar se já existe registro para este dente
$stmt = $db->prepare("SELECT id FROM odontograma WHERE paciente_id = ? AND dente = ?");
$stmt->execute([$paciente_id, $dente]);
$existe = $stmt->fetch();

try {
    if ($existe) {
        // Atualizar
        $stmt = $db->prepare("
            UPDATE odontograma 
            SET condicao = ?, procedimento_id = ?, observacoes = ?, data_registro = CURRENT_DATE
            WHERE id = ?
        ");
        $stmt->execute([$condicao, $procedimento_id, $observacoes, $existe['id']]);
    } else {
        // Inserir
        $stmt = $db->prepare("
            INSERT INTO odontograma (paciente_id, dente, condicao, procedimento_id, observacoes)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$paciente_id, $dente, $condicao, $procedimento_id, $observacoes]);
    }
    
    redirecionar("index.php?paciente_id=$paciente_id", 'Odontograma atualizado!');
} catch (Exception $e) {
    redirecionar("index.php?paciente_id=$paciente_id", 'Erro: ' . $e->getMessage(), 'danger');
}
