<?php
require_once __DIR__ . '/../config/conexao.php';

$sql = "SELECT * FROM procedimentos ORDER BY id DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$procedimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Procedimentos</title>
    <link rel="stylesheet" href="../assets/css/style-procedimentos.css">
</head>
<body>

<div class="container">
    <h1>Procedimentos Cadastrados</h1>

    <a href="novo.php" class="btn">Cadastrar Novo</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Valor Padrão</th>
                <th>Observações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($procedimentos as $p): ?>
                <tr>
                    <td data-label="ID"><?= $p['id'] ?></td>
                    <td data-label="Descrição"><?= htmlspecialchars($p['descricao']) ?></td>
                    <td data-label="Valor Padrão">R$ <?= number_format($p['valor_padrao'], 2, ',', '.') ?></td>
                    <td data-label="Observações"><?= htmlspecialchars($p['observacoes'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
