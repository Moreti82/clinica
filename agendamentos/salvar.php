<?php
require_once '../config/conexao.php';

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Coleta os dados do formulário
    $paciente_id     = $_POST['paciente_id'] ?? null;
    $profissional_id = $_POST['profissional_id'] ?? null;
    $data            = $_POST['data'] ?? null;
    $hora            = $_POST['hora'] ?? null;
    $observacoes     = $_POST['observacoes'] ?? '';
    $status          = 'Agendado'; // Status padrão

    // Verificação básica dos campos obrigatórios
    if (!$paciente_id || !$profissional_id || !$data || !$hora) {
        header("Location: novo.php?erro=1"); // Redireciona com erro
        exit;
    }

    try {
        // Verificar se já existe agendamento na mesma data e hora para o mesmo profissional
        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM agendamentos 
            WHERE data = ? AND hora = ? AND profissional_id = ?
        ");
        $stmt->execute([$data, $hora, $profissional_id]);

        if ($stmt->fetchColumn() > 0) {
            // Se o horário já estiver ocupado, redireciona com erro
            header("Location: novo.php?erro=horario_ocupado");
            exit;
        }

        // Inserção do novo agendamento
        $sql = "INSERT INTO agendamentos 
                (paciente_id, profissional_id, data, hora, status, observacoes)
                VALUES 
                (:paciente_id, :profissional_id, :data, :hora, :status, :observacoes)";

        $stmt = $db->prepare($sql);

        // Executa a query com os dados coletados
        $stmt->execute([
            ':paciente_id'     => $paciente_id,
            ':profissional_id' => $profissional_id,
            ':data'            => $data,
            ':hora'            => $hora,
            ':status'          => $status,
            ':observacoes'     => $observacoes
        ]);

        // Redireciona para a página de novo agendamento com sucesso
    echo "<script>
            alert('Agendamento realizado com sucesso!');
            window.location.href = 'calendario.php?sucesso=1';
        </script>";
    exit;
    } catch (PDOException $e) {
        // Caso ocorra erro ao executar, redireciona com erro genérico
        header("Location: novo.php?erro=2");
        exit;
    }

} else {
    // Redireciona para a página de novo agendamento se não for um POST
    header("Location: novo.php");
    exit;
}
?>
