<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envio de Arquivo com Webcam</title>
    <style>
        /* Estilos aqui (mesmo que antes) */
    </style>
</head>
<body>

    <div class="container">
        <h2>Enviar Arquivo</h2>
        <form id="form" enctype="multipart/form-data" method="POST" action="">
            <label for="motivo">Selecione o Motivo:</label>
            <select id="motivo" name="motivo">
                <option value="saude">Saúde</option>
                <option value="veteranos">Veteranos</option>
            </select>

            <label>Visualização da Webcam:</label>
            <div id="webcam-container">
                <video id="webcam" autoplay></video>
            </div>

            <button type="button" id="capture-btn">Capturar Foto</button>

            <canvas id="canvas" width="300" height="200"></canvas>

            <label for="arquivo">Escolha um Arquivo:</label>
            <input type="file" id="arquivo" name="arquivo" accept="image/*, .pdf, .doc, .docx">

            <button type="submit">Enviar</button>
        </form>

        <img id="captured-photo" alt="Foto capturada" />
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
                    alert("Não foi possível acessar a webcam.");
                });
        } else {
            alert("Seu navegador não suporta acesso à webcam.");
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
                capturedPhoto.style.display = 'block';  // Exibe a imagem
                alert('Foto capturada e inserida no campo de envio!');
            });
        });
    </script>

    <?php
    // Processa o envio do formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica se o arquivo foi enviado
        if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
            $motivo = $_POST['motivo'];
            $arquivo = $_FILES['arquivo'];

            // Caminho onde o arquivo será salvo
            $destino = 'uploads/' . basename($arquivo['name']);

            // Mover o arquivo para o diretório de destino
            if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
                echo "Arquivo enviado com sucesso! Motivo: $motivo";
            } else {
                echo "Erro ao enviar o arquivo.";
            }
        } else {
            echo "Nenhum arquivo foi enviado ou ocorreu um erro.";
        }
    }
    ?>

</body>
</html>
