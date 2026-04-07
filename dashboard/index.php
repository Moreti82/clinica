<?php
$page_title = 'Dashboard';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

// Estatísticas do dia de hoje
$hoje = date('Y-m-d');
$inicioMes = date('Y-m-01');
$fimMes = date('Y-m-t');

// Total de agendamentos hoje
$stmt = $db->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data = ? AND status IN ('Agendado', 'Confirmado')");
$stmt->execute([$hoje]);
$agendamentosHoje = $stmt->fetch()['total'];

// Total de agendamentos no mês
$stmt = $db->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data BETWEEN ? AND ? AND status IN ('Agendado', 'Confirmado')");
$stmt->execute([$inicioMes, $fimMes]);
$agendamentosMes = $stmt->fetch()['total'];

// Total de pacientes ativos
$stmt = $db->query("SELECT COUNT(*) as total FROM pacientes WHERE ativo = 1");
$totalPacientes = $stmt->fetch()['total'];

// Pacientes novos no mês
$stmt = $db->prepare("SELECT COUNT(*) as total FROM pacientes WHERE DATE(created_at) BETWEEN ? AND ?");
$stmt->execute([$inicioMes, $fimMes]);
$pacientesNovos = $stmt->fetch()['total'];

// Próximos agendamentos
$stmt = $db->prepare("
    SELECT a.*, p.nome as paciente_nome, pr.nome as profissional_nome 
    FROM agendamentos a 
    JOIN pacientes p ON a.paciente_id = p.id 
    JOIN profissionais pr ON a.profissional_id = pr.id 
    WHERE a.data >= ? AND a.status IN ('Agendado', 'Confirmado')
    ORDER BY a.data, a.hora 
    LIMIT 10
");
$stmt->execute([$hoje]);
$proximosAgendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Aniversariantes do mês
$mesAtual = date('m');
$stmt = $db->prepare("
    SELECT id, nome, data_nascimento, telefone 
    FROM pacientes 
    WHERE ativo = 1 AND strftime('%m', data_nascimento) = ?
    ORDER BY strftime('%d', data_nascimento)
");
$stmt->execute([$mesAtual]);
$aniversariantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agendamentos por profissional (para gráfico)
$stmt = $db->prepare("
    SELECT pr.nome as profissional, COUNT(*) as total 
    FROM agendamentos a 
    JOIN profissionais pr ON a.profissional_id = pr.id 
    WHERE a.data BETWEEN ? AND ? AND a.status IN ('Agendado', 'Confirmado')
    GROUP BY pr.id
");
$stmt->execute([$inicioMes, $fimMes]);
$agendamentosPorProfissional = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <div class="breadcrumb">
        <a href="index.php">Início</a> / Dashboard
    </div>
</div>

<?php exibirFlashMessage(); ?>

<!-- Cards de Estatísticas -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    
    <!-- Agendamentos Hoje -->
    <div class="card" style="border-left: 4px solid #667eea;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">Agendamentos Hoje</div>
                <div style="font-size: 2rem; font-weight: 700; color: #667eea;"><?php echo $agendamentosHoje; ?></div>
            </div>
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-calendar-day" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <a href="../agendamentos/calendario.php" style="display: block; margin-top: 15px; color: #667eea; text-decoration: none; font-size: 0.9rem;">
            Ver calendário →
        </a>
    </div>
    
    <!-- Agendamentos no Mês -->
    <div class="card" style="border-left: 4px solid #28a745;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">Agendamentos no Mês</div>
                <div style="font-size: 2rem; font-weight: 700; color: #28a745;"><?php echo $agendamentosMes; ?></div>
            </div>
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-calendar-alt" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <div style="margin-top: 15px; color: #666; font-size: 0.9rem;">
            <?php echo nomeMes(date('n')); ?> de <?php echo date('Y'); ?>
        </div>
    </div>
    
    <!-- Total de Pacientes -->
    <div class="card" style="border-left: 4px solid #17a2b8;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">Total de Pacientes</div>
                <div style="font-size: 2rem; font-weight: 700; color: #17a2b8;"><?php echo $totalPacientes; ?></div>
            </div>
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-users" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <div style="margin-top: 15px; color: #666; font-size: 0.9rem;">
            <span style="color: #28a745; font-weight: 600;">+<?php echo $pacientesNovos; ?></span> novos este mês
        </div>
    </div>
    
    <!-- Profissionais Ativos -->
    <div class="card" style="border-left: 4px solid #ffc107;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">Profissionais Ativos</div>
                <div style="font-size: 2rem; font-weight: 700; color: #ffc107;">
                    <?php 
                    $stmt = $db->query("SELECT COUNT(*) as total FROM profissionais WHERE ativo = 1");
                    echo $stmt->fetch()['total'];
                    ?>
                </div>
            </div>
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-user-md" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <a href="../profissionais/profissionais.php" style="display: block; margin-top: 15px; color: #ffc107; text-decoration: none; font-size: 0.9rem;">
            Gerenciar →
        </a>
    </div>
</div>

<!-- Conteúdo em duas colunas -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
    
    <!-- Coluna Principal -->
    <div>
        <!-- Próximos Agendamentos -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-clock"></i> Próximos Agendamentos
                </div>
                <a href="../agendamentos/novo.php" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.85rem;">
                    <i class="fa-solid fa-plus"></i> Novo
                </a>
            </div>
            
            <?php if (empty($proximosAgendamentos)): ?>
                <div style="text-align: center; padding: 40px; color: #999;">
                    <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; margin-bottom: 15px;"></i>
                    <p>Nenhum agendamento próximo</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Paciente</th>
                                <th>Profissional</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proximosAgendamentos as $ag): ?>
                                <tr>
                                    <td>
                                        <?php echo formatarData($ag['data']); ?> <br>
                                        <small style="color: #667eea;"><?php echo $ag['hora']; ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($ag['paciente_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($ag['profissional_nome']); ?></td>
                                    <td><?php echo statusBadge($ag['status']); ?></td>
                                    <td>
                                        <a href="../agendamentos/agendamentos.php?data=<?php echo $ag['data']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Gráfico de Agendamentos por Profissional -->
        <?php if (!empty($agendamentosPorProfissional)): ?>
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-chart-bar"></i> Agendamentos por Profissional
                </div>
            </div>
            
            <div style="padding: 20px;">
                <?php foreach ($agendamentosPorProfissional as $index => $item): ?>
                    <div style="margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span><?php echo htmlspecialchars($item['profissional']); ?></span>
                            <span style="font-weight: 600;"><?php echo $item['total']; ?></span>
                        </div>
                        <div style="background: #f0f0f0; border-radius: 10px; height: 8px; overflow: hidden;">
                            <div style="background: <?php echo gerarCor($index); ?>; height: 100%; width: <?php echo min(100, ($item['total'] / max(array_column($agendamentosPorProfissional, 'total')) * 100)); ?>%; transition: width 0.5s;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    
    </div>
    
    <!-- Coluna Lateral -->
    <div>
        
        <!-- Aniversariantes do Mês -->
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <i class="fa-solid fa-birthday-cake" style="font-size: 1.5rem;"></i>
                <div class="card-title" style="color: white; margin: 0;">Aniversariantes</div>
            </div>
            
            <?php if (empty($aniversariantes)): ?>
                <p style="opacity: 0.9;">Nenhum aniversariante este mês</p>
            <?php else: ?>
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php foreach ($aniversariantes as $aniv): 
                        $diaAniv = date('d', strtotime($aniv['data_nascimento']));
                        $hojeDia = date('d');
                        $isHoje = ($diaAniv == $hojeDia);
                    ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.2); <?php echo $isHoje ? 'background: rgba(255,255,255,0.2); margin: 0 -10px; padding: 10px; border-radius: 8px;' : ''; ?>">
                            <div>
                                <div style="font-weight: 600;"><?php echo htmlspecialchars(primeiroUltimoNome($aniv['nome'])); ?></div>
                                <?php if ($isHoje): ?>
                                    <div style="font-size: 0.8rem; color: #ffd700;">🎉 Hoje!</div>
                                <?php endif; ?>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.2rem; font-weight: 700;"><?php echo $diaAniv; ?></div>
                                <div style="font-size: 0.75rem; opacity: 0.8;"><?php echo nomeMes(date('n')); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        
        <!-- Acesso Rápido -->
        <div class="card">
            <div class="card-title" style="margin-bottom: 20px;">Acesso Rápido</div>
            
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="../pacientes/novo.php" class="btn btn-primary" style="justify-content: flex-start;">
                    <i class="fa-solid fa-user-plus"></i> Novo Paciente
                </a>
                
                <a href="../agendamentos/novo.php" class="btn btn-success" style="justify-content: flex-start;">
                    <i class="fa-solid fa-calendar-plus"></i> Novo Agendamento
                </a>
                
                <a href="../orcamentos/novo.php" class="btn btn-warning" style="justify-content: flex-start;">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Novo Orçamento
                </a>
                
                <a href="../financeiro/caixa.php" class="btn btn-secondary" style="justify-content: flex-start;">
                    <i class="fa-solid fa-cash-register"></i> Abrir Caixa
                </a>
            </div>
        </div>
    
    </div>
</div>

<?php include '../includes/footer.php'; ?>
