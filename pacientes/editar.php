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
    <link rel="stylesheet" href="../assets/css/style-editar-pacientes.css">
</head>
<body id="editar-paciente-page">

<div class="editar-paciente-container">
    <h2 class="editar-paciente-titulo">Editar Paciente</h2>

    <form action="atualizar.php" method="POST" class="editar-paciente-form">
        <input type="hidden" name="id" value="<?= $paciente['id'] ?>">

        <label class="editar-paciente-label">Nome *</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($paciente['nome']) ?>" class="editar-paciente-input" required>

        <label class="editar-paciente-label">CPF</label>
        <input type="text" name="cpf" value="<?= $paciente['cpf'] ?>" class="editar-paciente-input">

        <label class="editar-paciente-label">Endereço</label>
        <input type="text" name="endereco" value="<?= $paciente['endereco'] ?>" class="editar-paciente-input">

        <label class="editar-paciente-label">Telefone</label>
        <input type="text" name="telefone" value="<?= $paciente['telefone'] ?>" class="editar-paciente-input">

        <label class="editar-paciente-label">E-mail</label>
        <input type="email" name="email" value="<?= $paciente['email'] ?>" class="editar-paciente-input">

        <label class="editar-paciente-label">Data de Nascimento</label>
        <input type="date" name="data_nascimento" value="<?= $paciente['data_nascimento'] ?>" class="editar-paciente-input">

        <label class="editar-paciente-label">Status</label>
        <select name="ativo" class="editar-paciente-select">
            <option value="1" <?= $paciente['ativo'] ? 'selected' : '' ?>>Ativo</option>
            <option value="0" <?= !$paciente['ativo'] ? 'selected' : '' ?>>Inativo</option>
        </select>

        <div class="editar-paciente-actions">
            <button type="submit" class="editar-paciente-btn">Atualizar</button>
            <a href="listar.php" class="editar-paciente-voltar">Voltar</a>
        </div>
    </form>
</div>

</body>
</html>
