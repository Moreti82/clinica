<?php
require_once '../config/conexao.php';

$stmt = $db->query("SELECT * FROM pacientes");
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Pacientes - Clínica</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="container">
        <h2>Pacientes</h2>

        <a href="novo.php" class="btn">Novo Paciente</a>

        <table>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>

            <?php foreach ($pacientes as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= $p['telefone'] ?></td>
                    <td class="<?= $p['ativo'] ? 'status-ativo' : 'status-inativo' ?>">
                        <?= $p['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </td>
                    <td>
                        <a href="editar.php?id=<?= $p['id'] ?>" class="btn-editar">Editar</a>
                        |
                        <a href="excluir.php?id=<?= $p['id'] ?>" class="btn-excluir" onclick="return confirmarExclusao()">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script src="../assets/js/pacientes.js"></script>

</body>

</html>
