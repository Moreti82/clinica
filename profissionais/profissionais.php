<?php
require_once '../config/conexao.php';

// Buscar profissionais
$profissionais = $db->query("SELECT id, nome, cro, especialidade, telefone, ativo FROM profissionais WHERE ativo = 1 ORDER BY nome")
                    ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Profissionais</title>
    <link rel="stylesheet" href="../assets/css/style-profissionais.css">
    
</head>
<body id="profissionais-page">

<div class="profissionais-container">

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="mensagem-sucesso">
            Profissional atualizado com sucesso!
        </div>
        <script>
            // Remove o parâmetro "sucesso" da URL após exibir a mensagem
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('sucesso');
                window.history.replaceState({}, document.title, url);
            }
        </script>
    <?php endif; ?>

    <h2 class="profissionais-title">Profissionais Cadastrados</h2>

    <div class="profissionais-menu">
        <a href="novo.php" class="profissionais-btn">➕ Novo Profissional</a>
        <a href="../index.php" class="profissionais-btn-sec">⬅ Voltar</a>
        <a href="inativos.php" class="profissionais-btn-sec">👥 Profissionais Inativos</a>
    </div>


    <div class="profissionais-table-wrapper">
        <table class="profissionais-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CRO</th>
                    <th>Especialidade</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profissionais as $prof): ?>
                <tr>
                    <td><?= htmlspecialchars($prof['nome']) ?></td>
                    <td><?= htmlspecialchars($prof['cro']) ?></td>
                    <td><?= htmlspecialchars($prof['especialidade']) ?></td>
                    <td><?= htmlspecialchars($prof['telefone']) ?></td>
                    <td class="<?= $prof['ativo'] ? 'status-ativo' : 'status-inativo' ?>">
                        <?= $prof['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </td>
                    <td>
                        <a href="editar.php?id=<?= $prof['id'] ?>">Editar</a>
                        |
                        <a href="excluir.php?id=<?= $prof['id'] ?>" onclick="return confirm('Deseja excluir este profissional?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
