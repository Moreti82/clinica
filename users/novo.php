<?php
require_once '../auth/verifica_admin.php';
require_once '../config/conexao.php';

$perfis = $db->query("SELECT * FROM perfis")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Usuário</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>Cadastrar Usuário</h2>

<form action="salvar.php" method="POST">
    <label>Nome:</label><br>
    <input type="text" name="nome" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Senha:</label><br>
    <input type="password" name="senha" required><br><br>

    <label>Perfil:</label><br>
    <select name="perfil_id" required>
        <?php foreach ($perfis as $p): ?>
            <option value="<?= $p['id'] ?>"><?= $p['perfil'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Salvar</button>
</form>

<br>
<a href="listar.php">⬅ Voltar</a>

</body>
</html>
