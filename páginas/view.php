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
        img {
            cursor: pointer;
        }
        button.delete-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        
        /* Estilos do modal */
        .modal {
            display: none; /* Oculta o modal por padrão */
            position: fixed; /* Fixa o modal na tela */
            z-index: 1000; /* Fica acima de outros conteúdos */
            left: 0;
            top: 0;
            width: 100%; /* Largura total */
            height: 100%; /* Altura total */
            overflow: auto; /* Habilita scroll se necessário */
            background-color: rgba(0, 0, 0, 0.7); /* Fundo escuro com transparência */
        }
        
        .modal-content {
            margin: 15% auto; /* Centraliza verticalmente e horizontalmente */
            padding: 20px;
            width: 80%; /* Largura do conteúdo do modal */
            max-width: 700px; /* Largura máxima */
            background-color: white; /* Fundo branco */
            border-radius: 5px; /* Bordas arredondadas */
            text-align: center; /* Centraliza o texto */
        }
        
        .modal-content img {
            width: 100%; /* Imagem ocupa 100% da largura do conteúdo */
            border-radius: 5px; /* Bordas arredondadas da imagem */
        }

        .close {
            color: #aaa; /* Cor do botão de fechar */
            float: right; /* Posiciona à direita */
            font-size: 28px; /* Tamanho da fonte */
            font-weight: bold; /* Negrito */
            cursor: pointer; /* Muda o cursor */
        }

        .close:hover,
        .close:focus {
            color: black; /* Muda a cor ao passar o mouse */
            text-decoration: none; /* Remove o sublinhado */
            cursor: pointer; /* Muda o cursor */
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
                            <img src="data:image/png;base64,<?php echo base64_encode($resultado['imagem']); ?>" alt="Imagem" width="100" onclick="openModal(this)" />
                        </td>
                        <td><?php echo htmlspecialchars($resultado['data_hora']); ?></td>
                        <td><?php echo htmlspecialchars($resultado['observacoes']); ?></td>
                        <td><?php echo htmlspecialchars($resultado['horaBaixa']); ?></td>
                        <td>
                            <form method="POST" action="excluir.php">
                                <input type="hidden" name="delete_id" value="<?php echo $resultado['id']; ?>" />
                                <button type="submit" class="delete-btn">Remover</button>
                            </form>
                            <form action="darBaixa.php" method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($resultado['id']); ?>">
                                <button type="submit" style="background-color: #00ffff; color: white; border: none; padding: 5px 10px; border-radius: 5px;">Dar Baixa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal para exibir a imagem em tamanho grande -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="modalImage" src="" alt="Imagem em tamanho grande">
        </div>
    </div>

    <script>
        // Função para abrir o modal e exibir a imagem
        function openModal(imgElement) {
            var modal = document.getElementById('imageModal');
            var modalImg = document.getElementById('modalImage');
            modal.style.display = 'block'; // Muda a exibição para block
            modalImg.src = imgElement.src; // Usa o mesmo src da imagem clicada
        }

        // Função para fechar o modal
        function closeModal() {
            var modal = document.getElementById('imageModal');
            modal.style.display = 'none'; // Oculta o modal
        }

        // Fecha o modal se o usuário clicar fora do conteúdo
        window.onclick = function(event) {
            var modal = document.getElementById('imageModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>

</body>
</html>
