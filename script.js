let perguntas = []; // Agora as perguntas serão obtidas do banco de dados
let indiceAtual = 0; // Controla o índice da pergunta atual
let respostas = []; // Array que armazena as respostas dos usuários
let respostaSelecionada = null; // Controla a resposta selecionada

// Função para mostrar a avaliação e iniciar o questionário
function mostrarAvaliacao() {
    document.getElementById('welcome').style.display = 'none';
    document.getElementById('avaliacao').style.display = 'block';
    obterPerguntas(); // Garante que as perguntas são carregadas antes de exibir a avaliação
}

// Função para obter perguntas do servidor
function obterPerguntas() {
    fetch('obter_perguntas.php')
        .then(response => response.json())
        .then(data => {
            console.log('Perguntas recebidas:', data);  // Adiciona um log aqui para verificar os dados
            perguntas = data.map(item => item.texto); // Ajusta a estrutura para seu uso
            atualizarPergunta(); // Atualiza para exibir a primeira pergunta
        })
        .catch(error => {
            console.error('Erro ao obter perguntas:', error);
            alert("Erro ao carregar perguntas.");
        });
}

// Função para atualizar a pergunta exibida
function atualizarPergunta() {
    // Desabilitar botões antes de configurar
    atualizarBotoes();
    
    // Verifica se ainda há perguntas a serem exibidas
    if (perguntas.length === 0) {
        alert("Nenhuma pergunta disponível.");
        return;
    }

    document.getElementById('pergunta').innerText = perguntas[indiceAtual];

    // Mostrar os botões
    document.getElementById('btnEnviar').style.display = (indiceAtual === perguntas.length - 1) ? 'block' : 'none';
    document.getElementById('btnRetroceder').style.display = (indiceAtual === 0) ? 'none' : 'block';
    document.getElementById('btnAvancar').style.display = (indiceAtual === perguntas.length - 1) ? 'none' : 'block';

    // Validação: Atualiza seleção de resposta ao clicar
    document.querySelectorAll('.unidade').forEach(botao => {
        botao.addEventListener('click', function() {
            // Remove a classe 'selecionado' de todos os botões
            document.querySelectorAll('.unidade').forEach(btn => btn.classList.remove('selecionado'));
            // Adiciona a classe 'selecionado' no botão clicado
            botao.classList.add('selecionado');
            respostaSelecionada = botao.value; // Atualiza a resposta selecionada
            atualizarBotoes(); // Atualiza os botões com base na seleção
        });
    });

    // Limpar a seleção anterior
    document.querySelectorAll('.unidade').forEach(b => b.classList.remove('selecionado'));
    respostaSelecionada = null; // Reinicia

    // Limpa o campo de feedback
    document.getElementById('feedback').value = '';
    
    // Atualiza os botões (desabilita ou habilita conforme necessário)
    atualizarBotoes();
}

// Atualiza os estados dos botões
function atualizarBotoes() {
    console.log('Resposta selecionada:', respostaSelecionada); // Verifique o valor da resposta
    document.getElementById('btnAvancar').disabled = respostaSelecionada === null; // Desabilita o botão avançar
    document.getElementById('btnEnviar').disabled = (indiceAtual === perguntas.length - 1 && respostaSelecionada === null); // Desabilita o botão enviar na última pergunta
}

// Função para avançar para a próxima pergunta
function avancarPergunta() {
    if (respostaSelecionada === null) {
        alert("Por favor, selecione uma resposta antes de avançar!");
        return;
    }
    respostas[indiceAtual] = respostaSelecionada; // Armazena a resposta atual
    indiceAtual++; // Vai para a próxima pergunta
    atualizarPergunta(); // Atualiza a pergunta a ser exibida
}

// Função para voltar para a pergunta anterior
function retrocederPergunta() {
    if (indiceAtual > 0) {
        indiceAtual--; // Retorna o índice (-1)
        respostaSelecionada = respostas[indiceAtual]; // Retorna a resposta de antes
        atualizarPergunta(); // Atualiza a pergunta a ser mostrada
    }
}


// Função para finalizar a avaliação
function finalizarAvaliacao() {
    // Coleta as respostas e feedback
    const botoes = document.querySelectorAll('.unidade.selecionado');
    const respostas = [];
    botoes.forEach((botao, index) => {
        respostas.push({
            perguntaId: index + 1, // Substitua isso pelo ID real da pergunta
            resposta: botao.value
        });
    });

    const feedback = document.getElementById('feedback').value;

    // Cria o corpo da requisição
    const data = {
        respostas: respostas,
        feedback: feedback
    };

    // Envia os dados via fetch
    fetch('salvar_respostas.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Avaliação enviada com sucesso!');
                window.location.href = 'pagina_agradecimento.html'; // Redireciona para a página de agradecimento
            } else {
                alert('Erro ao enviar a avaliação: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Ocorreu um erro ao enviar a avaliação.');
        });
}
