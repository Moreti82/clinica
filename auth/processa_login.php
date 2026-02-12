<?php
session_start();
require_once '../config/conexao.php';

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// Validação inicial
if (empty($email) || empty($senha)) {
    $_SESSION['erro_login'] = "Informe e-mail e senha para acessar.";
    header("Location: login.php");
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

// Verifica usuário e senha
if ($user && password_verify($senha, $user['senha'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nome']    = $user['nome'];
    $_SESSION['perfil']  = $user['perfil'];

    header("Location: ../index.php");
    exit;
}

// Se falhar, define mensagem de erro
$_SESSION['erro_login'] = "Usuário ou senha inválidos.";
header("Location: login.php");
exit;
