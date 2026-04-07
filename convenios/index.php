<?php
$page_title = 'Convênios';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$convenios = $db->query("SELECT * FROM convenios WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Convênios</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / Convênios</div>
</div>

<?php exibirFlashMessage(); ?>

<div class="card" style="margin-bottom: 25px;">
    <a href="novo.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo Convênio</a>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Telefone</th>
                    <th>Desconto Padrão</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($convenios as $c): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c['nome']); ?></td>
                        <td><?php echo mascararCPF($c['cnpj']); ?></td>
                        <td><?php echo mascararTelefone($c['telefone']); ?></td>
                        <td><?php echo $c['desconto_padrao']; ?>%</td>
                        <td>
                            <a href="precos.php?id=<?php echo $c['id']; ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">
                                <i class="fa-solid fa-dollar-sign"></i> Preços
                            </a>
                            <a href="editar.php?id=<?php echo $c['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; margin-left: 5px;">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
