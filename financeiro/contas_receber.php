<?php
$page_title = 'Contas a Receber';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

// Filtros
$filtro_status = $_GET['status'] ?? 'todos';
$filtro_paciente = $_GET['paciente'] ?? '';

// Construir query
$sql = "
    SELECT c.*, p.nome as paciente_nome, fp.descricao as forma_pagamento
    FROM contas_receber c
    JOIN pacientes p ON c.paciente_id = p.id
    LEFT JOIN formas_pagamento fp ON c.forma_pagamento_id = fp.id
    WHERE 1=1
";
$params = [];

if ($filtro_status !== 'todos') {
    $sql .= " AND c.status = ?";
    $params[] = ucfirst($filtro_status);
}

if ($filtro_paciente) {
    $sql .= " AND p.nome LIKE ?";
    $params[] = "%$filtro_paciente%";
}

$sql .= " ORDER BY c.data_vencimento ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$contas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Totais
$totalPendente = array_sum(array_column(array_filter($contas, fn($c) => $c['status'] === 'Pendente'), 'valor_total'));
$totalRecebido = array_sum(array_column(array_filter($contas, fn($c) => $c['status'] === 'Recebido'), 'valor_total'));

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Contas a Receber</h1>
    <div class="breadcrumb">
        <a href="../dashboard/index.php">Início</a> / Financeiro / Contas a Receber
    </div>
</div>

<?php exibirFlashMessage(); ?>

<!-- Resumo -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
    <div class="card" style="border-left: 4px solid #ffc107;">
        <div style="font-size: 0.9rem; color: #666;">Total Pendente</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #ffc107;"><?php echo formatarDinheiro($totalPendente); ?></div>
    </div>
    
    <div class="card" style="border-left: 4px solid #28a745;">
        <div style="font-size: 0.9rem; color: #666;">Total Recebido</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #28a745;"><?php echo formatarDinheiro($totalRecebido); ?></div>
    </div>
    
    <div class="card" style="border-left: 4px solid #17a2b8;">
        <div style="font-size: 0.9rem; color: #666;">Total</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #17a2b8;"><?php echo formatarDinheiro($totalPendente + $totalRecebido); ?></div>
    </div>
</div>

<!-- Filtros -->
<div class="card" style="margin-bottom: 25px;">
    <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="margin: 0; flex: 1; min-width: 200px;">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="todos" <?php echo $filtro_status === 'todos' ? 'selected' : ''; ?>>Todos</option>
                <option value="pendente" <?php echo $filtro_status === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                <option value="recebido" <?php echo $filtro_status === 'recebido' ? 'selected' : ''; ?>>Recebido</option>
            </select>
        </div>
        
        <div class="form-group" style="margin: 0; flex: 2; min-width: 200px;">
            <label>Paciente</label>
            <input type="text" name="paciente" class="form-control" value="<?php echo htmlspecialchars($filtro_paciente); ?>" placeholder="Buscar por nome...">
        </div>
        
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Filtrar</button>
        <a href="nova_conta.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Nova Conta</a>
    </form>
</div>

<!-- Lista -->
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contas as $conta): 
                    $vencida = $conta['status'] === 'Pendente' && strtotime($conta['data_vencimento']) < strtotime('today');
                ?>
                    <tr <?php echo $vencida ? 'style="background: #fff3cd;"' : ''; ?>>
                        <td><?php echo htmlspecialchars($conta['paciente_nome']); ?></td>
                        <td><?php echo htmlspecialchars($conta['descricao']); ?></td>
                        <td style="font-weight: 600;"><?php echo formatarDinheiro($conta['valor_total']); ?></td>
                        <td>
                            <?php echo formatarData($conta['data_vencimento']); ?>
                            <?php if ($vencida): ?>
                                <span class="badge badge-danger">Vencida</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo statusBadge($conta['status']); ?></td>
                        <td>
                            <?php if ($conta['status'] === 'Pendente'): ?>
                                <a href="receber.php?id=<?php echo $conta['id']; ?>" class="btn btn-success" style="padding: 5px 10px; font-size: 0.8rem;">
                                    <i class="fa-solid fa-check"></i> Receber
                                </a>
                            <?php else: ?>
                                <span style="color: #28a745;"><i class="fa-solid fa-check-circle"></i> <?php echo formatarData($conta['data_recebimento']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
