<?php
require_once '../auth/verifica_admin.php';
require_once '../config/conexao.php';

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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<body>
<div class="container">

<h2>Usuários do Sistema</h2>

<div class="menu">
    <a href="novo.php" class="btn">➕ Novo Usuário</a>
    <a href="../public/dashboard.php">⬅ Voltar</a>
</div>

<table border="1" cellpadding="5">
    <tr>
        <th>Nome</th>
        <th>Email</th>
        <th>Perfil</th>
        <th>Status</th>
    </tr>

    <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['nome']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['perfil'] ?></td>
            <td class="<?= $u['ativo'] ? 'status-ativo' : 'status-inativo' ?>">
    <?= $u['ativo'] ? 'Ativo' : 'Inativo' ?>
</td>

        </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="../public/dashboard.php">⬅ Voltar</a>

</body>
</html>
