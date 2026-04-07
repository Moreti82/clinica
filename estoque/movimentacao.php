<?php
$page_title = 'Movimentação de Estoque';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$produtos = $db->query("SELECT id, descricao, quantidade_atual FROM produtos WHERE ativo = 1 ORDER BY descricao")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id = $_POST['produto_id'];
    $tipo = $_POST['tipo'];
    $quantidade = floatval($_POST['quantidade']);
    $motivo = $_POST['motivo'] ?? '';
    
    try {
        $db->beginTransaction();
        
        // Registrar movimentação
        $stmt = $db->prepare("
            INSERT INTO estoque_movimentacao (produto_id, tipo, quantidade, motivo, usuario_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$produto_id, $tipo, $quantidade, $motivo, $_SESSION['user_id'] ?? null]);
        
        // Atualizar quantidade do produto
        if ($tipo === 'entrada') {
            $stmt = $db->prepare("UPDATE produtos SET quantidade_atual = quantidade_atual + ? WHERE id = ?");
        } else {
            $stmt = $db->prepare("UPDATE produtos SET quantidade_atual = quantidade_atual - ? WHERE id = ?");
        }
        $stmt->execute([$quantidade, $produto_id]);
        
        $db->commit();
        
        redirecionar('index.php', 'Movimentação registrada!');
    } catch (Exception $e) {
        $db->rollBack();
        $erro = 'Erro: ' . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Movimentação de Estoque</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / <a href="index.php">Estoque</a> / Movimentação</div>
</div>

<?php if (isset($erro)): ?><div class="alert alert-danger"><?php echo $erro; ?></div><?php endif; ?>

<div class="card" style="max-width: 500px;">
    <form method="POST">
        <div class="form-group">
            <label>Produto *</label>
            <select name="produto_id" class="form-control" required>
                <option value="">Selecione</option>
                <?php foreach ($produtos as $p): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['descricao']) . ' (Qtd: ' . $p['quantidade_atual'] . ')'; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Tipo *</label>
            <select name="tipo" class="form-control" required>
                <option value="entrada">Entrada (+)</option>
                <option value="saida">Saída (-)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Quantidade *</label>
            <input type="number" name="quantidade" class="form-control" step="0.01" min="0.01" required>
        </div>
        
        <div class="form-group">
            <label>Motivo/Observação</label>
            <textarea name="motivo" class="form-control" rows="2" placeholder="Ex: Compra de material, Uso em procedimento..."></textarea>
        </div>
        
        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Registrar</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
