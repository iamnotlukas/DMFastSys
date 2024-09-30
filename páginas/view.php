<?php
require '../ConexaoBanco/conexao.php';

try {
    // Prepara a consulta para selecionar todos os dados da tabela Acessos
    $sql = "SELECT id, motivo, imagem, data_hora, observacoes, horaBaixa FROM Acessos";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Obtém todos os resultados
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
    $resultados = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="viewstyle.css">
    <title>Visualizar Dados</title>
</head>
<body>

    <h2>Dados Inseridos</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Motivo</th>
                <th>Imagem</th>
                <th>Data e Hora</th>
                <th>Observações</th>
                <th>Hora da Baixa</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($resultados)): ?>
                <tr>
                    <td colspan="7">Nenhum dado encontrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($resultados as $resultado): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($resultado['id']); ?></td>
                        <td><?php echo htmlspecialchars($resultado['motivo']); ?></td>
                        <td>
                            <img src="data:image/png;base64,<?php echo base64_encode($resultado['imagem']); ?>" alt="Imagem" onclick="showLargeImage(this, <?php echo htmlspecialchars(json_encode($resultado['imagem'])); ?>)" />
                        </td>
                        <td><?php echo htmlspecialchars($resultado['data_hora']); ?></td>
                        <td><?php echo htmlspecialchars($resultado['observacoes']); ?></td>
                        <td><?php echo htmlspecialchars($resultado['horaBaixa']); ?></td>
                        <td>
                            <form method="POST" action="excluir.php">
                                <input type="hidden" name="delete_id" value="<?php echo $resultado['id']; ?>" />
                                <button type="submit" style="background-color: #FF0000; color: white; border: none; padding: 5px 10px; border-radius: 5px;" class="delete-btn">Remover</button>
                            </form>
                            <form action="darBaixa.php" method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($resultado['id']); ?>">
                                <button type="submit" style="background-color: green; color: white; border: none; padding: 5px 10px; border-radius: 5px;">Dar Baixa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
