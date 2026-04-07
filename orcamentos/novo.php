<?php
$page_title = 'Novo Orçamento';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$pacientes = $db->query("SELECT id, nome FROM pacientes WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$profissionais = $db->query("SELECT id, nome FROM profissionais WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$procedimentos = $db->query("SELECT id, descricao, valor_padrao FROM procedimentos ORDER BY descricao")->fetchAll(PDO::FETCH_ASSOC);

// Buscar Convênios e Preços
$convenios = $db->query("SELECT id, nome, desconto_padrao FROM convenios WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$precos_conv_db = $db->query("SELECT convenio_id, procedimento_id, valor FROM procedimentos_convenio")->fetchAll(PDO::FETCH_ASSOC);
$precos_convenio = [];
foreach ($precos_conv_db as $pc) {
    if (!isset($precos_convenio[$pc['convenio_id']])) {
        $precos_convenio[$pc['convenio_id']] = [];
    }
    $precos_convenio[$pc['convenio_id']][$pc['procedimento_id']] = $pc['valor'];
}

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Novo Orçamento</h1>
    <div class="breadcrumb">
        <a href="../dashboard/index.php">Início</a> / 
        <a href="listar.php">Orçamentos</a> / Novo
    </div>
</div>

<form action="salvar.php" method="POST" id="formOrcamento">
    
    <!-- Dados Principais -->
    <div class="card">
        <div class="card-header"><div class="card-title">Dados do Orçamento</div></div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            
            <div class="form-group">
                <label>Paciente *</label>
                <select name="paciente_id" class="form-control" required>
                    <option value="">Selecione</option>
                    <?php foreach ($pacientes as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Convênio de Preços (Opcional)</label>
                <select name="convenio_id" id="convenioSelect" class="form-control" onchange="atualizarPrecosConvenio()">
                    <option value="">Particular / Tabela Padrão</option>
                    <?php foreach ($convenios as $c): ?>
                        <option value="<?php echo $c['id']; ?>" data-desconto="<?php echo $c['desconto_padrao']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Profissional *</label>
                <select name="profissional_id" class="form-control" required>
                    <option value="">Selecione</option>
                    <?php foreach ($profissionais as $pr): ?>
                        <option value="<?php echo $pr['id']; ?>"><?php echo htmlspecialchars($pr['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Validade (dias)</label>
                <input type="number" name="validade_dias" class="form-control" value="30" min="1">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Observações</label>
                <textarea name="observacoes" class="form-control" rows="2"></textarea>
            </div>
        
        </div>
    </div>
    
    <!-- Procedimentos -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Procedimentos</div>
        </div>
        
        <div id="itensOrcamento">
            <!-- Itens serão adicionados aqui -->
        </div>
        
        <button type="button" class="btn btn-primary" onclick="adicionarItem()" style="margin-top: 15px;">
            <i class="fa-solid fa-plus"></i> Adicionar Procedimento
        </button>
    </div>
    
    <!-- Totais -->
    <div class="card" style="background: #f8f9fa;">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            
            <div class="form-group">
                <label>Valor Total</label>
                <input type="text" id="valorTotal" class="form-control" readonly value="R$ 0,00" style="font-size: 1.2rem; font-weight: 600;">
            </div>
            
            <div class="form-group">
                <label>Desconto (Extra)</label>
                <input type="text" name="desconto" id="desconto" class="form-control" value="R$ 0,00" onblur="calcularTotal()">
            </div>
            
            <div class="form-group">
                <label>Valor Final</label>
                <input type="text" id="valorFinal" class="form-control" readonly value="R$ 0,00" style="font-size: 1.3rem; font-weight: 700; color: #28a745;">
            </div>
        
        </div>
    </div>
    
    <input type="hidden" name="valor_total" id="inputValorTotal" value="0">
    <input type="hidden" name="valor_final" id="inputValorFinal" value="0">
    
    <div style="display: flex; gap: 15px; justify-content: flex-end;">
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-save"></i> Salvar Orçamento</button>
    </div>
</form>

<script>
const procedimentos = <?php echo json_encode($procedimentos); ?>;
const convenios = <?php echo json_encode($convenios); ?>;
const precosConvenio = <?php echo json_encode($precos_convenio); ?>;
let itemCount = 0;

function obterValorProcedimento(procId) {
    const proc = procedimentos.find(p => p.id == procId);
    if (!proc) return 0;
    
    let valorBase = parseFloat(proc.valor_padrao);
    const convenioSelect = document.getElementById('convenioSelect');
    const convId = convenioSelect.value;
    
    if (convId) {
        // Verifica se tem preço customizado na tabela procedimentos_convenio
        if (precosConvenio[convId] && precosConvenio[convId][procId] !== undefined) {
            return parseFloat(precosConvenio[convId][procId]);
        }
        
        // Verifica se o convenio tem desconto padrao
        const option = convenioSelect.selectedOptions[0];
        const descontoPadrao = parseFloat(option.dataset.desconto) || 0;
        if (descontoPadrao > 0) {
            return valorBase - (valorBase * (descontoPadrao / 100));
        }
    }
    
    return valorBase;
}

function atualizarPrecosConvenio() {
    document.querySelectorAll('.item-orcamento').forEach(item => {
        const select = item.querySelector('.procedimento-select');
        if (select && select.value) {
            atualizarValor(select);
        }
    });
}

function adicionarItem() {
    itemCount++;
    const div = document.createElement('div');
    div.className = 'item-orcamento';
    div.style.cssText = 'display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 10px; align-items: end; margin-bottom: 10px; padding: 15px; background: white; border-radius: 8px; border: 1px solid #e0e0e0;';
    
    let options = '<option value="">Selecione</option>';
    procedimentos.forEach(proc => {
        options += `<option value="${proc.id}">${proc.descricao}</option>`;
    });
    
    div.innerHTML = `
        <div class="form-group" style="margin: 0;">
            <label>Procedimento</label>
            <select name="itens[${itemCount}][procedimento_id]" class="form-control procedimento-select" required onchange="atualizarValor(this)">${options}</select>
        </div>
        <div class="form-group" style="margin: 0;">
            <label>Dente</label>
            <input type="text" name="itens[${itemCount}][dente]" class="form-control" placeholder="Ex: 18">
        </div>
        <div class="form-group" style="margin: 0;">
            <label>Qtd</label>
            <input type="number" name="itens[${itemCount}][quantidade]" class="form-control quantidade" value="1" min="1" onchange="calcularItem(this)">
        </div>
        <div class="form-group" style="margin: 0;">
            <label>Valor</label>
            <input type="text" name="itens[${itemCount}][valor_unitario]" class="form-control valor-unitario" value="R$ 0,00" onblur="calcularItem(this)">
        </div>
        <button type="button" class="btn btn-danger" onclick="removerItem(this)" style="height: 40px;"><i class="fa-solid fa-trash"></i></button>
    `;
    
    document.getElementById('itensOrcamento').appendChild(div);
}

function removerItem(btn) {
    btn.closest('.item-orcamento').remove();
    calcularTotal();
}

function atualizarValor(select) {
    const procId = select.value;
    if (!procId) return;
    const valor = obterValorProcedimento(procId);
    const item = select.closest('.item-orcamento');
    item.querySelector('.valor-unitario').value = formatarMoeda(valor);
    calcularItem(select);
}

function calcularItem(element) {
    const item = element.closest('.item-orcamento');
    const qtd = parseInt(item.querySelector('.quantidade').value) || 1;
    const valorTexto = item.querySelector('.valor-unitario').value.replace(/[^\d]/g, '');
    const valor = parseInt(valorTexto) / 100;
    
    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    document.querySelectorAll('.item-orcamento').forEach(item => {
        const qtdInput = item.querySelector('.quantidade');
        const valorInput = item.querySelector('.valor-unitario');
        
        const qtd = parseInt(qtdInput.value) || 0;
        const valorTexto = valorInput.value.replace(/[^\d]/g, '') || '0';
        const valor = parseInt(valorTexto) / 100;
        
        total += qtd * valor;
    });
    
    const descontoInput = document.getElementById('desconto');
    const descontoTexto = descontoInput.value.replace(/[^\d]/g, '') || '0';
    const desconto = parseInt(descontoTexto) / 100;
    const final = Math.max(0, total - desconto);
    
    document.getElementById('valorTotal').value = formatarMoeda(total);
    document.getElementById('valorFinal').value = formatarMoeda(final);
    document.getElementById('inputValorTotal').value = total.toFixed(2);
    document.getElementById('inputValorFinal').value = final.toFixed(2);
}

function formatarMoeda(valor) {
    return 'R$ ' + valor.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

document.getElementById('desconto').addEventListener('blur', function(e) {
    let valor = e.target.value.replace(/[^\d]/g, '');
    if (valor) {
        e.target.value = formatarMoeda(parseInt(valor) / 100);
    }
    calcularTotal();
});

// Adicionar primeiro item automaticamente
adicionarItem();
</script>

<?php include '../includes/footer.php'; ?>
