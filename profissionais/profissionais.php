<?php
$page_title = 'Profissionais';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$profissionais = $db->query("SELECT id, nome, cro, especialidade, telefone, ativo FROM profissionais WHERE ativo = 1 ORDER BY nome")
    ->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Profissionais</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / Profissionais</div>
</div>

<?php exibirFlashMessage(); ?>

<div class="card" style="margin-bottom: 25px;">
    <a href="novo.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo Profissional</a>
    <a href="inativos.php" class="btn btn-secondary"><i class="fa-solid fa-user-slash"></i> Inativos</a>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CRO</th>
                    <th>Especialidade</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profissionais as $prof): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prof['nome']); ?></td>
                        <td><?php echo htmlspecialchars($prof['cro']); ?></td>
                        <td><?php echo htmlspecialchars($prof['especialidade']); ?></td>
                        <td><?php echo mascararTelefone($prof['telefone']); ?></td>
                        <td>
                            <a href="editar.php?id=<?php echo $prof['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">Editar</a>
                            <a href="excluir.php?id=<?php echo $prof['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem; margin-left: 5px;" onclick="return confirmarExclusao()">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
