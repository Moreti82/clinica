<?php
require_once '../config/conexao.php';
require_once '../auth/verifica_admin.php';

$sql = "
SELECT u.id, u.nome, u.email, u.ativo, p.perfil
FROM users u
JOIN perfis p ON p.id = u.perfil_id
ORDER BY u.nome
";

$usuarios = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Usuários</title>
    <link rel="stylesheet" href="../assets/css/style-listar-usuarios.css">
</head>

<body id="usuarios-page"><!-- ID ÚNICO DA PÁGINA -->

<div class="usuarios-container"><!-- CONTAINER EXCLUSIVO -->

    <h2 class="usuarios-title">Usuários do Sistema</h2>

    <div class="usuarios-menu">
        <a href="novo.php" class="btn usuarios-btn">➕ Novo Usuário</a>
        <a href="../index.php" class="btn usuarios-btn-sec">⬅ Voltar</a>
    </div>

    <div class="usuarios-table-wrapper">
        <table class="usuarios-table">
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
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['perfil']) ?></td>
                    <td class="<?= $u['ativo'] ? 'usuarios-status-ativo' : 'usuarios-status-inativo' ?>">
                        <?= $u['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
