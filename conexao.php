<?php
// conexao.php
$host = 'localhost'; // Endereço do servidor PostgreSQL
$dbname = 'sistema_avaliacao'; // Nome do banco de dados
$user = 'postgres'; // Usuário do banco de dados
$password = 'postgres'; // Senha do banco de dados

try {
    // Estabelece a conexão com o banco de dados
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    die(); // Finaliza o script em caso de erro
}
?>
