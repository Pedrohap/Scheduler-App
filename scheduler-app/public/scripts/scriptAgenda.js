
// JQuery para gerenciar as requisições AJAX
$(document).ready(function () {
  $("#form_add_cliente").submit(function (e) {
    e.preventDefault(); // Impede o envio padrão do formulário

    var dados = $(this).serialize();

    // Fazendo uma requisição GET para uma API pública
    $.ajax({
      url: "./addCliente", // URL da API
      type: "POST", // Tipo de requisição
      data: dados,
      success: function (dados) {
        $("#resultado_cliente").html(`
            <div class="alert alert-${dados.alertType} alert-dismissible fade show" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                </svg>
            ${dados.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
      },
      error: function (xhr, status, erro) {
        // Erro: mostra mensagem de erro
        $("#resultado_cliente").html(`
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                </svg>
            Ocorreu um erro: ${erro}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
      },
    });
  });

  $('#add_agenda_btn').on('click', function () {

      $.ajax({
        url: './getClientes',
        method: 'GET',
        success: function (dados) {
            let result = dados.data;
            let html = '';

          // Suponha que a API retorne um array de resultados
          result.forEach(function (item) {
            html += '<option value= "' + item.nome_completo + '">'; // ou qualquer estrutura que você queira
          });

          // Coloca o HTML gerado na div de resultado
          $('#usuario_clientes').html(html);
        },
        error: function () {
          $('#usuario_clientes').html('<p>Erro ao buscar dados.</p>');
        }
      });
  });
});

//JS Puro
document.getElementById('telefone').addEventListener('input', function (e) {
    let input = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
    input = input.substring(0, 11); // Limita a 11 dígitos

    // Formata como (XX) XXXXX-XXXX ou (XX) XXXX-XXXX
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
