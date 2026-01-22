<?php
session_start();
require_once '../config/conexao.php';

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

$sql = "
SELECT u.*, p.perfil
FROM users u
JOIN perfis p ON p.id = u.perfil_id
WHERE u.email = :email
AND u.senha = :senha
AND u.ativo = 1
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':email' => $email,
    ':senha' => $senha
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['nome']      = $user['nome'];
    $_SESSION['perfil']    = $user['perfil'];

    header("Location: ../public/dashboard.php");
    exit;
} else {
    echo "Login inválido";
}
