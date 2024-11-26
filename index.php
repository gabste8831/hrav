<?php
// Inclui a conexão ao banco de dados
require 'conexao.php';

// Função para obter perguntas do banco de dados

function obterPerguntas() {
    global $conn; // Usa a variável de conexão global
    $query = "SELECT texto FROM perguntas"; // Consulta para obter as perguntas
    try {
        $stmt = $conn->query($query); // Executa a consulta
        $perguntas = $stmt->fetchAll(PDO::FETCH_COLUMN); // Obtém as perguntas como um array
        return $perguntas; // Retorna o array de perguntas
    } catch (PDOException $e) {
        echo "Erro na consulta: " . $e->getMessage(); // Tratamento de erro
        return []; // Retorna um array vazio em caso de erro
    }
}


// Obtém as perguntas do banco de dados
$perguntas = obterPerguntas();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Avaliação de Prestação de Serviços HRAV</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <script>
        let perguntas = <?php echo json_encode($perguntas); ?>; // Converte as perguntas para JavaScript
        let indiceAtual = 0; // Indice da pergunta atual
        let notaSelecionada = null; // Variável para armazenar a nota selecionada

        function mostrarAvaliacao() {
            document.getElementById('welcome').style.display = 'none';
            document.getElementById('avaliacao').style.display = 'block';
            mostrarPergunta();
        }

        function mostrarPergunta() {
            const perguntaEl = document.getElementById('pergunta');
            const btnRetroceder = document.getElementById('btnRetroceder');
            const btnAvancar = document.getElementById('btnAvancar');
            const btnEnviar = document.getElementById('btnEnviar');
            const feedbackEl = document.getElementById('feedback'); // Campo de feedback

            if (indiceAtual < perguntas.length) {
                perguntaEl.innerText = perguntas[indiceAtual]; // Exibe a pergunta atual

                // Controla a visibilidade dos botões
                btnRetroceder.style.display = indiceAtual === 0 ? 'none' : 'inline-block';
                btnAvancar.style.display = indiceAtual === perguntas.length - 1 ? 'none' : 'inline-block';
                btnEnviar.style.display = indiceAtual === perguntas.length - 1 ? 'inline-block' : 'none';
            }

            limparSelecao(); // Limpa a seleção anterior de notas
            feedbackEl.value = ''; // Limpa o campo de feedback
        }

        function validarEscolha() {
            if (notaSelecionada !== null) {
                return true; // Nota foi escolhida
            }
            alert('Por favor, escolha uma nota antes de prosseguir.');
            return false;
        }
        let perguntaIndex = 0;

function mostrarProximaPergunta() {
  const perguntas = document.querySelectorAll('.pergunta');
  if (perguntaIndex < perguntas.length) {
    perguntas[perguntaIndex].style.display = 'block';  // Exibe a pergunta atual
    document.querySelector('#btnProxima').disabled = false;  // Habilita o botão de próxima
  } else {
    alert('Você completou todas as perguntas!');
  }
}

document.querySelector('#btnProxima').addEventListener('click', () => {
  salvarResposta();  // Salva a resposta e mostra a próxima pergunta
  perguntaIndex++;   // Avança para a próxima pergunta
});

        function retrocederPergunta() {
            if (indiceAtual > 0) {
                indiceAtual--; // Volta para a pergunta anterior
                mostrarPergunta(); // Atualiza a exibição
                notaSelecionada = null; // Reseta a seleção para a pergunta anterior
            }
        }
      

        // Função para selecionar a nota
        function selecionarNota(nota) {
            notaSelecionada = nota;
            // Adiciona a classe 'selecionado' para destacar a seleção
            const botoes = document.querySelectorAll('.unidade');
            botoes.forEach((botao) => {
                botao.classList.remove('selecionado');
            });
            const botaoSelecionado = document.querySelector(`button[value='${nota}']`);
            botaoSelecionado.classList.add('selecionado');
        }

        // Limpa a seleção de notas
        function limparSelecao() {
            const botoes = document.querySelectorAll('.unidade');
            botoes.forEach((botao) => {
                botao.classList.remove('selecionado');
            });
        }

        function enviarResposta() {
    const feedback = document.getElementById('feedback').value;
    const resposta = notaSelecionada;
    const perguntaId = indiceAtual + 1; // A ID da pergunta (baseado no índice atual)
    
    // Enviar a resposta para o PHP via AJAX
    const formData = new FormData();
    formData.append('id_pergunta', perguntaId);
    formData.append('resposta', resposta);
    formData.append('feedback', feedback);

    fetch('salvar_resposta.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('Resposta salva:', data); // Apenas para depuração
        // Avançar para a próxima pergunta
        avancarPergunta();
    })
    .catch(error => {
        console.error('Erro ao salvar resposta:', error);
    });
}


        function finalizarAvaliacao() {
    if (!validarEscolha()) return; // Garante que a nota foi escolhida

    let respostas = [];
    let feedback = document.getElementById('feedback').value;

    // Coleta as respostas das perguntas
    for (let i = 0; i < perguntas.length; i++) {
        let resposta = {
            perguntaId: i + 1, // Supondo que a pergunta no banco de dados tenha um ID incremental
            resposta: document.querySelector(`button[data-index='${i}'].selecionado`)?.value || null
        };
        respostas.push(resposta);
    }

    // Enviar as respostas via AJAX para o PHP
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'salvar_respostas.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert('Avaliação enviada com sucesso!');
            window.location.href = 'pagina_de_confirmacao.php'; // Redireciona para uma página de confirmação, por exemplo
        }
    };
    xhr.send(JSON.stringify({ respostas: respostas, feedback: feedback }));
}


    </script>
</head>

<body>
    <!-- Seção de Boas-vindas -->
    <section id="welcome">
        <h1 class="titulo">Bem-vindo ao Sistema de Avaliação do HRAV</h1>
        <p class="subtitulo">Clique no botão para iniciar sua avaliação.</p>
        <button class="btn" id="btnIniciar" onclick="mostrarAvaliacao()">Iniciar Avaliação</button>
    </section>

    <!-- Seção de Avaliação -->
    <section id="avaliacao" style="display:none;">
        <form id="formAvaliacoes" action="POST">
            <?php if (empty($perguntas)): ?>
                <p class="titulo">Nenhuma pergunta disponível para avaliação.</p>
                <button class="btn" type="button" onclick="alert('Não há perguntas disponíveis!')">Voltar</button>
            <?php else: ?>
                <p class="titulo" id="pergunta"></p>
                <div class="botoes_avaliacao">
    <button class="unidade" type="button" value="1" onclick="selecionarNota(1)" data-index="0">1</button>
    <button class="unidade" type="button" value="2" onclick="selecionarNota(2)" data-index="0">2</button>
    <button class="unidade" type="button" value="3" onclick="selecionarNota(3)" data-index="0">3</button>
    <button class="unidade" type="button" value="4" onclick="selecionarNota(4)" data-index="0">4</button>
    <button class="unidade" type="button" value="5" onclick="selecionarNota(5)" data-index="0">5</button>
    <button class="unidade" type="button" value="6" onclick="selecionarNota(6)" data-index="0">6</button>
    <button class="unidade" type="button" value="7" onclick="selecionarNota(7)" data-index="0">7</button>
    <button class="unidade" type="button" value="8" onclick="selecionarNota(8)" data-index="0">8</button>
    <button class="unidade" type="button" value="9" onclick="selecionarNota(9)" data-index="0">9</button>
    <button class="unidade" type="button" value="10" onclick="selecionarNota(10)" data-index="0">10</button>
</div>


                <div class="btn_container">
                    <button class="btn" type="button" id="btnRetroceder" onclick="retrocederPergunta()" style="display:none;">Retroceder</button>
                    <button class="btn" type="button" id="btnAvancar" onclick="avancarPergunta()">Avançar</button>
                    <button class="btn" type="button" id="btnEnviar" style="display:none;" onclick="finalizarAvaliacao()">Enviar Avaliação</button>

                </div>
                <p class="comentario">Comentários adicionais (opcional):</p>
                <textarea id="feedback" rows="4" cols="50"></textarea>
            <?php endif; ?>
        </form>
        <footer>
            <p class="mensagem_footer">Sua avaliação espontânea é anônima, nenhuma informação pessoal é solicitada ou armazenada.</p>
        </footer>
    </section>
</body>

</html>
