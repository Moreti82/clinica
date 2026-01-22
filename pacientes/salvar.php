<?php
require_once __DIR__ . '/../config/conexao.php';

// Captura segura dos dados
$nome            = trim($_POST['nome'] ?? '');
$cpf             = trim($_POST['cpf'] ?? '');
$telefone        = trim($_POST['telefone'] ?? '');
$email           = trim($_POST['email'] ?? '');
$data_nascimento = $_POST['data_nascimento'] ?? null;

// Validação mínima
if ($nome === '') {
    die('Nome é obrigatório');
}

try {
    $stmt = $db->prepare("
        INSERT INTO pacientes 
        (nome, cpf, telefone, email, data_nascimento, ativo)
        VALUES 
        (:nome, :cpf, :telefone, :email, :data_nascimento, 1)
    ");

    $stmt->execute([
        ':nome'            => $nome,
        ':cpf'             => $cpf,
        ':telefone'        => $telefone,
        ':email'           => $email,
        ':data_nascimento' => $data_nascimento
    ]);

    // Volta para a lista
    header('Location: listar.php');
    exit;

} catch (Exception $e) {
    die("Erro ao salvar paciente: " . $e->getMessage());
}
