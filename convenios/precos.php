<?php
$page_title = 'Preços do Convênio';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$convenio_id = $_GET['id'] ?? null;

if (!$convenio_id) {
    redirecionar('index.php', 'Convênio não especificado', 'danger');
}

// Buscar dados do convênio
$stmt = $db->prepare("SELECT * FROM convenios WHERE id = ?");
$stmt->execute([$convenio_id]);
$convenio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$convenio) {
    redirecionar('index.php', 'Convênio não encontrado', 'danger');
}

// Buscar todos os procedimentos
$procedimentos = $db->query("SELECT * FROM procedimentos ORDER BY descricao")->fetchAll(PDO::FETCH_ASSOC);

// Buscar preços já cadastrados para este convênio
$stmt = $db->prepare("
    SELECT pc.*, p.descricao as proc_descricao, p.valor_padrao
    FROM procedimentos_convenio pc
    JOIN procedimentos p ON pc.procedimento_id = p.id
    WHERE pc.convenio_id = ?
");
$stmt->execute([$convenio_id]);
$precosCadastrados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar por procedimento_id
$precos = [];
foreach ($precosCadastrados as $p) {
    $precos[$p['procedimento_id']] = $p;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        // Limpar preços antigos
        $stmt = $db->prepare("DELETE FROM procedimentos_convenio WHERE convenio_id = ?");
        $stmt->execute([$convenio_id]);
        
        // Inserir novos preços
        $stmt = $db->prepare("INSERT INTO procedimentos_convenio (convenio_id, procedimento_id, valor, desconto_percentual, observacoes) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($_POST['precos'] as $proc_id => $dados) {
            $valorStr = $dados['valor'] ?? '';
            $descPercStr = $dados['desconto_percentual'] ?? '';
            
            if (!empty($valorStr) || !empty($descPercStr)) {
                $valor = parseDinheiro($valorStr);
                $descPercStr = str_replace(',', '.', $descPercStr);
                $descPerc = floatval($descPercStr);
                $obs = $dados['observacao'] ?? '';
                
                $stmt->execute([$convenio_id, $proc_id, $valor, $descPerc, $obs]);
            }
        }
        
        $db->commit();
        redirecionar("precos.php?id=$convenio_id", 'Preços atualizados!');
    } catch (Exception $e) {
        $db->rollBack();
        $erro = 'Erro: ' . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Preços - <?php echo htmlspecialchars($convenio['nome']); ?></h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / <a href="index.php">Convênios</a> / Preços</div>
</div>

<?php if (isset($erro)): ?><div class="alert alert-danger"><?php echo $erro; ?></div><?php endif; ?>

<form method="POST">
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Procedimento</th>
                        <th>Valor Padrão</th>
                        <th style="width: 150px;">Desconto (%)</th>
                        <th style="width: 200px;">Valor Convênio *</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($procedimentos as $proc): 
                        $precoAtual = $precos[$proc['id']]['valor'] ?? '';
                        $descPercAtual = $precos[$proc['id']]['desconto_percentual'] ?? '';
                        $obsAtual = $precos[$proc['id']]['observacoes'] ?? '';
                        
                        // Aplica o desconto padrão automaticamente se ainda não houver nenhum registro configurado
                        if ($precoAtual === '' && $descPercAtual === '') {
                            $descPercAtual = floatval($convenio['desconto_padrao']);
                            if ($descPercAtual > 0) {
                                $precoAtual = $proc['valor_padrao'] - ($proc['valor_padrao'] * ($descPercAtual / 100));
                            } else {
                                $precoAtual = $proc['valor_padrao'];
                                $descPercAtual = '';
                            }
                        }
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($proc['descricao']); ?></td>
                            <td class="valor-padrao-td" data-valor="<?php echo $proc['valor_padrao']; ?>"><?php echo formatarDinheiro($proc['valor_padrao']); ?></td>
                            <td>
                                <input type="number" name="precos[<?php echo $proc['id']; ?>][desconto_percentual]" class="form-control desconto-perc" 
                                       value="<?php echo $descPercAtual !== '' ? $descPercAtual : ''; ?>" placeholder="0" min="0" max="100" step="0.01">
                            </td>
                            <td>
                                <input type="text" name="precos[<?php echo $proc['id']; ?>][valor]" class="form-control valor-conv" 
                                       value="<?php echo $precoAtual !== '' ? formatarDinheiro($precoAtual) : ''; ?>" placeholder="R$ 0,00">
                            </td>
                            <td>
                                <input type="text" name="precos[<?php echo $proc['id']; ?>][observacao]" class="form-control" 
                                       value="<?php echo htmlspecialchars($obsAtual); ?>" placeholder="Ex: Por dente, etc.">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
        <a href="index.php" class="btn btn-secondary">Voltar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Salvar Preços</button>
    </div>
</form>

<script>
document.querySelectorAll('input.valor-conv').forEach(function(input) {
    input.addEventListener('blur', function(e) {
        let valorStr = e.target.value.replace(/[^\d]/g, '');
        if (valorStr !== '') {
            let valor = parseInt(valorStr) / 100;
            e.target.value = valor.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
            
            // Recalcula a porcentagem de desconto baseada no valor fixo editado
            let tr = e.target.closest('tr');
            let valorPadrao = parseFloat(tr.querySelector('.valor-padrao-td').dataset.valor);
            let percInput = tr.querySelector('.desconto-perc');
            if (valorPadrao > 0 && percInput) {
                let calcPerc = ((valorPadrao - valor) / valorPadrao) * 100;
                if(calcPerc < 0) calcPerc = 0;
                percInput.value = calcPerc.toFixed(2);
            }
        }
    });
});

document.querySelectorAll('input.desconto-perc').forEach(function(input) {
    input.addEventListener('input', function(e) {
        let perc = parseFloat(e.target.value) || 0;
        let tr = e.target.closest('tr');
        let valorPadrao = parseFloat(tr.querySelector('.valor-padrao-td').dataset.valor);
        let valorConvInput = tr.querySelector('.valor-conv');
        if (valorPadrao > 0 && valorConvInput) {
            let novoValor = valorPadrao - (valorPadrao * (perc / 100));
            if (novoValor < 0) novoValor = 0;
            valorConvInput.value = novoValor.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
