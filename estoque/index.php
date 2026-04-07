<?php
$page_title = 'Estoque';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$filtro_categoria = $_GET['categoria'] ?? 'todos';
$filtro_alerta = isset($_GET['alerta']);

$sql = "SELECT * FROM produtos WHERE ativo = 1";
$params = [];

if ($filtro_categoria !== 'todos') {
    $sql .= " AND categoria = ?";
    $params[] = $filtro_categoria;
}

$sql .= " ORDER BY descricao";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($filtro_alerta) {
    $produtos = array_filter($produtos, fn($p) => $p['quantidade_atual'] <= $p['quantidade_minima']);
}

$categorias = $db->query("SELECT DISTINCT categoria FROM produtos WHERE categoria IS NOT NULL ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
$totalAlertas = count(array_filter($produtos, fn($p) => $p['quantidade_atual'] <= $p['quantidade_minima']));

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Controle de Estoque</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / Estoque</div>
</div>

<?php exibirFlashMessage(); ?>

<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px;">
    <div class="card" style="border-left: 4px solid #17a2b8;">
        <div style="font-size: 0.9rem; color: #666;">Total de Produtos</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #17a2b8;"><?php echo count($produtos); ?></div>
    </div>
    
    <div class="card" style="border-left: 4px solid #dc3545;">
        <div style="font-size: 0.9rem; color: #666;">Alertas</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #dc3545;"><?php echo $totalAlertas; ?></div>
    </div>
</div>

<div class="card" style="margin-bottom: 25px;">
    <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="margin: 0;">
            <label>Categoria</label>
            <select name="categoria" class="form-control" onchange="this.form.submit()">
                <option value="todos">Todas</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $filtro_categoria === $cat ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group" style="margin: 0;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="alerta" value="1" <?php echo $filtro_alerta ? 'checked' : ''; ?> onchange="this.form.submit()">
                <span>Apenas alertas</span>
            </label>
        </div>
        
        <a href="novo.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo</a>
        <a href="movimentacao.php" class="btn btn-primary"><i class="fa-solid fa-exchange-alt"></i> Movimentar</a>
    </form>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Qtd</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $prod): 
                    $alerta = $prod['quantidade_atual'] <= $prod['quantidade_minima'];
                ?>
                    <tr <?php echo $alerta ? 'style="background: #fff3cd;"' : ''; ?>>
                        <td><?php echo htmlspecialchars($prod['codigo'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($prod['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($prod['categoria'] ?? '-'); ?></td>
                        <td style="font-weight: 600;"><?php echo $prod['quantidade_atual'] . ' ' . $prod['unidade']; ?></td>
                        <td>
                            <?php if ($alerta): ?>
                                <span class="badge badge-danger"><i class="fa-solid fa-exclamation-triangle"></i> Baixo</span>
                            <?php else: ?>
                                <span class="badge badge-success">OK</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
