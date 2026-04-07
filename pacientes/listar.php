<?php
$page_title = 'Pacientes';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$stmt = $db->query("SELECT * FROM pacientes ORDER BY nome");
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Pacientes</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / Pacientes</div>
</div>

<?php exibirFlashMessage(); ?>

<div class="card" style="margin-bottom: 25px;">
    <a href="novo.php" class="btn btn-success"><i class="fa-solid fa-user-plus"></i> Novo Paciente</a>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                    <th>CPF</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pacientes as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['nome']); ?></td>
                        <td><?php echo mascararTelefone($p['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($p['email']); ?></td>
                        <td><?php echo mascararCPF($p['cpf']); ?></td>
                        <td><?php echo statusBadge($p['ativo'] ? 'Ativo' : 'Inativo'); ?></td>
                        <td style="white-space: nowrap;">
                            <a href="../odontograma/index.php?paciente_id=<?php echo $p['id']; ?>" class="btn btn-info" style="padding: 5px 10px; font-size: 0.8rem; margin: 2px; color: white;">
                                <i class="fa-solid fa-tooth"></i> ODONTOGRAMA
                            </a>
                            <a href="editar.php?id=<?php echo $p['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; margin: 2px;">
                                <i class="fa-solid fa-pen-to-square"></i> Editar
                            </a>
                            <a href="../prontuario/index.php?paciente_id=<?php echo $p['id']; ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem; margin: 2px;">
                                <i class="fa-solid fa-notes-medical"></i> Prontuário
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
