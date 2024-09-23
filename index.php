<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envio de Arquivo com Webcam</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-size: 14px;
            color: #333;
        }

        select, input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #webcam-container {
            margin: 15px 0;
            width: 100%;
            height: 200px;
            background-color: #000;
            border: 2px solid #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #canvas {
            display: none;
        }

        #captured-photo {
            display: none;
            margin-top: 20px;
            width: 100%;
            border: 2px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        #capture-btn {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Enviar Arquivo</h2>
        <form id="form" enctype="multipart/form-data">
            <label for="motivo">Selecione o Motivo:</label>
            <select id="motivo" name="motivo">
                <option value="saude">Saúde</option>
                <option value="veteranos">Veteranos</option>
            </select>

            <label>Visualização da Webcam:</label>
            <div id="webcam-container">
                <video id="webcam" autoplay></video>
            </div>

            <!-- Botão para capturar foto -->
            <button type="button" id="capture-btn">Capturar Foto</button>

            <canvas id="canvas" width="300" height="200"></canvas>

            <label for="arquivo">Escolha um Arquivo:</label>
            <input type="file" id="arquivo" name="arquivo" accept="image/*, .pdf, .doc, .docx">

            <button type="submit">Enviar</button>
        </form>

        <!-- Imagem capturada será mostrada aqui -->
        <img id="captured-photo" alt="Foto capturada" />
    </div>

    <script>
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('capture-btn');
        const fileInput = document.getElementById('arquivo');
        const form = document.getElementById('form');
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
            // Desenha o frame atual do vídeo no canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Converte o canvas para uma imagem em formato base64
            canvas.toBlob(function(blob) {
                // Cria um arquivo simulado a partir do blob
                const file = new File([blob], 'webcam-photo.png', { type: 'image/png' });

                // Atualiza o input de arquivo com o arquivo capturado
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                // Mostra a imagem capturada na tag <img>
                const imageUrl = URL.createObjectURL(blob);
                capturedPhoto.src = imageUrl;
                capturedPhoto.style.display = 'block';  // Exibe a imagem
                alert('Foto capturada e inserida no campo de envio!');
            });
        });

        // Evento de envio do formulário
        form.addEventListener('submit', function(e) {
            // Aqui você pode adicionar mais lógica se necessário antes de enviar
            alert('Formulário enviado com a foto capturada!');
        });
    </script>

</body>
</html>
