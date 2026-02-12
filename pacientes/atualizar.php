<?php
require_once __DIR__ . '/../config/conexao.php';

$id              = $_POST['id'] ?? null;
$nome            = trim($_POST['nome'] ?? '');
$cpf             = trim($_POST['cpf'] ?? '');
$endereco        = trim($_POST['endereco'] ?? '');
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
        endereco = :endereco,
        telefone = :telefone,
        email = :email,
        data_nascimento = :data_nascimento,
        ativo = :ativo
    WHERE id = :id
");

$stmt->execute([
    ':nome'            => $nome,
    ':cpf'             => $cpf,
    ':endereco'        => $endereco,
    ':telefone'        => $telefone,
    ':email'           => $email,
    ':data_nascimento' => $data_nascimento,
    ':ativo'           => $ativo,
    ':id'              => $id
]);

if ($stmt->rowCount() > 0) {
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-4">

        <div class="alert alert-success text-center" role="alert">
            Dados atualizados com sucesso! Redirecionando...
        </div>

    </body>
    </html>
    ';

    header("refresh:3;url=listar.php");
    exit;

} else {

    echo '
    <div class="alert alert-warning text-center" role="alert">
        Nenhuma alteração foi realizada.
    </div>
    ';
}

