<?php
require_once '../config/conexao.php';

date_default_timezone_set('America/Campo_Grande');


/* ======================================================
   FUNÇÃO PARA O CALENDÁRIO (NOVO)
   ====================================================== */

function listarAgendamentosPorMes($db, $mes, $ano) {

    // Garantir formato correto do mês (01, 02, 03...)
    $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);

    $inicio = "$ano-$mes-01";
    $fim = date("Y-m-t", strtotime($inicio));

    $sql = "
        SELECT data, COUNT(*) as total
        FROM agendamentos
        WHERE data BETWEEN :inicio AND :fim
        GROUP BY data
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':inicio' => $inicio,
        ':fim' => $fim
    ]);

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $agendamentos = [];

    foreach ($resultados as $linha) {
        $agendamentos[$linha['data']] = $linha['total'];
    }

    return $agendamentos;
}

function listarAgendamentosPorData($db, $data) {

    $sql = "
        SELECT 
            a.hora,
            p.nome AS paciente,
            pr.nome AS profissional,
            a.observacoes,
            a.status
        FROM agendamentos a
        JOIN pacientes p ON a.paciente_id = p.id
        JOIN profissionais pr ON a.profissional_id = pr.id
        WHERE a.data = :data
        ORDER BY a.hora
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([':data' => $data]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
