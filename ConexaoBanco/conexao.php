<?php
// Configurações do banco de dados
$host = 'localhost';    // Host do banco de dados (geralmente localhost)
$dbname = 'diasfastsys';   // Nome do banco de dados
$user = 'root';          // Nome de usuário do MySQL
$pass = '';              // Senha do MySQL

try {
    // Criando uma nova instância de PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

    // Configura o modo de erro para lançar exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<script>Conexão com o banco de dados bem-sucedida!</script>";
} catch (PDOException $e) {
    // Caso ocorra algum erro na conexão, ele será capturado aqui
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    exit;
}
?>
