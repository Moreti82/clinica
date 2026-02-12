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
    <link rel="stylesheet" href="../assets/css/style-listar-pacientes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body id="pacientes-body">

    <div id="pacientes-container">
        <h2 id="pacientes-title">Pacientes</h2>

        <a href="novo.php" class="pacientes-btn pacientes-btn-novo">
            <i class="fa-solid fa-user-plus"></i> Novo Paciente
        </a>

        <a href="../index.php" class="pacientes-btn pacientes-btn-voltar">
            <i class="fa-solid fa-arrow-left"></i>Voltar
        </a>

        <table id="pacientes-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                    <th>CPF</th>
                    <th>Endereço</th>
                    <th>Observações</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pacientes as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nome']) ?></td>
                        <td><?= $p['telefone'] ?></td>
                        <td><?= $p['email'] ?></td>
                        <td><?= $p['cpf'] ?></td>
                        <td><?= $p['endereco'] ?></td>
                        <td><?= $p['observacoes'] ?></td>
                        <td class="<?= $p['ativo'] ? 'paciente-status-ativo' : 'paciente-status-inativo' ?>">
                            <?= $p['ativo'] ? 'Ativo' : 'Inativo' ?>
                        </td>
                        <td class="pacientes-acoes">
                            <a href="editar.php?id=<?= $p['id'] ?>" class="pacientes-btn-editar">
                                <i class="fa-solid fa-pen-to-square"></i> Editar
                            </a>
                            |
                            <a href="excluir.php?id=<?= $p['id'] ?>" class="pacientes-btn-excluir" onclick="return confirmarExclusao()">
                                <i class="fa-solid fa-trash"></i> Excluir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="../assets/js/listar-pacientes.js"></script>

</body>
</html>
