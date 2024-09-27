<?php
require '../ConexaoBanco/conexao.php';

try {
    // Prepara a consulta para selecionar todos os dados da tabela Acessos
    $sql = "SELECT id, motivo, imagem FROM Acessos";
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
    <title>Visualizar Dados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
    </style>
</head>
<body>

    <h2>Dados Inseridos</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Motivo</th>
                <th>Imagem</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($resultados)): ?>
                <tr>
                    <td colspan="3">Nenhum dado encontrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($resultados as $resultado): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($resultado['id']); ?></td>
                        <td><?php echo htmlspecialchars($resultado['motivo']); ?></td>
                        <td>
                            <img src="data:image/png;base64,<?php echo base64_encode($resultado['imagem']); ?>" alt="Imagem" width="100" />
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
