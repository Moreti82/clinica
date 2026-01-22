<?php
require_once '../auth/verifica_admin.php';
require_once '../config/conexao.php';

$nome      = $_POST['nome'] ?? '';
$email     = $_POST['email'] ?? '';
$senha     = $_POST['senha'] ?? '';
$perfil_id = $_POST['perfil_id'] ?? '';

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "
INSERT INTO users (nome, email, senha, perfil_id)
VALUES (:nome, :email, :senha, :perfil_id)
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':nome' => $nome,
    ':email' => $email,
    ':senha' => $senhaHash,
    ':perfil_id' => $perfil_id
]);

header("Location: listar.php");
exit;
