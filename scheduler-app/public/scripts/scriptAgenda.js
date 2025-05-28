document.addEventListener('DOMContentLoaded', () => {
  const formClienteAdd = document.getElementById('form_add_cliente');
  const formAgendaAdd = document.getElementById('form_add_agenda');
  const formAgendaFiltro = document.getElementById('filtro-agendas');
  const clienteInputId = document.getElementById('cliente_id');
  const clienteInputNome = document.getElementById('nome_completo_cliente');
  const clienteInputIdFiltro = document.getElementById('cliente_id_filtro');
  const clienteInputNomeFiltro = document.getElementById('nome_completo_filtro');
  const telefoneInput = document.getElementById('telefone');
  const listaAutoCompleteClientes = document.getElementById('usuario_clientes');
  const listaAutoCompleteClientesFiltro = document.getElementById('usuario_clientes_filtro');
  const divAgendas = document.getElementById('div_agendas');
  const btnAgendaAdd = document.getElementById('add_agenda_btn');

  let nomesDisponiveis = [] // variavel auxiliar para gerenciar o auto completar
  let nomesDisponiveisFiltro = []; // variavel auxiliar para gerenciar o auto completar no filtro
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

  //Função para arrumar provisioramente o problema de incompatibilidade de UTC-Time
  function formatarParaDatetimeLocal(dataStr) {
    const [data, hora] = dataStr.split(' ');
    const [ano, mes, dia] = data.split('-');
    const [horaH, minuto] = hora.split(':');

    // Monta em formato que o input datetime-local aceita (yyyy-MM-ddTHH:mm)
    return `${ano}-${mes}-${dia}T${horaH}:${minuto}`;
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

  async function carregarClienteIdFiltro(){
    let idCliente = clienteInputIdFiltro.value;
    if (idCliente.length > 0){
      try {
        const response = await fetch(`./getCliente/${idCliente}`);
        const dados = await response.json();

        if(dados.data.nome_completo){
          clienteInputNomeFiltro.value = dados.data.nome_completo;
        } else {
          clienteInputNomeFiltro.value = '';
        }
      } catch {
        console.log('Erro ao buscar dados.');
      }
    } else {
      clienteInputNomeFiltro.value = '';
    }
  }

  async function carregarClientesOnInputFiltro() {
    if (clienteInputNomeFiltro.value.length >= 3 ){
      try {
        let nomeIncompleto = clienteInputNomeFiltro.value;
        const response = await fetch(`./getClienteNameLike/${nomeIncompleto}`);
        const dados = await response.json();
        nomesDisponiveisFiltro = dados.data;
        listaAutoCompleteClientesFiltro.innerHTML = '';

        nomesDisponiveisFiltro.forEach(item => {
          const li = document.createElement('li');
          li.textContent = item.nome_completo;
          li.classList.add('list-group-item', 'list-group-item-action');
          li.addEventListener('click', () => {
            clienteInputNomeFiltro.value = item.nome_completo;
            clienteInputIdFiltro.value = item.id;
            listaAutoCompleteClientesFiltro.innerHTML = '';
          });
          listaAutoCompleteClientesFiltro.appendChild(li);
        });
      } catch {
        listaAutoCompleteClientesFiltro.innerHTML = '';
        console.log("Erro ao buscar dados.");
      }
    } else {
        nomesDisponiveisFiltro = [];
        listaAutoCompleteClientesFiltro.innerHTML = '';
    }
  }

function checkClienteOnBlurFiltro() {
  setTimeout(() => {
    const nomeDigitado = clienteInputNomeFiltro.value;
    if (nomesDisponiveisFiltro){
      const match = nomesDisponiveisFiltro.find(item => item.nome_completo === nomeDigitado);
      if (!match) {
        clienteInputNomeFiltro.value = '';
        clienteInputIdFiltro.value = '';
      }
    } else {
      clienteInputNomeFiltro.value = '';
      clienteInputIdFiltro.value = '';
    }
  }, 200);
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
    let diaDoMes = dataInicial.getDate();

    const horasInicias = dataInicial.getHours().toString().padStart(2, '0');
    const minutosIniciais = dataInicial.getMinutes().toString().padStart(2, '0');

    const horasFinais = dataFinal.getHours().toString().padStart(2, '0');
    const minutosFinais = dataFinal.getMinutes().toString().padStart(2, '0');

    // Verifica se o evento dura mais de um dia
    if (dataFinal.getDay() > dataInicial.getDay()) {
        diaSemana = `${diasSemana[dataInicial.getDay()]} - ${diasSemana[dataFinal.getDay()]}`;
        diaDoMes =`${dataInicial.getDate()}-${dataFinal.getDate()}`
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
                                <span class="widget-49-pro-title"><strong>${agenda.titulo}</strong></span>
                                <span class="widget-49-pro-title">${agenda.nome_cliente}</span>
                                <span class="widget-49-meeting-time">${horasInicias}:${minutosIniciais} até ${horasFinais}:${minutosFinais} Hrs</span>
                            </div>
                        </div>
                        <p class="widget-49-meeting-points" style="color: #727686;">
                            ${agenda.descricao}
                        </p>
                        <div class="widget-49-meeting-action">
                            <button class="btn btn-sm btn-secondary me-1 btn-editar-agenda" data-id="${agenda.id}">Editar</a>
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

  // Muda o titulo do modal caso o usuario deseje adicionar uma Agenda e limpa o form
  btnAgendaAdd.addEventListener('click', async (e) =>{
    document.getElementById('agendaModalLabel').innerHTML = "Adicionar Agenda";
    document.getElementById('btn-agenda-submit').innerHTML = "Adicionar";
    formAgendaAdd.reset();
    formAgendaAdd.querySelector('#agenda_id').value = '';
  });

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

    const agendaId = formAgendaAdd.querySelector('#agenda_id').value;

    const url = agendaId ? `./updateAgenda/${agendaId}` : './addAgenda';

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: dados.toString()
      });

      const resultado = await response.json();

      mostrarAlerta('resultado_agenda', resultado.alertType, resultado.message);

      if (resultado.success) {
        // Fecha modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('agendaModal'));
        modal.hide();

        // Recarrega com ou sem filtro
        const algumFiltroAtivo = Array.from(formAgendaFiltro.elements).some(el => el.value.trim() !== '');
        if (algumFiltroAtivo) {
          formAgendaFiltro.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        } else {
          carregaAgendas();
        }

        // Limpa o campo hidden (importante!)
        formAgendaAdd.querySelector('#agenda_id').value = '';
      }
    } catch (error) {
      mostrarAlerta('resultado_agenda', 'danger', `Erro: ${error.message}`);
    }
  });

  // Carregar clientes de acordo com o auto completar
  clienteInputId.addEventListener('input', carregarClienteId);
  clienteInputNome.addEventListener('input', carregarClientesOnInput);
  clienteInputNome.addEventListener('blur', checkClienteOnBlur);
  clienteInputIdFiltro.addEventListener('input', carregarClienteIdFiltro);
  clienteInputNomeFiltro.addEventListener('input', carregarClientesOnInputFiltro);
  clienteInputNomeFiltro.addEventListener('blur', checkClienteOnBlurFiltro);

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

  // Submissão do formulário via fetch
  formAgendaFiltro.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(formAgendaFiltro);
    const dados = new URLSearchParams(formData).toString();

    try {
      const response = await fetch(`./getAgendasFiltros?${dados}`, {
        method: 'GET'
      });

      const resultado = await response.json();

      const data = resultado.data;

      divAgendas.innerHTML = '';

      const cardsHtml = await Promise.all(data.map(agenda => criaCardAgenga(agenda)));
      divAgendas.innerHTML = cardsHtml.join('');
    } catch (error) {
      console.log(error);
    }
  });
  
  // Manejamento das Agendas

  // Trexo de eventos do botão Editar e excluir funcionar
  let idParaExcluir = null;

  // Delegação de evento para o botão excluir nos cards
  document.addEventListener('click', async function (event) {
  
    // Botão clicado foi Excluir
  if (event.target.classList.contains('btn-excluir-agenda')) {
    event.preventDefault();
    idParaExcluir = event.target.dataset.id;
    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    modal.show();
  }

  // Botão clicado foi Editar
  if (event.target.classList.contains('btn-editar-agenda')) {
    event.preventDefault();
    const id = event.target.dataset.id;

    try {
      const response = await fetch(`./getAgenda/${id}`);
      const resultado = await response.json();

      if (resultado.success && resultado.data) {
        const dados = resultado.data[0];

        // Preencher os campos do modal
        document.getElementById('agenda_id').value = dados.id;
        document.getElementById('titulo').value = dados.titulo;
        document.getElementById('descricao').value = dados.descricao;
        document.getElementById('cliente_id').value = dados.cliente_id;
        document.getElementById('nome_completo_cliente').value = dados.nome_cliente ?? '';

        // Converter datas
        if (dados.data_inicial && dados.data_final) {
          const dataIni = new Date(dados.data_inicial.replace(' ', 'T'));
          const dataFim = new Date(dados.data_final.replace(' ', 'T'));

          document.getElementById('data_inicial').value = formatarParaDatetimeLocal(dados.data_inicial);
          document.getElementById('data_final').value = formatarParaDatetimeLocal(dados.data_final);
        } else {
          alert('Datas não encontradas');
        }

        // Muda título do modal e do botão
        document.getElementById('agendaModalLabel').innerHTML = "Editar Agenda";
        document.getElementById('btn-agenda-submit').innerHTML = "Salvar";

        // Abre modal
        const modal = new bootstrap.Modal(document.getElementById('agendaModal'));
        modal.show();

      } else {
        alert('Erro ao buscar dados da agenda.');
      }
    } catch (error) {
      alert('Erro ao carregar agenda: ' + error.message);
    }
  }
});

  // Confirmar exclusão no modal
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
          // Verifica se existe filtro ativo
          const algumFiltroAtivo = Array.from(formAgendaFiltro.elements).some(el => el.value.trim() !== '');

          if (algumFiltroAtivo) {
            // Reenvia o formulário de filtro para atualizar a lista com base nos filtros
            formAgendaFiltro.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
          } else {
            // Remove o card do DOM diretamente
            const card = document.querySelector(`.btn-excluir-agenda[data-id="${idParaExcluir}"]`).closest('.col-3');
            if (card) card.remove();
          }
        } else {
          alert('Erro ao excluir a agenda.');
        }
      } catch (error) {
        alert('Erro na requisição: ' + error.message);
      }
      idParaExcluir = null;
    }
  });

  carregaAgendas()

});