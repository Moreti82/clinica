<?php
$page_title = 'Editar Procedimento';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    redirecionar('procedimentos.php', 'Procedimento não especificado', 'danger');
}

// Buscar procedimento
$stmt = $db->prepare("SELECT * FROM procedimentos WHERE id = ?");
$stmt->execute([$id]);
$procedimento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$procedimento) {
    redirecionar('procedimentos.php', 'Procedimento não encontrado', 'danger');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'descricao' => $_POST['descricao'],
        'valor_padrao' => parseDinheiro($_POST['valor_padrao']),
        'observacoes' => $_POST['observacoes'] ?? ''
    ];
    
    try {
        $sql = "UPDATE procedimentos SET descricao = :descricao, valor_padrao = :valor_padrao, observacoes = :observacoes WHERE id = :id";
        $stmt = $db->prepare($sql);
        $dados['id'] = $id;
        $stmt->execute($dados);
        
        redirecionar('procedimentos.php', 'Procedimento atualizado!');
    } catch (Exception $e) {
        $erro = 'Erro: ' . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Editar Procedimento</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / <a href="procedimentos.php">Procedimentos</a> / Editar</div>
</div>

<?php if (isset($erro)): ?><div class="alert alert-danger"><?php echo $erro; ?></div><?php endif; ?>

<div class="card" style="max-width: 600px;">
    <form method="POST">
        <div class="form-group">
            <label>Descrição *</label>
            <input type="text" name="descricao" class="form-control" required value="<?php echo htmlspecialchars($procedimento['descricao']); ?>">
        </div>
        
        <div class="form-group">
            <label>Valor Padrão *</label>
            <input type="text" name="valor_padrao" class="form-control" required id="valor" value="<?php echo formatarDinheiro($procedimento['valor_padrao']); ?>">
        </div>
        
        <div class="form-group">
            <label>Observações</label>
            <textarea name="observacoes" class="form-control" rows="3"><?php echo htmlspecialchars($procedimento['observacoes'] ?? ''); ?></textarea>
        </div>
        
        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
            <a href="procedimentos.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Salvar</button>
        </div>
    </form>
</div>

<script>
document.getElementById('valor').addEventListener('blur', function(e) {
    let valor = e.target.value.replace(/[^\d]/g, '');
    if (valor) {
        valor = (parseInt(valor) / 100).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
        e.target.value = valor;
    }
});
</script>

<?php include '../includes/footer.php'; ?>
