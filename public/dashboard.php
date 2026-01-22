<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>

<h2>Bem-vindo, <?= $_SESSION['nome'] ?></h2>
<p>Perfil: <?= $_SESSION['perfil'] ?></p>

<a href="../auth/logout.php">Sair</a>

</body>
</html>
