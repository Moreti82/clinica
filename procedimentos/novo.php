<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Procedimento</title>
    <link rel="stylesheet" href="../assets/css/style-novo-procedimento.css">
</head>
<body id="procedimento-body">
    <div id="procedimento-container">
        <h2 id="procedimento-title">
            <i class="fas fa-tooth"></i> Cadastrar Procedimento
        </h2>

        <form id="procedimento-form" method="post" action="salvar.php">
            <!-- Descrição -->
            <div class="procedimento-group">
                <label for="descricao">Descrição</label>
                <input type="text" id="descricao" name="descricao" required>
            </div>

            <!-- Valor Padrão -->
            <div class="procedimento-group">
                <label for="valor_padrao">Valor Padrão (R$)</label>
                <input type="number" step="0.01" id="valor_padrao" name="valor_padrao" required>
            </div>

            <!-- Observações -->
            <div class="procedimento-group observacoes">
                <label for="observacoes">Observações</label>
                <textarea id="observacoes" name="observacoes" rows="3"></textarea>
            </div>

            <!-- Botões -->
            <div id="procedimento-actions">
                <button type="submit" class="procedimento-btn salvar-btn">
                    <i class="fas fa-save"></i> Salvar
                </button>
                <a href="procedimentos.php" class="procedimento-btn cancelar-btn">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Ícones Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
