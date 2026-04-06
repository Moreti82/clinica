<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao    = $_POST['descricao'];
    $valor_padrao = $_POST['valor_padrao'];
    $observacoes  = $_POST['observacoes'] ?? null;

    try {
        $sql = "INSERT INTO procedimentos (descricao, valor_padrao, observacoes) 
                VALUES (:descricao, :valor_padrao, :observacoes)";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':valor_padrao', $valor_padrao);
        $stmt->bindParam(':observacoes', $observacoes);

        $stmt->execute();

        echo "<script>
                alert('Procedimento cadastrado com sucesso!');
                window.location.href = 'novo.php';
              </script>";
    } catch (PDOException $e) {
        echo "<script>
                alert('Erro ao cadastrar: " . $e->getMessage() . "');
                window.location.href = 'novo.php';
              </script>";
    }
}
?>
