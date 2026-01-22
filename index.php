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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
    <h1>Painel da Clínica</h1>

    <div class="menu">
        <a href="pacientes/listar.php" class="btn">Pacientes</a>
        <a href="#" class="btn">Agendamentos</a>
        <a href="#" class="btn">Procedimentos</a>
        <a href="./auth/logout.php" class="btn">Sair</a>
    </div>
</div>

</body>
</html>
