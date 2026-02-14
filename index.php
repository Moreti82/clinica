<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ./auth/login.php ");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel da Clínica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style-index.css">
</head>
<body id="clinica-body">

<div id="clinica-container">
    <h1 id="clinica-title">Painel da Clínica</h1>

    <div id="clinica-menu">
        <?php if (isset($_SESSION['perfil']) && strtolower($_SESSION['perfil']) === 'admin'): ?>
            <a href="users/listar.php" class="clinica-btn">
                <i class="fa-solid fa-user-shield"></i> Usuarios
            </a>
        <?php endif; ?>

        <a href="pacientes/listar.php" class="clinica-btn">
            <i class="fa-solid fa-user-injured"></i> Pacientes
        </a>

        <a href="agendamentos/profissionais.php" class="clinica-btn">    
        <i class="fa-solid fa-stethoscope"></i> Profissionais
        </a>
                
        <a href="agendamentos/calendario.php" class="clinica-btn">
            <i class="fa-solid fa-calendar-check"></i> Agendamentos
        </a>
        <a href="#" class="clinica-btn">
            <i class="fa-solid fa-tooth"></i> Procedimentos
        </a>
        <a href="./auth/logout.php" class="clinica-btn logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Sair
        </a>
    </div>

</div>

</body>
</html>
