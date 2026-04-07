<?php
$page_title = 'Relatórios';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

// Período padrão: mês atual
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-t');

// Estatísticas do período
// Agendamentos
$stmt = $db->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data BETWEEN ? AND ?");
$stmt->execute([$data_inicio, $data_fim]);
$totalAgendamentos = $stmt->fetch()['total'];

// Pacientes novos
$stmt = $db->prepare("SELECT COUNT(*) as total FROM pacientes WHERE DATE(created_at) BETWEEN ? AND ?");
$stmt->execute([$data_inicio, $data_fim]);
$pacientesNovos = $stmt->fetch()['total'];

// Receitas
$stmt = $db->prepare("SELECT SUM(valor) as total FROM caixa WHERE tipo = 'entrada' AND data_movimento BETWEEN ? AND ?");
$stmt->execute([$data_inicio, $data_fim]);
$totalReceitas = $stmt->fetch()['total'] ?? 0;

// Despesas
$stmt = $db->prepare("SELECT SUM(valor) as total FROM caixa WHERE tipo = 'saida' AND data_movimento BETWEEN ? AND ?");
$stmt->execute([$data_inicio, $data_fim]);
$totalDespesas = $stmt->fetch()['total'] ?? 0;

// Agendamentos por profissional
$stmt = $db->prepare("
    SELECT pr.nome as profissional, COUNT(*) as total 
    FROM agendamentos a 
    JOIN profissionais pr ON a.profissional_id = pr.id 
    WHERE a.data BETWEEN ? AND ?
    GROUP BY pr.id
");
$stmt->execute([$data_inicio, $data_fim]);
$agendamentosPorProfissional = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procedimentos mais realizados
$stmt = $db->prepare("
    SELECT proc.descricao, COUNT(*) as total
    FROM prontuarios p
    JOIN procedimentos proc ON p.procedimentos_realizados LIKE '%' || proc.descricao || '%'
    WHERE p.data_atendimento BETWEEN ? AND ?
    GROUP BY proc.id
    ORDER BY total DESC
    LIMIT 5
");
$stmt->execute([$data_inicio, $data_fim]);
$procedimentosTop = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Relatórios Gerenciais</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / Relatórios</div>
</div>

<!-- Filtro de Período -->
<div class="card" style="margin-bottom: 25px;">
    <form method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
        <div class="form-group" style="margin: 0;">
            <label>Data Início</label>
            <input type="date" name="data_inicio" class="form-control" value="<?php echo $data_inicio; ?>">
        </div>
        
        <div class="form-group" style="margin: 0;">
            <label>Data Fim</label>
            <input type="date" name="data_fim" class="form-control" value="<?php echo $data_fim; ?>">
        </div>
        
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filtrar</button>
        <button type="button" onclick="window.print()" class="btn btn-secondary"><i class="fa-solid fa-print"></i> Imprimir</button>
    </form>
</div>

<!-- Cards de Resumo -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px;">
    <div class="card" style="border-left: 4px solid #667eea;">
        <div style="font-size: 0.9rem; color: #666;">Agendamentos</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #667eea;"><?php echo $totalAgendamentos; ?></div>
    </div>
    
    <div class="card" style="border-left: 4px solid #17a2b8;">
        <div style="font-size: 0.9rem; color: #666;">Pacientes Novos</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #17a2b8;"><?php echo $pacientesNovos; ?></div>
    </div>
    
    <div class="card" style="border-left: 4px solid #28a745;">
        <div style="font-size: 0.9rem; color: #666;">Receitas</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #28a745;"><?php echo formatarDinheiro($totalReceitas); ?></div>
    </div>
    
    <div class="card" style="border-left: 4px solid #dc3545;">
        <div style="font-size: 0.9rem; color: #666;">Despesas</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #dc3545;"><?php echo formatarDinheiro($totalDespesas); ?></div>
    </div>
</div>

<!-- Gráficos -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
    
    <!-- Agendamentos por Profissional -->
    <div class="card">
        <div class="card-header"><div class="card-title">Agendamentos por Profissional</div></div>
        
        <?php if (empty($agendamentosPorProfissional)): ?>
            <div style="text-align: center; padding: 40px; color: #999;">Nenhum dado</div>
        <?php else: ?>
            <div style="padding: 20px;">
                <?php foreach ($agendamentosPorProfissional as $index => $item): ?>
                    <div style="margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span><?php echo htmlspecialchars($item['profissional']); ?></span>
                            <span style="font-weight: 600;"><?php echo $item['total']; ?></span>
                        </div>
                        <div style="background: #f0f0f0; border-radius: 10px; height: 8px;">
                            <div style="background: <?php echo gerarCor($index); ?>; height: 100%; width: <?php echo min(100, ($item['total'] / max(array_column($agendamentosPorProfissional, 'total')) * 100)); ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    
    <!-- Procedimentos mais realizados -->
    <div class="card">
        <div class="card-header"><div class="card-title">Top Procedimentos</div></div>
        
        <?php if (empty($procedimentosTop)): ?>
            <div style="text-align: center; padding: 40px; color: #999;">Nenhum dado</div>
        <?php else: ?>
            <div style="padding: 20px;">
                <?php foreach ($procedimentosTop as $index => $item): ?>
                    <div style="margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span><?php echo htmlspecialchars($item['descricao']); ?></span>
                            <span style="font-weight: 600;"><?php echo $item['total']; ?></span>
                        </div>
                        <div style="background: #f0f0f0; border-radius: 10px; height: 8px;">
                            <div style="background: <?php echo gerarCor($index + 5); ?>; height: 100%; width: <?php echo min(100, ($item['total'] / max(array_column($procedimentosTop, 'total')) * 100)); ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
