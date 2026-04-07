<?php
$page_title = 'Novo Atendimento';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$paciente_id = $_GET['paciente_id'] ?? null;
$agendamento_id = $_GET['agendamento_id'] ?? null;

if (!$paciente_id) {
    redirecionar('../pacientes/listar.php', 'Paciente não especificado', 'danger');
}

// Buscar dados do paciente
$stmt = $db->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt->execute([$paciente_id]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    redirecionar('../pacientes/listar.php', 'Paciente não encontrado', 'danger');
}

// Buscar profissionais
$profissionais = $db->query("SELECT id, nome FROM profissionais WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Se veio de um agendamento, buscar dados
$agendamento = null;
if ($agendamento_id) {
    $stmt = $db->prepare("
        SELECT a.*, p.nome as profissional_nome 
        FROM agendamentos a 
        JOIN profissionais p ON a.profissional_id = p.id 
        WHERE a.id = ? AND a.paciente_id = ?
    ");
    $stmt->execute([$agendamento_id, $paciente_id]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
}

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Novo Atendimento</h1>
    <div class="breadcrumb">
        <a href="../dashboard/index.php">Início</a> / 
        <a href="../pacientes/listar.php">Pacientes</a> / 
        <a href="index.php?paciente_id=<?php echo $paciente_id; ?>">Prontuário</a> / Novo Atendimento
    </div>
</div>

<div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-bottom: 25px;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <i class="fa-solid fa-user-injured" style="font-size: 2rem;"></i>
        <div>
            <h2 style="margin: 0;"><?php echo htmlspecialchars($paciente['nome']); ?></h2>
            <div style="opacity: 0.9;">CPF: <?php echo mascararCPF($paciente['cpf']); ?></div>
        </div>
    </div>
</div>

<form action="salvar.php" method="POST">
    <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
    <?php if ($agendamento_id): ?><input type="hidden" name="agendamento_id" value="<?php echo $agendamento_id; ?>"><?php endif; ?>
    
    <div class="card">
        <div class="card-header"><div class="card-title">Dados do Atendimento</div></div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Profissional *</label>
                <select name="profissional_id" class="form-control" required>
                    <option value="">Selecione</option>
                    <?php foreach ($profissionais as $prof): ?>
                        <option value="<?php echo $prof['id']; ?>" <?php echo ($agendamento && $agendamento['profissional_id'] == $prof['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($prof['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Data *</label>
                <input type="date" name="data_atendimento" class="form-control" required 
                    value="<?php echo $agendamento ? $agendamento['data'] : date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label>Hora</label>
                <input type="time" name="hora_atendimento" class="form-control"
                    value="<?php echo $agendamento ? $agendamento['hora'] : ''; ?>">
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header"><div class="card-title">Detalhes do Atendimento</div></div>
        
        <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
            <div class="form-group">
                <label>Queixa Principal</label>
                <textarea name="queixa" class="form-control" rows="3"><?php echo $agendamento ? $agendamento['observacoes'] : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Diagnóstico</label>
                <textarea name="diagnostico" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label>Procedimentos Realizados</label>
                <textarea name="procedimentos_realizados" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label>Prescrição / Receituário</label>
                <textarea name="prescricao" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label>Observações</label>
                <textarea name="observacoes" class="form-control" rows="2"></textarea>
            </div>
        </div>
    </div>
    
    <div style="display: flex; gap: 15px; justify-content: flex-end;">
        <a href="index.php?paciente_id=<?php echo $paciente_id; ?>" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Salvar Atendimento</button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>
