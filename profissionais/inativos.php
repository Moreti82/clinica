<?php
require_once '../config/conexao.php';

// Buscar apenas profissionais inativos
$profissionais = $db->query("SELECT id, nome, cro, especialidade, telefone, ativo 
                             FROM profissionais 
                             WHERE ativo = 0 
                             ORDER BY nome")
                    ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Profissionais Inativos</title>
    <link rel="stylesheet" href="../assets/css/style-profissionais.css">
    <link rel="stylesheet" href="../assets/css/style-inativos.css">
</head>
<body id="profissionais-page">

<div class="profissionais-container">

    <h2 class="profissionais-title">Profissionais Inativos</h2>

    <?php if (isset($_GET['sucesso'])): ?>
    <div class="mensagem-sucesso">
        Profissional reativado com sucesso!
    </div>
    <script>
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('sucesso');
            window.history.replaceState({}, document.title, url);
        }
    </script>
<?php endif; ?>


    <div class="profissionais-menu">
        <a href="profissionais.php" class="profissionais-btn-sec">⬅ Voltar para Ativos</a>
    </div>

    <div class="profissionais-table-wrapper">
        <table class="profissionais-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CRO</th>
                    <th>Especialidade</th>
                    <th>Telefone</th>
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
                    <td>
                        <form action="reativar.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $prof['id'] ?>">
                            <button type="submit" class="btn-reativar">Reativar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
