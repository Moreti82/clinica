<?php
$page_title = 'Caixa';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

// Data do caixa (padrão: hoje)
$data_caixa = $_GET['data'] ?? date('Y-m-d');

// Buscar movimentações do dia
$stmt = $db->prepare("
    SELECT c.*, fp.descricao as forma_pagamento, u.nome as usuario_nome
    FROM caixa c
    LEFT JOIN formas_pagamento fp ON c.forma_pagamento_id = fp.id
    LEFT JOIN users u ON c.usuario_id = u.id
    WHERE c.data_movimento = ?
    ORDER BY c.id ASC
");
$stmt->execute([$data_caixa]);
$movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular totais
$entradas = array_sum(array_column(array_filter($movimentacoes, fn($m) => $m['tipo'] === 'entrada'), 'valor'));
$saidas = array_sum(array_column(array_filter($movimentacoes, fn($m) => $m['tipo'] === 'saida'), 'valor'));
$saldo = $entradas - $saidas;

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Controle de Caixa</h1>
    <div class="breadcrumb">
        <a href="../dashboard/index.php">Início</a> / Financeiro / Caixa
    </div>
</div>

<?php exibirFlashMessage(); ?>

<!-- Resumo do Dia -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
    <div class="card" style="border-left: 4px solid #28a745;">
        <div style="font-size: 0.9rem; color: #666;">Entradas</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #28a745;"><?php echo formatarDinheiro($entradas); ?></div>
    </div>
    
    <div class="card" style="border-left: 4px solid #dc3545;">
        <div style="font-size: 0.9rem; color: #666;">Saídas</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #dc3545;"><?php echo formatarDinheiro($saidas); ?></div>
    </div>
    
    <div class="card" style="border-left: 4px solid #17a2b8;">
        <div style="font-size: 0.9rem; color: #666;">Saldo do Dia</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #17a2b8;"><?php echo formatarDinheiro($saldo); ?></div>
    </div>
</div>

<!-- Filtro de Data -->
<div class="card" style="margin-bottom: 25px;">
    <form method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
        <div class="form-group" style="margin: 0;">
            <label>Data</label>
            <input type="date" name="data" class="form-control" value="<?php echo $data_caixa; ?>">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Buscar</button>
        <a href="nova_movimentacao.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Nova Movimentação</a>
    </form>
</div>

<!-- Movimentações -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Movimentações do Dia - <?php echo formatarData($data_caixa); ?></div>
    </div>
    
    <?php if (empty($movimentacoes)): ?>
        <div style="text-align: center; padding: 40px; color: #999;">
            <i class="fa-solid fa-inbox" style="font-size: 3rem; margin-bottom: 15px;"></i>
            <p>Nenhuma movimentação nesta data</p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Forma Pagamento</th>
                        <th>Valor</th>
                        <th>Usuário</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimentacoes as $mov): ?>
                        <tr>
                            <td>
                                <?php if ($mov['tipo'] === 'entrada'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-arrow-up"></i> Entrada</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-arrow-down"></i> Saída</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($mov['descricao']); ?></td>
                            <td><?php echo htmlspecialchars($mov['categoria'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($mov['forma_pagamento'] ?? '-'); ?></td>
                            <td style="font-weight: 600; color: <?php echo $mov['tipo'] === 'entrada' ? '#28a745' : '#dc3545'; ?>">
                                <?php echo ($mov['tipo'] === 'entrada' ? '+' : '-') . ' ' . formatarDinheiro($mov['valor']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($mov['usuario_nome'] ?? 'Sistema'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
