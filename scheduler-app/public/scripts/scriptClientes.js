document.addEventListener('DOMContentLoaded', async () => {
    const linhasTbClientes = document.getElementById("linhas-clientes")

    async function carregaClientes(params) {
        try {
            const response = await fetch(`./getClientes`);
            const dados = await response.json();
            
        } catch {
            console.log('Erro ao buscar dados.');
        }
    }

})