<?php
require '../ConexaoBanco/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // Atualiza a coluna horaBaixa com a hora atual para o registro com o ID fornecido
        $sql = "UPDATE Acessos SET horaBaixa = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Baixa registrada com sucesso!'); window.location.href='view.php';</script>";
        } else {
            echo "<script>alert('Erro ao registrar a baixa.'); window.location.href='view.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Erro: " . $e->getMessage() . "'); window.location.href='view.php';</script>";
    }
} else {
    echo "<script>alert('ID não fornecido.'); window.location.href='view.php';</script>";
}
?>
