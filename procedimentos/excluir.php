<?php
require_once '../includes/functions.php';
require_once '../config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    redirecionar('procedimentos.php', 'Procedimento não especificado', 'danger');
}

try {
    $stmt = $db->prepare("DELETE FROM procedimentos WHERE id = ?");
    $stmt->execute([$id]);
    
    redirecionar('procedimentos.php', 'Procedimento excluído!');
} catch (Exception $e) {
    redirecionar('procedimentos.php', 'Erro ao excluir: ' . $e->getMessage(), 'danger');
}
