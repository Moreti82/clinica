<?php
require_once '../config/conexao.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $db->prepare("UPDATE profissionais SET ativo = 0 WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

// Redireciona de volta para a lista com mensagem de sucesso
header("Location: profissionais.php?sucesso=1");
exit;
?>
