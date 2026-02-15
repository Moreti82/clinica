<?php
require_once '../config/conexao.php';

// Pega o ID do profissional da URL
$profissionalId = $_GET['id'] ?? null;

if (!$profissionalId) {
    header("Location: profissionais.php"); 
    exit;
}

// Busca os dados do profissional para editar
$stmt = $db->prepare("SELECT * FROM profissionais WHERE id = :id");
$stmt->execute([':id' => $profissionalId]);
$profissional = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profissional) {
    header("Location: profissionais.php");
    exit;
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome          = $_POST['nome'] ?? '';
    $cro           = $_POST['cro'] ?? '';
    $especialidade = $_POST['especialidade'] ?? '';
    $telefone      = $_POST['telefone'] ?? '';
    $ativo         = $_POST['ativo'] ?? 1;

    if (!$nome || !$cro) {
        $erro = "Nome e CRO são obrigatórios.";
    } else {
        $updateSql = "
            UPDATE profissionais
            SET nome = :nome, cro = :cro, especialidade = :especialidade, telefone = :telefone, ativo = :ativo
            WHERE id = :id
        ";

        $stmt = $db->prepare($updateSql);
        $stmt->execute([
            ':nome' => $nome,
            ':cro' => $cro,
            ':especialidade' => $especialidade,
            ':telefone' => $telefone,
            ':ativo' => $ativo,
            ':id' => $profissionalId
        ]);

        // Redireciona para a lista de profissionais com parâmetro de sucesso
        header("Location: profissionais.php?sucesso=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Profissional</title>
    <link rel="stylesheet" href="../assets/css/style-novo-profissional.css">
</head>
<body>

<div class="container">

    <h2>Editar Profissional</h2>

    <?php if (isset($erro)): ?>
        <div style="color:red;"><?= $erro ?></div>
    <?php endif; ?>

    <form action="" method="POST">

        <div>
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($profissional['nome']) ?>" required>
        </div>

        <div>
            <label>CRO:</label>
            <input type="text" name="cro" value="<?= htmlspecialchars($profissional['cro']) ?>" required>
        </div>

        <div>
            <label>Especialidade:</label>
            <input type="text" name="especialidade" value="<?= htmlspecialchars($profissional['especialidade']) ?>">
        </div>

        <div>
            <label>Telefone:</label>
            <input type="text" name="telefone" value="<?= htmlspecialchars($profissional['telefone']) ?>">
        </div>

        <div>
            <label>Ativo:</label>
            <select name="ativo">
                <option value="1" <?= $profissional['ativo'] == 1 ? 'selected' : '' ?>>Sim</option>
                <option value="0" <?= $profissional['ativo'] == 0 ? 'selected' : '' ?>>Não</option>
            </select>
        </div>

        <br>

        <button type="submit">Salvar</button>
        <a href="profissionais.php" class="btn-voltar">Voltar</a>

    </form>

</div>

</body>
</html>
