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
            $fileExtension = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));

            // Verifica se o arquivo é uma imagem ou vídeo
            if (strpos($fileType, 'image') !== false || $fileExtension === 'jpg' || $fileExtension === 'jpeg' || $fileExtension === 'png') {
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
    <script src="https://cdn.jsdelivr.net/npm/heic2any@latest/dist/heic2any.min.js"></script>
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
                <input type="file" id="arquivo" name="arquivo" accept="image/*,video/*,.heic,.pdf,.doc,.docx">

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

<script src="https://cdn.jsdelivr.net/npm/heic2any@latest/dist/heic2any.min.js"></script>

<script>
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('capture-btn');
    const fileInput = document.getElementById('arquivo');
    const capturedPhoto = document.getElementById('captured-photo');
    const form = document.getElementById('form');

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

    // Função para converter qualquer formato de imagem para JPEG
    async function convertToJpeg(file) {
        try {
            const mimeType = file.type;
            if (mimeType === 'image/jpeg') {
                return file;  // Se já for JPEG, retorna o arquivo original
            } else if (mimeType === 'image/heic') {
                const convertedBlob = await heic2any({
                    blob: file,
                    toType: 'image/jpeg',
                    quality: 0.7
                });
                return new File([convertedBlob], 'converted-image.jpg', { type: 'image/jpeg' });
            } else {
                const img = new Image();
                img.src = URL.createObjectURL(file);
                return new Promise((resolve, reject) => {
                    img.onload = async () => {
                        const canvas = document.createElement('canvas');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);
                        canvas.toBlob(blob => {
                            const jpegFile = new File([blob], 'converted-image.jpg', { type: 'image/jpeg' });
                            resolve(jpegFile);
                        }, 'image/jpeg', 0.7);
                    };
                    img.onerror = (error) => {
                        console.error('Erro ao carregar a imagem:', error);
                        alert('Erro ao carregar a imagem. O arquivo pode estar corrompido ou em um formato não suportado.');
                        reject(new Error('Erro ao carregar a imagem.'));
                    };
                });
            }
        } catch (error) {
            console.error('Erro na conversão para JPEG:', error);
            alert('Erro ao converter a imagem. Tente novamente.');
            return null;
        }
    }

    // Quando o arquivo for selecionado
    fileInput.addEventListener('change', async function () {
        const selectedFile = this.files[0];
        if (selectedFile) {
            const convertedFile = await convertToJpeg(selectedFile);
            if (convertedFile) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(convertedFile);
                fileInput.files = dataTransfer.files;
                alert('Imagem convertida para JPEG e pronta para envio!');
            } else {
                alert('Falha na conversão da imagem.');
            }
        }
    });

    // Envio do formulário com a imagem já convertida
    form.addEventListener('submit', async function (event) {
        const selectedFile = fileInput.files[0];
        if (selectedFile && selectedFile.type !== 'image/jpeg') {
            event.preventDefault();  // Previne o envio enquanto converte
            const convertedFile = await convertToJpeg(selectedFile);
            if (convertedFile) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(convertedFile);
                fileInput.files = dataTransfer.files;
                form.submit();  // Reenvia o formulário com o arquivo convertido
            } else {
                alert('Falha na conversão da imagem.');
            }
        }
    });

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
