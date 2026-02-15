<?php
require_once '../config/conexao.php';

$id = $_POST['id'] ?? null;

if ($id) {
    $stmt = $db->prepare("UPDATE profissionais SET ativo = 1 WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

// Redireciona para a lista de inativos com mensagem
header("Location: inativos.php?sucesso=1");
exit;
?>
