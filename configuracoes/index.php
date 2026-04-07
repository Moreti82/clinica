<?php
$page_title = 'Configurações';
require_once '../includes/functions.php';
require_once '../config/conexao.php';

// Buscar configurações
$configuracoes = $db->query("SELECT * FROM configuracoes ORDER BY chave")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $db->prepare("UPDATE configuracoes SET valor = ? WHERE chave = ?");
        
        foreach ($_POST as $chave => $valor) {
            if (strpos($chave, 'config_') === 0) {
                $chaveReal = substr($chave, 7);
                $stmt->execute([$valor, $chaveReal]);
            }
        }
        
        redirecionar('index.php', 'Configurações salvas!');
    } catch (Exception $e) {
        $erro = 'Erro ao salvar: ' . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Configurações do Sistema</h1>
    <div class="breadcrumb"><a href="../dashboard/index.php">Início</a> / Configurações</div>
</div>

<?php exibirFlashMessage(); ?>
<?php if (isset($erro)): ?><div class="alert alert-danger"><?php echo $erro; ?></div><?php endif; ?>

<form method="POST">
    
    <!-- Dados da Clínica -->
    <div class="card" style="margin-bottom: 25px;">
        <div class="card-header"><div class="card-title"><i class="fa-solid fa-hospital"></i> Dados da Clínica</div></div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <?php 
            $camposClinica = array_filter($configuracoes, fn($c) => strpos($c['chave'], 'clinica_') === 0);
            foreach ($camposClinica as $config): 
            ?>
                <div class="form-group">
                    <label><?php echo htmlspecialchars($config['descricao']); ?></label>
                    <input type="text" name="config_<?php echo $config['chave']; ?>" class="form-control" 
                           value="<?php echo htmlspecialchars($config['valor']); ?>">
                </div>
            <?php endforeach; ?>
        
        </div>
    </div>
    
    
    <!-- Configurações de Agendamento -->
    <div class="card" style="margin-bottom: 25px;">
        <div class="card-header"><div class="card-title"><i class="fa-solid fa-calendar"></i> Configurações de Agendamento</div></div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <?php 
            $camposAgenda = array_filter($configuracoes, fn($c) => in_array($c['chave'], ['horario_inicio', 'horario_fim', 'intervalo_agenda']));
            foreach ($camposAgenda as $config): 
            ?>
                <div class="form-group">
                    <label><?php echo htmlspecialchars($config['descricao']); ?></label>
                    <input type="text" name="config_<?php echo $config['chave']; ?>" class="form-control" 
                           value="<?php echo htmlspecialchars($config['valor']); ?>">
                </div>
            <?php endforeach; ?>
        
        </div>
    </div>
    
    
    <!-- Configurações de Notificação -->
    <div class="card" style="margin-bottom: 25px;">
        <div class="card-header"><div class="card-title"><i class="fa-solid fa-bell"></i> Notificações</div></div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <?php 
            $camposNotif = array_filter($configuracoes, fn($c) => strpos($c['chave'], 'dias_antecedencia') === 0);
            foreach ($camposNotif as $config): 
            ?>
                <div class="form-group">
                    <label><?php echo htmlspecialchars($config['descricao']); ?></label>
                    <input type="number" name="config_<?php echo $config['chave']; ?>" class="form-control" 
                           value="<?php echo htmlspecialchars($config['valor']); ?>">
                </div>
            <?php endforeach; ?>
        
        </div>
    </div>
    
    
    <div style="display: flex; gap: 15px; justify-content: flex-end;">
        <a href="../dashboard/index.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Salvar Configurações</button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>
