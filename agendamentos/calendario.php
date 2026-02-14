<?php
require_once 'controller.php';

date_default_timezone_set('America/Campo_Grande');

$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');

$mes = (int)$mes;
$ano = (int)$ano;

$hoje = date('Y-m-d');

/* ===============================
   MÊS ANTERIOR
================================= */

$mesAnterior = $mes - 1;
$anoAnterior = $ano;

if ($mesAnterior < 1) {
    $mesAnterior = 12;
    $anoAnterior--;
}

/* ===============================
   PRÓXIMO MÊS
================================= */

$mesProximo = $mes + 1;
$anoProximo = $ano;

if ($mesProximo > 12) {
    $mesProximo = 1;
    $anoProximo++;
}

/* ===============================
   FERIADOS FIXOS (BRASIL - exemplo)
================================= */

$feriados = [
    "$ano-01-01", // Confraternização Universal
    "$ano-04-21", // Tiradentes
    "$ano-09-07", // Independência
    "$ano-10-12", // Nossa Senhora Aparecida
    "$ano-11-02", // Finados
    "$ano-11-15", // Proclamação da República
    "$ano-12-25"  // Natal
];

/* ===============================
   AGENDAMENTOS DO MÊS
================================= */

$agendamentos = listarAgendamentosPorMes($db, $mes, $ano);

$primeiroDia = mktime(0, 0, 0, $mes, 1, $ano);
$diasNoMes = date('t', $primeiroDia);
$diaSemanaInicio = date('w', $primeiroDia);

/* ===============================
   FORMATAR NOME DO MÊS
================================= */

$dataObj = DateTime::createFromFormat('Y-m-d', "$ano-$mes-01");

$formatter = new IntlDateFormatter(
    'pt_BR',
    IntlDateFormatter::LONG,
    IntlDateFormatter::NONE,
    'America/Campo_Grande',
    IntlDateFormatter::GREGORIAN,
    "MMMM 'de' yyyy"
);

$mesFormatado = ucfirst($formatter->format($dataObj));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Calendário</title>
    <link rel="stylesheet" href="../assets/css/style-calendario.css">
</head>
<body id="calendario-page">

<div class="calendario-container">

    <div class="calendario-header">

        <a href="../index.php" class="btn-voltar">⬅ Voltar</a>

        <div class="mes-navegacao">
            <a href="?mes=<?= $mesAnterior ?>&ano=<?= $anoAnterior ?>" class="nav-btn">⬅</a>
            <h2><?= $mesFormatado ?></h2>
            <a href="?mes=<?= $mesProximo ?>&ano=<?= $anoProximo ?>" class="nav-btn">➡</a>
        </div>

    </div>


    <div class="calendario-grid">

    <?php
    $diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

    foreach ($diasSemana as $diaNome) {
        echo "<div class='dia-semana'>$diaNome</div>";
    }

    // Espaços vazios antes do primeiro dia
    for ($i = 0; $i < $diaSemanaInicio; $i++) {
        echo "<div class='dia vazio'></div>";
    }

    // Dias do mês
    for ($dia = 1; $dia <= $diasNoMes; $dia++) {

        $dataAtual = sprintf("%04d-%02d-%02d", $ano, $mes, $dia);
        $diaSemana = date('w', strtotime($dataAtual)); // 0 = dom | 6 = sab

        $temAgendamento = isset($agendamentos[$dataAtual]);
        $total = $temAgendamento ? $agendamentos[$dataAtual] : 0;

        $classe = "dia";

        // Final de semana
        if ($diaSemana == 0 || $diaSemana == 6) {
            $classe .= " fim-semana";
        }

        // Feriado
        if (in_array($dataAtual, $feriados)) {
            $classe .= " feriado";
        }

        // Dia com agendamento
        if ($temAgendamento) {
            $classe .= " com-agendamento";
        }

        // Dia atual
        if ($dataAtual === $hoje) {
            $classe .= " hoje";
        }

        echo "<div class='$classe'>";
        echo "<a href='agendamentos.php?data=$dataAtual'>";
        echo "<span class='numero'>$dia</span>";

        if ($temAgendamento) {
            echo "<span class='badge'>$total</span>";
        }

        echo "</a>";
        echo "</div>";
    }
    ?>

    </div>

</div>

</body>
</html>
