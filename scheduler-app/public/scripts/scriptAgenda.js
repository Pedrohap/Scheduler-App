document.addEventListener('DOMContentLoaded', () => {
  const formClienteAdd = document.getElementById('form_add_cliente');
  const formAgendaAdd = document.getElementById('form_add_agenda');
  const clienteInputId = document.getElementById('cliente_id');
  const clienteInputNome = document.getElementById('nome_completo_cliente');
  const telefoneInput = document.getElementById('telefone');
  const listaAutoCompleteClientes = document.getElementById('usuario_clientes');
  const divAgendas = document.getElementById('div_agendas');

  let nomesDisponiveis = [] // variavel auxiliar para gerenciar o auto completar
  
  // Função para exibir alertas (sucesso/erro)
  function mostrarAlerta(idElemento,tipo, mensagem) {
    let resultadoDiv = document.getElementById(idElemento);
    resultadoDiv.innerHTML = `
      <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
          class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
          <path
            d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
        </svg>
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>`;
  }

  // Função para buscar cliente e atualizar o input de nome
  async function carregarClienteId(){
    let idCliente = clienteInputId.value;
    if (idCliente.length > 0){
      try {
        const response = await fetch(`./getCliente/${idCliente}`);
        const dados = await response.json();

        if(dados.data.nome_completo){
          clienteInputNome.value = dados.data.nome_completo;
        } else {
          clienteInputNome.value = '';
        }
      } catch {
        console.log('Erro ao buscar dados.');
      }
    } else {
      clienteInputNome.value = '';
    }
  }

  // Função para buscar clientes e atualizar o <datalist>
  async function carregarClientesOnInput() {
    if (clienteInputNome.value.length >= 3 ){
      try {
        let nomeIncompleto = clienteInputNome.value;
        const response = await fetch(`./getClienteNameLike/${nomeIncompleto}`);
        const dados = await response.json();
        nomesDisponiveis = dados.data;
        listaAutoCompleteClientes.innerHTML = '';

        nomesDisponiveis.forEach(item => {
          const li = document.createElement('li');
          li.textContent = item.nome_completo;
          li.classList.add('list-group-item', 'list-group-item-action');
          li.addEventListener('click', () => {
            clienteInputNome.value = item.nome_completo;
            clienteInputId.value = item.id;
            listaAutoCompleteClientes.innerHTML = '';
          });
          listaAutoCompleteClientes.appendChild(li);
        });
      } catch {
        listaAutoCompleteClientes.innerHTML = '';
        console.log("Erro ao buscar dados.");
      }
    } else {
        nomesDisponiveis = [];
        listaAutoCompleteClientes.innerHTML = '';
    }
  }

  // Função para buscar clientes e atualizar o <datalist>
  async function checkClienteOnBlur() {
    setTimeout(() => {
      const nomeDigitado = clienteInputNome.value;
      if (nomesDisponiveis){
        const match = nomesDisponiveis.find(item => item.nome_completo === nomeDigitado);
        if (!match) {
          clienteInputNome.value = '';
          clienteInputId.value = '';
        }
      } else {
        clienteInputNome.value = '';
        clienteInputId.value = '';
      }

    }, 200); // delay para dar tempo suficiente para permitir o clique antes de apagar
  }

  // Função que carrega as agendas vinculadas ao usuario
  async function criaCardAgenga(agenda) {
    // Convertendo para formato ISO
    let isoDataInicial = agenda.data_inicial.replace(" ", "T");
    let isoDataFinal = agenda.data_final.replace(" ", "T");

    // Criando objetos Date (tratando como horário local)
    const dataInicial = new Date(isoDataInicial);
    const dataFinal = new Date(isoDataFinal);

    // Arrays com nomes de dias e meses
    const diasSemana = ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"];
    const meses = ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"];

    // Usando let para permitir alterações
    let diaSemana = diasSemana[dataInicial.getDay()];
    let mesAbreviado = meses[dataInicial.getMonth()];
    const diaDoMes = dataInicial.getDate();

    const horasInicias = dataInicial.getHours().toString().padStart(2, '0');
    const minutosIniciais = dataInicial.getMinutes().toString().padStart(2, '0');

    const horasFinais = dataFinal.getHours().toString().padStart(2, '0');
    const minutosFinais = dataFinal.getMinutes().toString().padStart(2, '0');

    // Verifica se o evento dura mais de um dia
    if (dataFinal.getDay() > dataInicial.getDay()) {
        diaSemana = `${diasSemana[dataInicial.getDay()]} - ${diasSemana[dataFinal.getDay()]}`;
    }

    // Verifica se o evento passa de mês
    if (dataFinal.getMonth() > dataInicial.getMonth()) {
        mesAbreviado = `${meses[dataInicial.getMonth()]} - ${meses[dataFinal.getMonth()]}`;
    }

    // HTML final
    const html = `
        <div class="col-3">
            <div class="card card-margin">
                <div class="card-header no-border">
                    <h5 class="card-title">${diaSemana}</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="widget-49">
                        <div class="widget-49-title-wrapper">
                            <div class="widget-49-date-primary">
                                <span class="widget-49-date-day">${diaDoMes}</span>
                                <span class="widget-49-date-month">${mesAbreviado}</span>
                            </div>
                            <div class="widget-49-meeting-info">
                                <span class="widget-49-pro-title">${agenda.titulo}</span>
                                <span class="widget-49-meeting-time">${horasInicias}:${minutosIniciais} até ${horasFinais}:${minutosFinais} Hrs</span>
                            </div>
                        </div>
                        <p class="widget-49-meeting-points" style="color: #727686;">
                            ${agenda.descricao}
                        </p>
                        <div class="widget-49-meeting-action">
                            <a href="#" class="btn btn-sm btn-flash-border-primary">Editar</a>
                            <button class="btn btn-sm btn-danger btn-excluir-agenda" data-id="${agenda.id}">Excluir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    `;

    return html;
  }

  // Exibe as agendas parao usuario quando a pagina é carregada
  async function carregaAgendas(){
    try {
      const response = await fetch('./getAgendas', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      });

      const resultado = await response.json();
      const data = resultado.data;

      divAgendas.innerHTML = '';

      const cardsHtml = await Promise.all(data.map(agenda => criaCardAgenga(agenda)));
      divAgendas.innerHTML = cardsHtml.join('');
    } catch (error) {
      console.log(error.message);
    }
  }

  // Submissão do formulário via fetch
  formClienteAdd.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(formClienteAdd);
    const dados = new URLSearchParams(formData);

    try {
      const response = await fetch('./addCliente', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: dados.toString()
      });

      const resultado = await response.json();

      mostrarAlerta('resultado_cliente',resultado.alertType, resultado.message);
    } catch (error) {
      mostrarAlerta('resultado_cliente','danger', `Ocorreu um erro: ${error.message}`);
    }
  });

    // Submissão do formulário via fetch
  formAgendaAdd.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(formAgendaAdd);
    const dados = new URLSearchParams(formData);

    try {
      const response = await fetch('./addAgenda', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: dados.toString()
      });

      const resultado = await response.json();

      mostrarAlerta('resultado_agenda',resultado.alertType, resultado.message);
    } catch (error) {
      mostrarAlerta('resultado_agenda','danger', `Ocorreu um erro: ${error.message}`);
    }
  });

  // Carregar clientes ao digitar no campo de id
  clienteInputId.addEventListener('input', carregarClienteId);
  clienteInputNome.addEventListener('input', carregarClientesOnInput);
  clienteInputNome.addEventListener('blur', checkClienteOnBlur);

  // Máscara de telefone
  telefoneInput.addEventListener('input', function (e) {
    let input = e.target.value.replace(/\D/g, '').substring(0, 11);

    if (input.length > 10) {
      input = input.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (input.length > 6) {
      input = input.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    } else if (input.length > 2) {
      input = input.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    } else {
      input = input.replace(/(\d{0,2})/, '($1');
    }

    e.target.value = input;
  });
  
  // Manejamento das Agendas
  carregaAgendas()

  let idParaExcluir = null;

document.addEventListener('click', function (event) {
  if (event.target.classList.contains('btn-excluir-agenda')) {
    event.preventDefault();
    idParaExcluir = event.target.dataset.id;
    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    modal.show();
  }
});

document.getElementById('confirmDeleteBtn').addEventListener('click', async function () {
  if (idParaExcluir) {
    try {
      const response = await fetch(`/deleteAgenda/${idParaExcluir}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      });

      if (response.ok) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
        modal.hide();
        carregaAgendas(); // Atualiza os cards
      } else {
        alert('Erro ao excluir a agenda.');
      }
    } catch (error) {
      alert('Erro na requisição: ' + error.message);
    }
    idParaExcluir = null;
  }
  });
});