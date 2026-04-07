<?php
/**
 * Header padrão do sistema OdontoCare
 * Inclui verificação de sessão e estrutura HTML base
 */

// Iniciar sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: /clinica_odonto_completa/auth/login.php");
    exit;
}

// Obter informações do usuário logado
$usuario_nome = $_SESSION['nome'] ?? 'Usuário';
$usuario_perfil = $_SESSION['perfil'] ?? 'user';
$is_admin = strtolower($usuario_perfil) === 'admin';

// Página atual para menu ativo
$pagina_atual = basename($_SERVER['PHP_SELF']);
$diretorio_atual = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo isset($page_title) ? $page_title . ' - OdontoCare' : 'OdontoCare - Gestão Odontológica'; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Estilos base -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        /* Header */
        .system-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .system-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .system-brand i {
            font-size: 2rem;
            color: #667eea;
        }
        
        .system-title {
            font-size: 1.5rem;
            color: #333;
            font-weight: 600;
        }
        
        .system-subtitle {
            font-size: 0.85rem;
            color: #666;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-details {
            text-align: right;
        }
        
        .user-name {
            font-weight: 600;
            color: #333;
        }
        
        .user-role {
            font-size: 0.8rem;
            color: #667eea;
            text-transform: uppercase;
        }
        
        .btn-logout {
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .btn-logout:hover {
            background: #c82333;
        }
        
        /* Menu Lateral */
        .sidebar {
            position: fixed;
            left: 0;
            top: 80px;
            bottom: 0;
            width: 260px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 2px 0 20px rgba(0,0,0,0.1);
            overflow-y: auto;
            padding: 20px 0;
        }
        
        .menu-section {
            margin-bottom: 20px;
        }
        
        .menu-title {
            padding: 10px 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #999;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #555;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .menu-item:hover,
        .menu-item.active {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.1) 0%, transparent 100%);
            color: #667eea;
            border-left-color: #667eea;
        }
        
        .menu-item i {
            width: 20px;
            text-align: center;
        }
        
        /* Conteúdo Principal */
        .main-content {
            margin-left: 260px;
            margin-top: 80px;
            padding: 30px;
            min-height: calc(100vh - 80px);
        }
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 1.8rem;
            color: white;
            margin-bottom: 10px;
        }
        
        .breadcrumb {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
        }
        
        .breadcrumb a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .card-title {
            font-size: 1.2rem;
            color: #333;
            font-weight: 600;
        }
        
        /* Botões */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        /* Tabelas */
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        /* Status badges */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        /* Alertas */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        /* Formulários */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .system-header {
                padding: 15px;
            }
            
            .system-subtitle {
                display: none;
            }
        }
    </style>
    
    <?php if (isset($page_css)): ?>
    <link rel="stylesheet" href="<?php echo $page_css; ?>">
    <?php endif; ?>
</head>
<body>
    <!-- Header do Sistema -->
    <header class="system-header">
        <div class="system-brand">
            <i class="fa-solid fa-tooth"></i>
            <div>
                <div class="system-title">Personalize</div>
                <div class="system-subtitle">Gestão Inteligente</div>
            </div>
        </div>
        
        <div class="user-info">
            <div class="user-details">
                <div class="user-name"><?php echo htmlspecialchars($usuario_nome); ?></div>
                <div class="user-role"><?php echo htmlspecialchars($usuario_perfil); ?></div>
            </div>
            <a href="/clinica_odonto_completa/auth/logout.php" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Sair
            </a>
        </div>
    </header>
    
    <!-- Menu Lateral -->
    <nav class="sidebar">
        <div class="menu-section">
            <div class="menu-title">Principal</div>
            <a href="/clinica_odonto_completa/dashboard/index.php" class="menu-item <?php echo $diretorio_atual === 'dashboard' ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>
            <a href="/clinica_odonto_completa/agendamentos/calendario.php" class="menu-item <?php echo $diretorio_atual === 'agendamentos' ? 'active' : ''; ?>">
                <i class="fa-solid fa-calendar-check"></i>
                Agendamentos
            </a>
        </div>
        
        <div class="menu-section">
            <div class="menu-title">Cadastros</div>
            <a href="/clinica_odonto_completa/pacientes/listar.php" class="menu-item <?php echo $diretorio_atual === 'pacientes' ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-injured"></i>
                Pacientes
            </a>
            <a href="/clinica_odonto_completa/profissionais/profissionais.php" class="menu-item <?php echo $diretorio_atual === 'profissionais' ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-doctor"></i>
                Profissionais
            </a>
            <a href="/clinica_odonto_completa/procedimentos/procedimentos.php" class="menu-item <?php echo $diretorio_atual === 'procedimentos' ? 'active' : ''; ?>">
                <i class="fa-solid fa-tooth"></i>
                Procedimentos
            </a>
            <a href="/clinica_odonto_completa/convenios/index.php" class="menu-item <?php echo $diretorio_atual === 'convenios' ? 'active' : ''; ?>">
                <i class="fa-solid fa-id-card"></i>
                Convênios
            </a>
        </div>
        
        <div class="menu-section">
            <div class="menu-title">Financeiro</div>
            <a href="/clinica_odonto_completa/orcamentos/listar.php" class="menu-item <?php echo $diretorio_atual === 'orcamentos' ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                Orçamentos
            </a>
            <a href="/clinica_odonto_completa/financeiro/contas_receber.php" class="menu-item <?php echo $diretorio_atual === 'financeiro' ? 'active' : ''; ?>">
                <i class="fa-solid fa-money-bill-wave"></i>
                Contas a Receber
            </a>
            <a href="/clinica_odonto_completa/financeiro/caixa.php" class="menu-item <?php echo $diretorio_atual === 'financeiro' ? 'active' : ''; ?>">
                <i class="fa-solid fa-cash-register"></i>
                Caixa
            </a>
        </div>
        
        <div class="menu-section">
            <div class="menu-title">Clínica</div>
            <a href="/clinica_odonto_completa/odontograma/index.php" class="menu-item <?php echo $diretorio_atual === 'odontograma' ? 'active' : ''; ?>">
                <i class="fa-solid fa-teeth-open"></i>
                Odontograma
            </a>
            <a href="/clinica_odonto_completa/estoque/index.php" class="menu-item <?php echo $diretorio_atual === 'estoque' ? 'active' : ''; ?>">
                <i class="fa-solid fa-boxes-stacked"></i>
                Estoque
            </a>
        </div>
        
        <div class="menu-section">
            <div class="menu-title">Relatórios</div>
            <a href="/clinica_odonto_completa/relatorios/index.php" class="menu-item <?php echo $diretorio_atual === 'relatorios' ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-pie"></i>
                Relatórios
            </a>
        </div>
        
        <?php if ($is_admin): ?>
        <div class="menu-section">
            <div class="menu-title">Administração</div>
            <a href="/clinica_odonto_completa/users/listar.php" class="menu-item <?php echo $diretorio_atual === 'users' ? 'active' : ''; ?>">
                <i class="fa-solid fa-users-cog"></i>
                Usuários
            </a>
            <a href="/clinica_odonto_completa/configuracoes/index.php" class="menu-item <?php echo $diretorio_atual === 'configuracoes' ? 'active' : ''; ?>">
                <i class="fa-solid fa-cog"></i>
                Configurações
            </a>
        </div>
        <?php endif; ?>
    </nav>
    
    <!-- Conteúdo Principal -->
    <main class="main-content">
