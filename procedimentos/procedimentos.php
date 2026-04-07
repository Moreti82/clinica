<?php
$page_title = 'Procedimentos';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$procedimentos = $db->query("SELECT * FROM procedimentos ORDER BY descricao")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Procedimentos</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / Procedimentos</div>
</div>

<?php exibirFlashMessage(); ?>

<div class="card" style="margin-bottom: 25px;">
    <a href="novo.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo Procedimento</a>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Valor Padrão</th>
                    <th>Observações</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($procedimentos as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['descricao']); ?></td>
                        <td><?php echo formatarDinheiro($p['valor_padrao']); ?></td>
                        <td><?php echo htmlspecialchars($p['observacoes'] ?? ''); ?></td>
                        <td>
                            <a href="editar.php?id=<?php echo $p['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">
                                <i class="fa-solid fa-pen-to-square"></i> Editar
                            </a>
                            <a href="excluir.php?id=<?php echo $p['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem; margin-left: 5px;" onclick="return confirmarExclusao()">
                                <i class="fa-solid fa-trash"></i> Excluir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
