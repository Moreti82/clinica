<?php
require_once __DIR__ . '/../config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die('Paciente não informado');
}

$stmt = $db->prepare("SELECT * FROM pacientes WHERE id = :id");
$stmt->execute([':id' => $id]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    die('Paciente não encontrado');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Paciente</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Editar Paciente</h2>

    <form action="atualizar.php" method="POST">
        <input type="hidden" name="id" value="<?= $paciente['id'] ?>">

        <label>Nome *</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($paciente['nome']) ?>" required>

        <label>CPF</label>
        <input type="text" name="cpf" value="<?= $paciente['cpf'] ?>">

        <label>Telefone</label>
        <input type="text" name="telefone" value="<?= $paciente['telefone'] ?>">

        <label>E-mail</label>
        <input type="email" name="email" value="<?= $paciente['email'] ?>">

        <label>Data de Nascimento</label>
        <input type="date" name="data_nascimento" value="<?= $paciente['data_nascimento'] ?>">

        <label>Status</label>
        <select name="ativo">
            <option value="1" <?= $paciente['ativo'] ? 'selected' : '' ?>>Ativo</option>
            <option value="0" <?= !$paciente['ativo'] ? 'selected' : '' ?>>Inativo</option>
        </select>

        <button type="submit">Atualizar</button>
        <a href="listar.php" class="btn">Voltar</a>
    </form>
</div>

</body>
</html>
