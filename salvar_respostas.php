<?php
// salvar_resposta.php
$data = json_decode(file_get_contents('php://input'), true);

// Verifique se os dados foram recebidos corretamente
if (isset($data['pergunta']) && isset($data['resposta'])) {
    $pergunta = $data['pergunta'];
    $resposta = $data['resposta'];

    
    // Conectar ao banco de dados (ajuste com suas credenciais)
    $conn = new mysqli('localhost', 'root', '', 'nome_do_banco');

    if ($conn->connect_error) {
        die('Conexão falhou: ' . $conn->connect_error);
    }
    

    // Inserir a resposta no banco de dados
    $stmt = $conn->prepare("INSERT INTO respostas (pergunta_id, resposta) VALUES (?, ?)");
    $stmt->bind_param('is', $pergunta, $resposta);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'sucesso']);
    } else {
        echo json_encode(['status' => 'erro']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados ausentes']);
}
?>
