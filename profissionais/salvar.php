<?php
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome          = $_POST['nome'] ?? null;
    $cro           = $_POST['cro'] ?? null;
    $especialidade = $_POST['especialidade'] ?? '';
    $telefone      = $_POST['telefone'] ?? '';
    $ativo         = $_POST['ativo'] ?? 1;

    if (!$nome || !$cro) {
        header("Location: novo.php?erro=1");
        exit;
    }

    try {

        $sql = "INSERT INTO profissionais 
                (nome, cro, especialidade, telefone, ativo)
                VALUES 
                (:nome, :cro, :especialidade, :telefone, :ativo)";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':nome'          => $nome,
            ':cro'           => $cro,
            ':especialidade' => $especialidade,
            ':telefone'      => $telefone,
            ':ativo'         => $ativo
        ]);

        header("Location: novo.php?sucesso=1");
        exit;

    } catch (PDOException $e) {

        header("Location: novo.php?erro=2");
        exit;
    }

} else {
    header("Location: novo.php");
    exit;
}
