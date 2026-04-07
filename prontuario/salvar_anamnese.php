<?php
require_once '../includes/functions.php';
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('../pacientes/listar.php', 'Acesso inválido', 'danger');
}

$paciente_id = $_POST['paciente_id'] ?? null;
$anamnese_id = $_POST['anamnese_id'] ?? null;

if (!$paciente_id) {
    redirecionar('../pacientes/listar.php', 'Paciente não especificado', 'danger');
}

// Dados da anamnese
$dados = [
    'queixa_principal' => $_POST['queixa_principal'] ?? '',
    'historico_medico' => $_POST['historico_medico'] ?? '',
    'historico_familiar' => $_POST['historico_familiar'] ?? '',
    'medicamentos' => $_POST['medicamentos'] ?? '',
    'alergias' => $_POST['alergias'] ?? '',
    'habitos' => $_POST['habitos'] ?? '',
    'pressao_arterial' => $_POST['pressao_arterial'] ?? '',
    'diabete' => $_POST['diabete'] ?? 0,
    'problema_cardiaco' => $_POST['problema_cardiaco'] ?? 0,
    'gravida' => $_POST['gravida'] ?? 0,
    'observacoes' => $_POST['observacoes'] ?? ''
];

try {
    if ($anamnese_id) {
        // Atualizar
        $sql = "UPDATE anamneses SET 
            queixa_principal = :queixa_principal,
            historico_medico = :historico_medico,
            historico_familiar = :historico_familiar,
            medicamentos = :medicamentos,
            alergias = :alergias,
            habitos = :habitos,
            pressao_arterial = :pressao_arterial,
            diabete = :diabete,
            problema_cardiaco = :problema_cardiaco,
            gravida = :gravida,
            observacoes = :observacoes,
            data_atualizacao = CURRENT_TIMESTAMP
            WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        $dados['id'] = $anamnese_id;
        $stmt->execute($dados);
        
        redirecionar("index.php?paciente_id=$paciente_id", 'Anamnese atualizada com sucesso!');
    } else {
        // Inserir
        $sql = "INSERT INTO anamneses (
            paciente_id, queixa_principal, historico_medico, historico_familiar,
            medicamentos, alergias, habitos, pressao_arterial, diabete,
            problema_cardiaco, gravida, observacoes
        ) VALUES (
            :paciente_id, :queixa_principal, :historico_medico, :historico_familiar,
            :medicamentos, :alergias, :habitos, :pressao_arterial, :diabete,
            :problema_cardiaco, :gravida, :observacoes
        )";
        
        $stmt = $db->prepare($sql);
        $dados['paciente_id'] = $paciente_id;
        $stmt->execute($dados);
        
        redirecionar("index.php?paciente_id=$paciente_id", 'Anamnese salva com sucesso!');
    }
} catch (Exception $e) {
    redirecionar("anamnese.php?paciente_id=$paciente_id", 'Erro ao salvar: ' . $e->getMessage(), 'danger');
}
