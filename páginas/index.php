<?php
session_start();  // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');  // Redireciona para a página de login
    exit();
}

require '../ConexaoBanco/conexao.php';

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['motivo']) && isset($_FILES['arquivo'])) {
        $motivo = $_POST['motivo'];
        $observacoes = isset($_POST['observacoes']) ? $_POST['observacoes'] : null;

        // Verifica se houve erro no upload
        if ($_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
            $fileType = mime_content_type($_FILES['arquivo']['tmp_name']);
            
            // Verifica se o arquivo é uma imagem ou vídeo
            if (strpos($fileType, 'image') !== false) {
                // Processa como imagem
                $imagem = file_get_contents($_FILES['arquivo']['tmp_name']);
            } elseif (strpos($fileType, 'video') !== false) {
                // Processa como vídeo
                $imagem = file_get_contents($_FILES['arquivo']['tmp_name']);
            } else {
                echo "<script>alert('Tipo de arquivo não suportado. Apenas imagens e vídeos são permitidos.');</script>";
                exit();
            }

            try {
                $sql = "INSERT INTO Acessos (motivo, imagem, observacoes) VALUES (:motivo, :imagem, :observacoes)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':motivo', $motivo);
                $stmt->bindParam(':imagem', $imagem, PDO::PARAM_LOB);
                $stmt->bindParam(':observacoes', $observacoes);

                if ($stmt->execute()) {
                    echo "<script>alert('Dados inseridos com sucesso!');</script>";
                } else {
                    echo "<script>alert('Erro ao inserir os dados.');</script>";
                }
            } catch (PDOException $e) {
                echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
            }
        } else {
            echo "<script>alert('Erro no upload do arquivo.');</script>";
        }
    } else {
        echo "<script>alert('Motivo ou arquivo não enviado corretamente.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envio de Arquivo com Webcam</title>
    <link rel="stylesheet" href="style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">
    <img src="../imagens/logoMarinha.png" style="width: 13%;">
    <h2>Enviar Arquivo</h2>
    <div class="content">
        <div class="left-column">
            <label>Visualização da Webcam:</label>
            <div id="webcam-container">
                <video id="webcam" autoplay></video>
            </div>
            <button type="button" id="capture-btn">Capturar Foto</button>
            <canvas id="canvas" width="300" height="200"></canvas>
            <img id="captured-photo" alt="Foto capturada" />
        </div>

        <div class="right-column">
            <form id="form" enctype="multipart/form-data" method="POST" action="">
                <label for="motivo">Selecione o Motivo:</label>
                <select id="motivo" name="motivo">
                    <option value="saude">Saúde</option>
                    <option value="veteranos">Veteranos</option>
                    <option value="reuniao">Reunião</option>
                    <option value="acompanhar">Acompanhar</option>
                    <option value="empresa">Empresa</option>
                    <option value="outros">Outros</option>
                </select>

                <label for="arquivo">Escolha um Arquivo:</label>
                <input type="file" id="arquivo" name="arquivo" accept="image/*,video/*,.pdf,.doc,.docx">

                <label for="observacoes">Observações (máx. 20 caracteres):</label>
                <input type="text" id="observacoes" name="observacoes" maxlength="20">

                <div class="botoes" style="margin: 5% auto;">
                    <button type="submit">Enviar</button>
                    <a href="view.php" style="text-decoration: none;">
                        <button type="button" style="background-color: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Ver Registros</button>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <form method="POST" action="logout.php">
        <button class="logout-btn" type="submit" style="
            width: fit-content;
            background: orange;
            margin: 0 auto;">Logout</button>
    </form>
    <h5>Desenvolvido por MN-RC DIAS 24.0729.23</h5>
</div>

<script>
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('capture-btn');
    const fileInput = document.getElementById('arquivo');
    const capturedPhoto = document.getElementById('captured-photo');

    // Acessar a webcam
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                video.srcObject = stream;
                video.play();
            })
            .catch(function(err) {
                console.error("Erro ao acessar a webcam: ", err);
                alert("Não foi possível acessar a webcam. Você pode escolher um arquivo.");
            });
    } else {
        alert("Seu navegador não suporta acesso à webcam. Use o campo de seleção de arquivo.");
    }

    // Função para capturar a foto e colocá-la no input de arquivo
    captureBtn.addEventListener('click', function() {
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(function(blob) {
            const file = new File([blob], 'webcam-photo.png', { type: 'image/png' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            const imageUrl = URL.createObjectURL(blob);
            capturedPhoto.src = imageUrl;
            capturedPhoto.style.display = 'block';
            alert('Foto capturada e inserida no campo de envio!');
        });
    });
</script>

</body>
</html>
