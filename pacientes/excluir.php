<?php
require_once '../config/conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Excluir o paciente
    $stmt = $db->prepare("DELETE FROM pacientes WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        // Se a exclusão for bem-sucedida, redireciona para a listagem de pacientes
        header("Location: listar.php");
        exit;
    } else {
        // Caso ocorra algum erro, você pode adicionar uma mensagem de erro
        echo "Erro ao excluir o paciente.";
    }
} else {
    // Se o ID não for encontrado na URL, redireciona para a listagem de pacientes
    header("Location: listar.php");
    exit;
}
?>
