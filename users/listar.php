<?php
$page_title = 'Usuários';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$sql = "SELECT u.id, u.nome, u.email, u.ativo, p.perfil FROM users u JOIN perfis p ON p.id = u.perfil_id ORDER BY u.nome";
$usuarios = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="page-header">
 	<h1 class="page-title">Usuários do Sistema</h1>
 	<div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / Usuários</div>
</div>

<?php exibirFlashMessage(); ?>

<div class="card" style="margin-bottom: 25px;">
 	<a href="novo.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo Usuário</a>
</div>

<div class="card">
 	<div class="table-container">
 		<table>
 			<thead>
 				<tr>
 					<th>Nome</th>
 					<th>Email</th>
 					<th>Perfil</th>
 					<th>Status</th>
 				</tr>
 			</thead>
 			<tbody>
 				<?php foreach ($usuarios as $u): ?>
 				<tr>
 					<td><?php echo htmlspecialchars($u['nome']); ?></td>
 					<td><?php echo htmlspecialchars($u['email']); ?></td>
 					<td><?php echo htmlspecialchars($u['perfil']); ?></td>
 					<td><?php echo statusBadge($u['ativo'] ? 'Ativo' : 'Inativo'); ?></td>
 				</tr>
 				<?php endforeach; ?>
 			</tbody>
 		</table>
 	</div>
</div>

<?php include '../includes/footer.php'; ?>