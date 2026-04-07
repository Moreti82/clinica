<?php
$page_title = 'Prontuário';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$paciente_id = $_GET['paciente_id'] ?? null;

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

// Buscar anamnese
$stmt = $db->prepare("SELECT * FROM anamneses WHERE paciente_id = ?");
$stmt->execute([$paciente_id]);
$anamnese = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar atendimentos do prontuário
$stmt = $db->prepare("
    SELECT p.*, pr.nome as profissional_nome 
    FROM prontuarios p 
    JOIN profissionais pr ON p.profissional_id = pr.id 
    WHERE p.paciente_id = ? 
    ORDER BY p.data_atendimento DESC, p.hora_atendimento DESC
");
$stmt->execute([$paciente_id]);
$atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular idade
$idade = calcularIdade($paciente['data_nascimento']);

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Prontuário Eletrônico</h1>
    <div class="breadcrumb">
        <a href="../dashboard/index.php">Início</a> / 
        <a href="../pacientes/listar.php">Pacientes</a> / Prontuário
    </div>
</div>

<?php exibirFlashMessage(); ?>

<!-- Dados do Paciente -->
<div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
        <div>
            <h2 style="margin-bottom: 10px;"><i class="fa-solid fa-user-injured"></i> <?php echo htmlspecialchars($paciente['nome']); ?></h2>
            <div style="display: flex; gap: 20px; flex-wrap: wrap; opacity: 0.9;">
                <span><i class="fa-solid fa-id-card"></i> CPF: <?php echo mascararCPF($paciente['cpf']); ?></span>
                <span><i class="fa-solid fa-phone"></i> <?php echo mascararTelefone($paciente['telefone']); ?></span>
                <span><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($paciente['email']); ?></span>
                <span><i class="fa-solid fa-cake-candles"></i> <?php echo $idade ? $idade . ' anos' : 'Idade não informada'; ?></span>
            </div>
            
            <?php if ($paciente['endereco']): ?>
                <div style="margin-top: 10px; opacity: 0.9;">
                    <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($paciente['endereco']); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <a href="anamnese.php?paciente_id=<?php echo $paciente_id; ?>" class="btn btn-secondary">
                <i class="fa-solid fa-clipboard-list"></i> <?php echo $anamnese ? 'Editar Anamnese' : 'Fazer Anamnese'; ?>
            </a>
            
            <a href="../odontograma/index.php?paciente_id=<?php echo $paciente_id; ?>" class="btn btn-info" style="color: white;">
                <i class="fa-solid fa-tooth"></i> Odontograma
            </a>
            
            <a href="novo.php?paciente_id=<?php echo $paciente_id; ?>" class="btn" style="background: white; color: #667eea;">
                <i class="fa-solid fa-plus"></i> Novo Atendimento
            </a>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px;">
    
    <!-- Coluna Lateral: Anamnese -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-heart-pulse"></i> Anamnese</div>
            </div>
            
            <?php if ($anamnese): ?>
                <div style="font-size: 0.95rem;">
                    
                    <?php if ($anamnese['queixa_principal']): ?>
                        <p style="margin-bottom: 15px;">
                            <strong>Queixa Principal:</strong><br>
                            <?php echo nl2br(htmlspecialchars($anamnese['queixa_principal'])); ?>
                        </p>
                    <?php endif; ?>
                    
                    
                    <?php if ($anamnese['alergias']): ?>
                        <p style="margin-bottom: 15px; color: #dc3545;">
                            <strong><i class="fa-solid fa-triangle-exclamation"></i> Alergias:</strong><br>
                            <?php echo nl2br(htmlspecialchars($anamnese['alergias'])); ?>
                        </p>
                    <?php endif; ?>
                    
                    
                    <?php if ($anamnese['medicamentos']): ?>
                        <p style="margin-bottom: 15px;">
                            <strong>Medicamentos:</strong><br>
                            <?php echo nl2br(htmlspecialchars($anamnese['medicamentos'])); ?>
                        </p>
                    <?php endif; ?>
                    
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                        
                        <div class="badge <?php echo $anamnese['diabete'] ? 'badge-danger' : 'badge-success'; ?>" style="justify-self: start;">
                            Diabete: <?php echo $anamnese['diabete'] ? 'Sim' : 'Não'; ?>
                        </div>
                        
                        <div class="badge <?php echo $anamnese['problema_cardiaco'] ? 'badge-danger' : 'badge-success'; ?>" style="justify-self: start;">
                            Cardíaco: <?php echo $anamnese['problema_cardiaco'] ? 'Sim' : 'Não'; ?>
                        </div>
                        
                        
                        <div class="badge <?php echo $anamnese['gravida'] ? 'badge-warning' : 'badge-success'; ?>" style="justify-self: start;">
                            Grávida: <?php echo $anamnese['gravida'] ? 'Sim' : 'Não'; ?>
                        </div>
                        
                        
                        <?php if ($anamnese['pressao_arterial']): ?>
                            <div class="badge badge-info" style="justify-self: start;">
                                PA: <?php echo htmlspecialchars($anamnese['pressao_arterial']); ?>
                            </div>
                        <?php endif; ?>
                    
                    </div>
                    
                    
                    <?php if ($anamnese['historico_medico']): ?>
                        <p style="margin-bottom: 15px;">
                            <strong>Histórico Médico:</strong><br>
                            <?php echo nl2br(htmlspecialchars($anamnese['historico_medico'])); ?>
                        </p>
                    <?php endif; ?>
                    
                    
                    <?php if ($anamnese['observacoes']): ?>
                        <p>
                            <strong>Observações:</strong><br>
                            <?php echo nl2br(htmlspecialchars($anamnese['observacoes'])); ?>
                        </p>
                    <?php endif; ?>
                
                </div>
            
            <?php else: ?>
                <div style="text-align: center; padding: 30px; color: #999;">
                    <i class="fa-solid fa-clipboard-question" style="font-size: 3rem; margin-bottom: 15px;"></i>
                    <p>Anamnese não realizada</p>
                    
                    <a href="anamnese.php?paciente_id=<?php echo $paciente_id; ?>" class="btn btn-primary" style="margin-top: 15px;">
                        Realizar Anamnese
                    </a>
                </div>
            <?php endif; ?>
        </div>
    
    </div>
    
    
    <!-- Coluna Principal: Histórico de Atendimentos -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-notes-medical"></i> Histórico de Atendimentos</div>
            </div>
            
            <?php if (empty($atendimentos)): ?>
                <div style="text-align: center; padding: 50px; color: #999;">
                    <i class="fa-solid fa-folder-open" style="font-size: 4rem; margin-bottom: 20px;"></i>
                    <p>Nenhum atendimento registrado</p>
                    
                    <a href="novo.php?paciente_id=<?php echo $paciente_id; ?>" class="btn btn-primary" style="margin-top: 20px;">
                        <i class="fa-solid fa-plus"></i> Registrar Primeiro Atendimento
                    </a>
                </div>
            
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <?php foreach ($atendimentos as $atendimento): ?>
                        
                        <div style="border: 1px solid #e0e0e0; border-radius: 10px; padding: 20px; background: #fafafa;">
                            
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                
                                <div>
                                    <div style="font-size: 1.1rem; font-weight: 600; color: #667eea; margin-bottom: 5px;">
                                        <i class="fa-solid fa-calendar"></i> <?php echo formatarData($atendimento['data_atendimento']); ?>
                                        <?php if ($atendimento['hora_atendimento']): ?>
                                            <span style="color: #666; font-weight: normal;"> às <?php echo $atendimento['hora_atendimento']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="color: #666; font-size: 0.9rem;">
                                        <i class="fa-solid fa-user-doctor"></i> Dr(a). <?php echo htmlspecialchars($atendimento['profissional_nome']); ?>
                                    </div>
                                </div>
                                
                                
                                <a href="visualizar.php?id=<?php echo $atendimento['id']; ?>" class="btn btn-secondary" style="padding: 5px 12px; font-size: 0.8rem;">
                                    Ver Detalhes
                                </a>
                            
</div>
                            
                            
                            <?php if ($atendimento['queixa']): ?>
                                
                                <p style="margin-bottom: 10px;">
                                    <strong>Queixa:</strong> <?php echo limitarTexto($atendimento['queixa'], 100); ?>
                                </p>
                            
                            <?php endif; ?>
                            
                            
                            
                            <?php if ($atendimento['diagnostico']): ?>
                                
                                <p style="margin-bottom: 10px;">
                                    <strong>Diagnóstico:</strong> <?php echo limitarTexto($atendimento['diagnostico'], 100); ?>
                                </p>
                            
                            <?php endif; ?>
                            
                            
                            
                            <?php if ($atendimento['procedimentos_realizados']): ?>
                                
                                <p>
                                    <strong>Procedimentos:</strong> <?php echo limitarTexto($atendimento['procedimentos_realizados'], 100); ?>
                                </p>
                            
                            <?php endif; ?>
                        
                        </div>
                    
                    <?php endforeach; ?>
                
                </div>
            
            <?php endif; ?>
        
        </div>
    
    </div>
</div>

<?php include '../includes/footer.php'; ?>
