<?php
require '../ConexaoBanco/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    try {
        $sql = "DELETE FROM Acessos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $delete_id);
        $stmt->execute();

        echo "<script>alert('Registro removido com sucesso!'); window.location.href = 'view.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Erro ao remover registro: " . $e->getMessage() . "'); window.location.href = 'view.php';</script>";
    }
} else {
    // Redireciona de volta para a página principal caso o acesso seja inválido
    header("Location: view.php");
    exit;
}
?>
