<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversor HEIC para JPEG</title>
    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.3/dist/heic2any.min.js"></script>

</head>
<body>

    <h2>Selecione um arquivo HEIC para converter</h2>
    <input type="file" id="fileInput" accept=".heic, .heif">
    <br><br>
    <button id="convertButton" disabled>Converter e Enviar</button>
    <br><br>

    <h3>Imagem Convertida:</h3>
    <img id="convertedImage" alt="Imagem convertida" style="display:none; width: 300px; height: auto;">

    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.3/dist/heic2any.min.js"></script>
    <script>
        const fileInput = document.getElementById('fileInput');
        const convertButton = document.getElementById('convertButton');

        // Habilita o botão quando um arquivo HEIC for selecionado
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            // Verifica a extensão do arquivo selecionado
            if (file && (file.name.toLowerCase().endsWith('.heic') || file.name.toLowerCase().endsWith('.heif'))) {
                convertButton.disabled = false;
            } else {
                convertButton.disabled = true;
                alert('Selecione um arquivo HEIC válido.');
            }
        });

        // Converte a imagem quando o botão é clicado
        convertButton.addEventListener('click', async function() {
            const file = fileInput.files[0];

            try {
                // Converte o arquivo HEIC para JPEG
                const convertedBlob = await heic2any({
                    blob: file,
                    toType: 'image/jpeg',  // Mudar para 'image/png' se preferir PNG
                    quality: 0.8           // Ajustar a qualidade entre 0 e 1
                });

                // Exibe a imagem convertida
                const imgURL = URL.createObjectURL(convertedBlob);
                const convertedImage = document.getElementById('convertedImage');
                convertedImage.src = imgURL;
                convertedImage.style.display = 'block';

            } catch (error) {
                console.error('Erro ao converter HEIC:', error);
                alert('Falha na conversão do arquivo HEIC.');
            }
        });
    </script>

</body>
</html>
