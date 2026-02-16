<?php
require_once '../config/conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Atualizar o paciente para inativo
    $stmt = $db->prepare("UPDATE pacientes SET ativo = 0 WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        // Se a atualização for bem-sucedida, redireciona para a listagem de pacientes
        header("Location: listar.php?sucesso=1");
        exit;
    } else {
        echo "Erro ao atualizar o status do paciente.";
    }
} else {
    header("Location: listar.php?erro=1");
    exit;
}
