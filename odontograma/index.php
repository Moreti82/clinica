<?php
// Garantir que não há espaços antes desta tag
error_reporting(E_ALL & ~E_NOTICE); // Silenciar avisos menores no Xdebug
ini_set('display_errors', 1);

$page_title = 'Odontograma Calibrado';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$paciente_id = $_GET['paciente_id'] ?? null;
if (!$paciente_id) {
    redirecionar('../pacientes/listar.php', 'Paciente nao especificado', 'danger');
}

// Buscar dados do paciente
$stmt = $db->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt->execute([$paciente_id]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    redirecionar('../pacientes/listar.php', 'Paciente nao encontrado', 'danger');
}

// Carregar mapeamento do JSON
$mapeamento_path = '../db/mapeamento_dentes.json';
$coordenadas = [];
if (file_exists($mapeamento_path)) {
    $conteudoStr = file_get_contents($mapeamento_path);
    if (!empty($conteudoStr)) {
        $coordenadas = json_decode($conteudoStr, true) ?: [];
    }
}

// Buscar registros clinicos
$stmt = $db->prepare("
    SELECT o.*, p.descricao as procedimento_nome 
    FROM odontograma o 
    LEFT JOIN procedimentos p ON o.procedimento_id = p.id 
    WHERE o.paciente_id = ?
");
$stmt->execute([$paciente_id]);
$regs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$achados = [];
if ($regs) {
    foreach ($regs as $r) { 
        if (isset($r['condicao']) && $r['condicao'] != 'Saudavel') {
            $achados[$r['dente']] = $r;
        }
    }
}

// Condicoes sem acentos para evitar erro de encoding no JS/PHP
$condicoes = [
    'Saudavel' => ['label' => 'Saudavel', 'cor' => '#10b981', 'icon' => 'fa-check'],
    'Carie' => ['label' => 'Carie', 'cor' => '#ef4444', 'icon' => 'fa-virus'],
    'Tratado' => ['label' => 'Tratado', 'cor' => '#3b82f6', 'icon' => 'fa-shield-halved'],
    'Extraido' => ['label' => 'Extraido', 'cor' => '#64748b', 'icon' => 'fa-xmark'],
    'Canal' => ['label' => 'Canal', 'cor' => '#f59e0b', 'icon' => 'fa-bolt-lightning'],
    'Coroa' => ['label' => 'Coroa', 'cor' => '#8b5cf6', 'icon' => 'fa-crown'],
    'Implante' => ['label' => 'Implante', 'cor' => '#ec4899', 'icon' => 'fa-anchor'],
    'Fratura' => ['label' => 'Fratura', 'cor' => '#f97316', 'icon' => 'fa-burst'],
];

include '../includes/header.php';
?>

<style>
.odontograma-workspace { display: flex; gap: 30px; background: #fff; padding: 30px; border-radius: 30px; }
.arcada-main { position: relative; width: 800px; height: 600px; background: url('../assets/img/arcada_final.png') no-repeat center; background-size: contain; border: 2px solid #f1f5f9; border-radius: 20px; overflow: hidden; }
.calib-overlay { position: absolute; top:0; left:0; right:0; bottom:0; background: rgba(99, 102, 241, 0.1); z-index: 500; display: none; cursor: crosshair; }
.calibrating .tooth-point { pointer-events: none; }
.tooth-point { position: absolute; width:30px; height:30px; border-radius:50%; cursor:pointer; z-index:100; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:800; color:#fff; border: 2px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.3); transition: transform 0.2s; }
.tooth-point:hover { transform: scale(1.4); z-index: 1000; }
#lines-svg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 80; }
.side-panel { flex: 1; }
.callout-entry { background: #f8fafc; border-left: 6px solid #cbd5e1; padding: 12px; border-radius: 12px; margin-bottom: 12px; font-size: 0.9rem; }
</style>

<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-tooth"></i> Odontograma</h1>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 style="margin:0;"><?php echo htmlspecialchars($paciente['nome'] ?? 'Paciente'); ?></h2>
        <div id="calib-status" style="color:#6366f1; font-weight:700;">Modo de Uso Padrao</div>
    </div>
    <div style="display: flex; gap: 10px;">
        <button id="btnCalibrar" class="btn btn-secondary"><i class="fa-solid fa-crosshairs"></i> CALIBRAR</button>
        <button id="btnSalvarCalib" class="btn btn-success" style="display: none;"><i class="fa-solid fa-floppy-disk"></i> SALVAR</button>
    </div>
</div>

<div class="odontograma-workspace">
    <div class="arcada-main" id="arcada-main">
        <div class="calib-overlay" id="calib-overlay"></div>
        <svg id="lines-svg"></svg>
        <?php foreach ($coordenadas as $num => $coord): 
            if (!is_array($coord)) continue; // Evita erro se o dado estiver malformado
            $achado = $achados[$num] ?? null;
            $info = isset($achado['condicao']) ? ($condicoes[$achado['condicao']] ?? $condicoes['Saudavel']) : $condicoes['Saudavel'];
            $cor = $achado ? $info['cor'] : '#1e293b'; ?>
            <div class="tooth-point" id="dot-<?php echo $num; ?>" style="top: <?php echo (isset($coord['top']) ? $coord['top'] : 0); ?>%; left: <?php echo (isset($coord['left']) ? $coord['left'] : 0); ?>%; background: <?php echo $cor; ?>;" onclick="editarDente(<?php echo $num; ?>)">
                <?php echo $num; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="side-panel">
        <h4 style="margin-top:0;">Diagnosticos</h4>
        <div id="findings-list">
            <?php foreach ($achados as $num => $a): 
                $infoA = $condicoes[$a['condicao']] ?? $condicoes['Saudavel']; ?>
                <div class="callout-entry" id="callout-<?php echo $num; ?>" style="border-left-color: <?php echo $infoA['cor']; ?>;">
                    <strong>Dente <?php echo $num; ?></strong>: <span style="color:<?php echo $infoA['cor']; ?>;"><?php echo $infoA['label']; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div id="modalDente" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 15px; width: 400px; padding: 25px;">
        <h3>Dente #<span id="numeroDente"></span></h3>
        <form action="atualizar.php" method="POST">
            <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
            <input type="hidden" name="dente" id="inputDente">
            <div class="form-group">
                <label>Condicao</label>
                <select name="condicao" class="form-control" style="width:100%;">
                    <?php foreach ($condicoes as $k => $i): ?>
                        <option value="<?php echo $k; ?>"><?php echo $i['label']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
let localCoords = <?php echo json_encode($coordenadas); ?>;
let isCalibrating = false;

document.getElementById('btnCalibrar').addEventListener('click', function() {
    isCalibrating = !isCalibrating;
    const main = document.getElementById('arcada-main');
    this.innerText = isCalibrating ? 'Cancelar' : 'Calibrar';
    document.getElementById('calib-overlay').style.display = isCalibrating ? 'block' : 'none';
    document.getElementById('btnSalvarCalib').style.display = isCalibrating ? 'inline-block' : 'none';
    if(isCalibrating) main.classList.add('calibrating'); else main.classList.remove('calibrating');
});

document.getElementById('calib-overlay').addEventListener('click', function(e) {
    const rect = this.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    const denteNum = prompt("Numero do dente?");
    if(denteNum && !isNaN(denteNum)) {
        localCoords[denteNum] = { top: y.toFixed(2), left: x.toFixed(2) };
        renderHotspotsLocally();
    }
});

document.getElementById('btnSalvarCalib').addEventListener('click', function() {
    if(confirm("Deseja salvar o mapeamento?")) {
        fetch('salvar_coordenadas.php', { method: 'POST', body: JSON.stringify(localCoords) })
        .then(res => res.json()).then(data => { alert(data.msg); location.reload(); });
    }
});

function renderHotspotsLocally() {
    const main = document.getElementById('arcada-main');
    Array.from(main.querySelectorAll('.tooth-point')).forEach(d => d.remove());
    Object.keys(localCoords).forEach(num => {
        const dot = document.createElement('div');
        dot.className = 'tooth-point';
        dot.style.top = localCoords[num].top + '%';
        dot.style.left = localCoords[num].left + '%';
        dot.style.background = '#6366f1';
        dot.innerText = num;
        main.appendChild(dot);
    });
}

function editarDente(num) {
    if(isCalibrating) return;
    document.getElementById('numeroDente').textContent = num;
    document.getElementById('inputDente').value = num;
    document.getElementById('modalDente').style.display = 'flex';
}
function fecharModal() { document.getElementById('modalDente').style.display = 'none'; }

window.addEventListener('load', function() {
    const svg = document.getElementById('lines-svg');
    const container = document.getElementById('arcada-main').getBoundingClientRect();
    const condicoes = <?php echo json_encode($condicoes); ?>;
    <?php foreach ($achados as $num => $a): ?>
        if(localCoords[<?php echo $num; ?>]) {
            const coord = localCoords[<?php echo $num; ?>];
            const box = document.getElementById('callout-<?php echo $num; ?>');
            if(box) {
                const boxRect = box.getBoundingClientRect();
                const x1 = (parseFloat(coord.left) / 100) * container.width;
                const y1 = (parseFloat(coord.top) / 100) * container.height;
                const x2 = container.width;
                const y2 = boxRect.top - container.top + (boxRect.height / 2);
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', x1); line.setAttribute('y1', y1);
                line.setAttribute('x2', x2); line.setAttribute('y2', y2);
                const color = condicoes['<?php echo $a['condicao']; ?>'] ? condicoes['<?php echo $a['condicao']; ?>'].cor : '#6366f1';
                line.setAttribute('stroke', color); line.setAttribute('stroke-width', '2'); line.setAttribute('stroke-dasharray', '5');
                svg.appendChild(line);
            }
        }
    <?php endforeach; ?>
});
</script>

<?php include '../includes/footer.php'; ?>
