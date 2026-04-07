<?php
$page_title = 'Calendário';
require_once '../includes/functions.php';
require_once 'controller.php';

date_default_timezone_set('America/Campo_Grande');

$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');
$mes = (int)$mes;
$ano = (int)$ano;
$hoje = date('Y-m-d');

$mesAnterior = $mes - 1;
$anoAnterior = $ano;
if ($mesAnterior < 1) {
    $mesAnterior = 12;
    $anoAnterior--;
}

$mesProximo = $mes + 1;
$anoProximo = $ano;
if ($mesProximo > 12) {
    $mesProximo = 1;
    $anoProximo++;
}

$feriados = [
    "$ano-01-01", "$ano-04-21", "$ano-09-07",
    "$ano-10-12", "$ano-11-02", "$ano-11-15", "$ano-12-25"
];

$agendamentos = listarAgendamentosPorMes($db, $mes, $ano);

$primeiroDia = mktime(0, 0, 0, $mes, 1, $ano);
$diasNoMes = date('t', $primeiroDia);
$diaSemanaInicio = date('w', $primeiroDia);

$dataObj = DateTime::createFromFormat('Y-m-d', "$ano-$mes-01");
$formatter = new IntlDateFormatter('pt_BR', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'America/Campo_Grande', IntlDateFormatter::GREGORIAN, "MMMM 'de' yyyy");
$mesFormatado = ucfirst($formatter->format($dataObj));

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Calendário de Agendamentos</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / Calendário</div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="?mes=<?php echo $mesAnterior; ?>&ano=<?php echo $anoAnterior; ?>" class="btn btn-secondary"><i class="fa-solid fa-chevron-left"></i> Mês Anterior</a>
        <h2 style="margin: 0; color: white;"><?php echo $mesFormatado; ?></h2>
        <a href="?mes=<?php echo $mesProximo; ?>&ano=<?php echo $anoProximo; ?>" class="btn btn-secondary">Próximo Mês <i class="fa-solid fa-chevron-right"></i></a>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; text-align: center;">
        <?php
        $diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        foreach ($diasSemana as $diaNome) {
            echo "<div style='font-weight: 600; padding: 10px; background: #667eea; color: white; border-radius: 8px;'>$diaNome</div>";
        }
        
        for ($i = 0; $i < $diaSemanaInicio; $i++) {
            echo "<div></div>";
        }
        
        for ($dia = 1; $dia <= $diasNoMes; $dia++) {
            $dataAtual = sprintf("%04d-%02d-%02d", $ano, $mes, $dia);
            $diaSemana = date('w', strtotime($dataAtual));
            $temAgendamento = isset($agendamentos[$dataAtual]);
            $total = $temAgendamento ? $agendamentos[$dataAtual] : 0;
            
            $style = "padding: 15px; border-radius: 8px; cursor: pointer; transition: all 0.3s; ";
            $style .= ($diaSemana == 0 || $diaSemana == 6) ? "background: #f0f0f0; " : "background: white; ";
            $style .= ($dataAtual === $hoje) ? "border: 2px solid #667eea; " : "border: 1px solid #e0e0e0; ";
            $style .= "position: relative;";
            
            echo "<a href='agendamentos.php?data=$dataAtual' style='text-decoration: none; color: inherit;'>";
            echo "<div style='$style' onmouseover='this.style.transform=\"scale(1.05)\"' onmouseout='this.style.transform=\"scale(1)\"'>";
            echo "<div style='font-size: 1.2rem; font-weight: 600;'>$dia</div>";
            
            if ($temAgendamento) {
                echo "<span style='position: absolute; top: 5px; right: 5px; background: #667eea; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 0.75rem; display: flex; align-items: center; justify-content: center;'>$total</span>";
            }
            
            echo "</div>";
            echo "</a>";
        }
        ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
