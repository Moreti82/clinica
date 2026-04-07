<?php
$page_title = 'Orçamentos';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

// Filtros
$filtro_status = $_GET['status'] ?? 'todos';

$sql = "
    SELECT o.*, p.nome as paciente_nome, pr.nome as profissional_nome
    FROM orcamentos o
    JOIN pacientes p ON o.paciente_id = p.id
    JOIN profissionais pr ON o.profissional_id = pr.id
    WHERE 1=1
";
$params = [];

if ($filtro_status !== 'todos') {
    $sql .= " AND o.status = ?";
    $params[] = ucfirst($filtro_status);
}

$sql .= " ORDER BY o.data_orcamento DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$orcamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Orçamentos</h1>
    <div class="breadcrumb">
        <a href="../dashboard/index.php">Início</a> / Orçamentos
    </div>
</div>

<?php exibirFlashMessage(); ?>

<!-- Filtros -->
<div class="card" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
            <div class="form-group" style="margin: 0;">
                <label>Status</label>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="todos" <?php echo $filtro_status === 'todos' ? 'selected' : ''; ?>>Todos</option>
                    <option value="pendente" <?php echo $filtro_status === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                    <option value="aprovado" <?php echo $filtro_status === 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                    <option value="recusado" <?php echo $filtro_status === 'recusado' ? 'selected' : ''; ?>>Recusado</option>
                </select>
            </div>
        </form>
        
        <a href="novo.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo Orçamento</a>
    </div>
</div>

<!-- Lista -->
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Data</th>
                    <th>Paciente</th>
                    <th>Profissional</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orcamentos as $orc): ?>
                    <tr>
                        <td>#<?php echo $orc['id']; ?></td>
                        <td><?php echo formatarData($orc['data_orcamento']); ?></td>
                        <td><?php echo htmlspecialchars($orc['paciente_nome']); ?></td>
                        <td><?php echo htmlspecialchars($orc['profissional_nome']); ?></td>
                        <td style="font-weight: 600;"><?php echo formatarDinheiro($orc['valor_final']); ?></td>
                        <td><?php echo statusBadge($orc['status']); ?></td>
                        <td>
                            <a href="visualizar.php?id=<?php echo $orc['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">
                                <i class="fa-solid fa-eye"></i> Ver
                            </a>
                            
                            <?php if ($orc['status'] === 'Pendente'): ?>
                                <a href="aprovar.php?id=<?php echo $orc['id']; ?>&acao=aprovado" class="btn btn-success" style="padding: 5px 10px; font-size: 0.8rem; margin-left: 5px;">
                                    <i class="fa-solid fa-check"></i> Aprovar
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
