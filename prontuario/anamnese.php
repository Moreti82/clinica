<?php
$page_title = 'Anamnese';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$paciente_id = $_GET['paciente_id'] ?? null;

if (!$paciente_id) {
    redirecionar('../pacientes/listar.php', 'Paciente não especificado', 'danger');
}

$stmt = $db->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt->execute([$paciente_id]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    redirecionar('../pacientes/listar.php', 'Paciente não encontrado', 'danger');
}

$stmt = $db->prepare("SELECT * FROM anamneses WHERE paciente_id = ?");
$stmt->execute([$paciente_id]);
$anamnese = $stmt->fetch(PDO::FETCH_ASSOC);

$idade = calcularIdade($paciente['data_nascimento']);

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Ficha de Anamnese</h1>
    <div class="breadcrumb">
        <a href="../dashboard/index.php">Início</a> / 
        <a href="../pacientes/listar.php">Pacientes</a> / 
        <a href="index.php?paciente_id=<?php echo $paciente_id; ?>">Prontuário</a> / Anamnese
    </div>
</div>

<form action="salvar_anamnese.php" method="POST">
    <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
    <?php if ($anamnese): ?><input type="hidden" name="anamnese_id" value="<?php echo $anamnese['id']; ?>"><?php endif; ?>
    
    <div class="card">
        <div class="card-header"><div class="card-title">Dados da Anamnese</div></div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Queixa Principal</label>
                <textarea name="queixa_principal" class="form-control" rows="3"><?php echo $anamnese['queixa_principal'] ?? ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Histórico Médico</label>
                <textarea name="historico_medico" class="form-control" rows="3"><?php echo $anamnese['historico_medico'] ?? ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Histórico Familiar</label>
                <textarea name="historico_familiar" class="form-control" rows="3"><?php echo $anamnese['historico_familiar'] ?? ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Medicamentos</label>
                <textarea name="medicamentos" class="form-control" rows="3"><?php echo $anamnese['medicamentos'] ?? ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label style="color: #dc3545;">Alergias</label>
                <textarea name="alergias" class="form-control" rows="3" style="border-color: #dc3545;"><?php echo $anamnese['alergias'] ?? ''; ?></textarea>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header"><div class="card-title">Condições</div></div>
        
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
            <div class="form-group">
                <label>Pressão Arterial</label>
                <input type="text" name="pressao_arterial" class="form-control" value="<?php echo $anamnese['pressao_arterial'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Diabete</label>
                <select name="diabete" class="form-control">
                    <option value="0" <?php echo ($anamnese['diabete'] ?? 0) == 0 ? 'selected' : ''; ?>>Não</option>
                    <option value="1" <?php echo ($anamnese['diabete'] ?? 0) == 1 ? 'selected' : ''; ?>>Sim</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Cardíaco</label>
                <select name="problema_cardiaco" class="form-control">
                    <option value="0" <?php echo ($anamnese['problema_cardiaco'] ?? 0) == 0 ? 'selected' : ''; ?>>Não</option>
                    <option value="1" <?php echo ($anamnese['problema_cardiaco'] ?? 0) == 1 ? 'selected' : ''; ?>>Sim</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Grávida</label>
                <select name="gravida" class="form-control">
                    <option value="0" <?php echo ($anamnese['gravida'] ?? 0) == 0 ? 'selected' : ''; ?>>Não</option>
                    <option value="1" <?php echo ($anamnese['gravida'] ?? 0) == 1 ? 'selected' : ''; ?>>Sim</option>
                </select>
            </div>
        </div>
    </div>
    
    <div style="display: flex; gap: 15px; justify-content: flex-end;">
        <a href="index.php?paciente_id=<?php echo $paciente_id; ?>" class="btn btn-secondary">Voltar</a>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>
