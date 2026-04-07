<?php
require_once '../includes/functions.php';

// Receber o JSON enviado por POST
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) {
    // Salvar as coordenadas no arquivo JSON de configuração
    file_put_contents('../db/mapeamento_dentes.json', json_encode($data, JSON_PRETTY_PRINT));
    echo json_encode(['status' => 'success', 'msg' => 'Mapeamento salvo com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Dados inválidos recebidos.']);
}
?>
