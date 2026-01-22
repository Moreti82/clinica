<?php
require_once __DIR__ . '/../config/conexao.php';

$id              = $_POST['id'] ?? null;
$nome            = trim($_POST['nome'] ?? '');
$cpf             = trim($_POST['cpf'] ?? '');
$telefone        = trim($_POST['telefone'] ?? '');
$email           = trim($_POST['email'] ?? '');
$data_nascimento = $_POST['data_nascimento'] ?? null;
$ativo            = $_POST['ativo'] ?? 1;

if (!$id || $nome === '') {
    die('Dados inválidos');
}

$stmt = $db->prepare("
    UPDATE pacientes SET
        nome = :nome,
        cpf = :cpf,
        telefone = :telefone,
        email = :email,
        data_nascimento = :data_nascimento,
        ativo = :ativo
    WHERE id = :id
");

$stmt->execute([
    ':nome'            => $nome,
    ':cpf'             => $cpf,
    ':telefone'        => $telefone,
    ':email'           => $email,
    ':data_nascimento' => $data_nascimento,
    ':ativo'           => $ativo,
    ':id'              => $id
]);

header('Location: listar.php');
exit;
