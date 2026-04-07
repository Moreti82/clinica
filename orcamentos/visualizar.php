<?php
$page_title = 'Visualizar Orçamento';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    redirecionar('listar.php', 'Orçamento não especificado', 'danger');
}

// Buscar orçamento
$stmt = $db->prepare("
    SELECT o.*, p.nome as paciente_nome, p.cpf, p.telefone, p.endereco,
           pr.nome as profissional_nome, pr.cro
    FROM orcamentos o
    JOIN pacientes p ON o.paciente_id = p.id
    JOIN profissionais pr ON o.profissional_id = pr.id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$orcamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orcamento) {
    redirecionar('listar.php', 'Orçamento não encontrado', 'danger');
}

// Buscar itens
$stmt = $db->prepare("
    SELECT oi.*, proc.descricao as procedimento_nome
    FROM orcamento_itens oi
    JOIN procedimentos proc ON oi.procedimento_id = proc.id
    WHERE oi.orcamento_id = ?
");
$stmt->execute([$id]);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Data de validade
$dataValidade = date('Y-m-d', strtotime($orcamento['data_orcamento'] . ' + ' . $orcamento['validade_dias'] . ' days'));
$expirado = strtotime($dataValidade) < strtotime('today');

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Orçamento #<?php echo $id; ?></h1>
    <div class="breadcrumb">
        <a href="../dashboard/index.php">Início</a> / 
        <a href="listar.php">Orçamentos</a> / Visualizar
    </div>
</div>

<?php exibirFlashMessage(); ?>

<div style="display: flex; gap: 15px; margin-bottom: 25px;">
    <a href="listar.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    
    <button onclick="window.print()" class="btn btn-primary"><i class="fa-solid fa-print"></i> Imprimir</button>
    
    <?php if ($orcamento['status'] === 'Pendente'): ?>
        <a href="aprovar.php?id=<?php echo $id; ?>&acao=aprovado" class="btn btn-success"><i class="fa-solid fa-check"></i> Aprovar</a>
        
        <a href="aprovar.php?id=<?php echo $id; ?>&acao=recusado" class="btn btn-danger"><i class="fa-solid fa-times"></i> Recusar</a>
    
    <?php endif; ?>
</div>

<!-- Orçamento para Impressão -->
<div class="card" id="orcamento-print">
    
    <!-- Cabeçalho -->
    <div style="text-align: center; border-bottom: 2px solid #667eea; padding-bottom: 20px; margin-bottom: 30px;">
        <h1 style="color: #667eea; margin-bottom: 10px;">OdontoCare</h1>
        <p style="color: #666;">Orçamento Odontológico</p>
    </div>
    
    
    <!-- Dados -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
        
        <div>
            <h3 style="color: #667eea; margin-bottom: 15px;">Paciente</h3>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($orcamento['paciente_nome']); ?></p>
            <p><strong>CPF:</strong> <?php echo mascararCPF($orcamento['cpf']); ?></p>
            <p><strong>Telefone:</strong> <?php echo mascararTelefone($orcamento['telefone']); ?></p>
        </div>
        
        
        <div>
            <h3 style="color: #667eea; margin-bottom: 15px;">Orçamento</h3>
            <p><strong>Nº:</strong> #<?php echo $id; ?></p>
            <p><strong>Data:</strong> <?php echo formatarData($orcamento['data_orcamento']); ?></p>
            <p><strong>Validade:</strong> <?php echo formatarData($dataValidade); ?>
                <?php if ($expirado && $orcamento['status'] === 'Pendente'): ?>
                    <span class="badge badge-danger">Expirado</span>
                <?php endif; ?>
            </p>
            <p><strong>Status:</strong> <?php echo statusBadge($orcamento['status']); ?></p>
        </div>
    
    </div>
    
    
    <!-- Profissional -->
    <p style="margin-bottom: 20px;"><strong>Profissional Responsável:</strong> 
        Dr(a). <?php echo htmlspecialchars($orcamento['profissional_nome']); ?> - CRO: <?php echo htmlspecialchars($orcamento['cro']); ?>
    </p>
    
    
    <!-- Itens -->
    <table style="width: 100%; margin-bottom: 30px;">
        <thead>
            <tr style="background: #667eea; color: white;">
                <th style="padding: 12px; text-align: left;">Procedimento</th>
                <th style="padding: 12px; text-align: center;">Dente</th>
                <th style="padding: 12px; text-align: center;">Qtd</th>
                <th style="padding: 12px; text-align: right;">Valor Unit.</th>
                <th style="padding: 12px; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itens as $item): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;"><?php echo htmlspecialchars($item['procedimento_nome']); ?></td>
                    <td style="padding: 12px; text-align: center;"><?php echo $item['dente'] ?: '-'; ?></td>
                    <td style="padding: 12px; text-align: center;"><?php echo $item['quantidade']; ?></td>
                    <td style="padding: 12px; text-align: right;"><?php echo formatarDinheiro($item['valor_unitario']); ?></td>
                    <td style="padding: 12px; text-align: right; font-weight: 600;"><?php echo formatarDinheiro($item['valor_total']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    
    <!-- Totais -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span>Subtotal:</span>
            <span><?php echo formatarDinheiro($orcamento['valor_total']); ?></span>
        </div>
        
        <?php if ($orcamento['desconto'] > 0): ?>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #28a745;">
                <span>Desconto:</span>
                <span>-<?php echo formatarDinheiro($orcamento['desconto']); ?></span>
            </div>
        <?php endif; ?>
        
        <div style="display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: 700; color: #667eea; border-top: 2px solid #667eea; padding-top: 10px;">
            <span>TOTAL:</span>
            <span><?php echo formatarDinheiro($orcamento['valor_final']); ?></span>
        </div>
    </div>
    
    <?php if ($orcamento['observacoes']): ?>
        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 8px;">
            <strong>Observações:</strong><br>
            <?php echo nl2br(htmlspecialchars($orcamento['observacoes'])); ?>
        </div>
    <?php endif; ?>
    
    
    <!-- Assinaturas -->
    <div style="margin-top: 60px; display: grid; grid-template-columns: 1fr 1fr; gap: 50px;">
        
        <div style="text-align: center;">
            <div style="border-top: 1px solid #333; padding-top: 10px;">
                Assinatura do Paciente
            </div>
        </div>
        
        
        <div style="text-align: center;">
            <div style="border-top: 1px solid #333; padding-top: 10px;">
                Assinatura do Profissional
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
