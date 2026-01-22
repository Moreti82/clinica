<?php
session_start();
require_once '../config/conexao.php';

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    header("Location: login.php?erro=1");
    exit;
}

$sql = "
SELECT u.*, p.perfil
FROM users u
JOIN perfis p ON p.id = u.perfil_id
WHERE u.email = :email
AND u.ativo = 1
";

$stmt = $db->prepare($sql);
$stmt->execute([':email' => $email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($senha, $user['senha'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nome']   = $user['nome'];
    $_SESSION['perfil'] = $user['perfil'];

    header("Location: ../index.php");
    exit;
}

header("Location: login.php?erro=1");
exit;
