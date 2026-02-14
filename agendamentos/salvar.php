<?php
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $paciente_id     = $_POST['paciente_id'] ?? null;
    $profissional_id = $_POST['profissional_id'] ?? null;
    $data            = $_POST['data'] ?? null;
    $hora            = $_POST['hora'] ?? null;
    $observacoes     = $_POST['observacoes'] ?? '';
    $status          = 'Agendado';

    // Validação básica
    if (!$paciente_id || !$profissional_id || !$data || !$hora) {
        header("Location: novo.php?erro=1");
        exit;
    }

    try {

        // 🔒 Verificar se já existe agendamento na mesma data e hora
        $stmt = $db->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = ? AND hora = ?");
        $stmt->execute([$data, $hora]);

        if ($stmt->fetchColumn() > 0) {
            header("Location: novo.php?erro=horario_ocupado");
            exit;
        }

        // ✅ Inserir agendamento
        $sql = "INSERT INTO agendamentos 
                (paciente_id, profissional_id, data, hora, status, observacoes)
                VALUES 
                (:paciente_id, :profissional_id, :data, :hora, :status, :observacoes)";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':paciente_id'     => $paciente_id,
            ':profissional_id' => $profissional_id,
            ':data'            => $data,
            ':hora'            => $hora,
            ':status'          => $status,
            ':observacoes'     => $observacoes
        ]);

        header("Location: novo.php?sucesso=1");
        exit;

    } catch (PDOException $e) {

        header("Location: novo.php?erro=2");
        exit;
    }

} else {
    header("Location: novo.php");
    exit;
}
