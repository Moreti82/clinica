<?php
require_once '../auth/verifica_admin.php';
require_once '../config/conexao.php';

$perfis = $db->query("SELECT * FROM perfis")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Novo Usuário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style-novo-usuario.css">
</head>

<body id="usuario-novo-page"><!-- ID ÚNICO DA PÁGINA -->

<div class="usuario-novo-container"><!-- CONTAINER EXCLUSIVO -->

    <h2 class="usuario-novo-title">Cadastrar Usuário</h2>

    <form action="salvar.php" method="POST" class="usuario-novo-form">

        <div class="usuario-novo-field">
            <label>Nome</label>
            <input type="text" name="nome" required>
        </div>

        <div class="usuario-novo-field">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="usuario-novo-field">

            <label>Senha</label>

            <div class="password-container">
                <input type="password" name="senha" id="senha" required>
                <i class="fa-solid fa-eye" id="toggleSenha"></i>
            </div>

        </div>

        
        <div class="usuario-novo-field">

            <label>Confirmar Senha</label>

            <div class="password-container">
                <input type="password" name="conf-senha" id="conf-senha" required>
                <i class="fa-solid fa-eye" id="toggleConfSenha"></i>
            </div>

            <label id="confirmar-senha"></label>

        </div>

        <div class="usuario-novo-field">
            <label>Perfil</label>
            
            <select name="perfil_id" required>
                <option value="">Selecione...</option>
                <?php foreach ($perfis as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['perfil']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="usuario-novo-actions">
            <button id="salvar" disabled type="submit" class="btn usuario-novo-btn">💾 Salvar</button>
            <a href="listar.php" class="btn usuario-novo-btn-sec">⬅ Voltar</a>
        </div>

    </form>

</div>]

    <script src="../assets/js/novo-usuario.js"></script>

</body>
</html>
