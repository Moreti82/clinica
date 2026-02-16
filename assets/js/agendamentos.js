document.querySelectorAll('.abrir-modal').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const nome = this.getAttribute('data-nome');

        document.getElementById('agendamento-id').value = id;
        document.getElementById('modal-titulo').textContent = "Alterar status de " + nome;

        document.getElementById('modal-status').style.display = 'flex'; // usa flex para centralizar
    });
});

document.querySelector('.fechar').addEventListener('click', function() {
    document.getElementById('modal-status').style.display = 'none';
});

window.addEventListener('click', function(e) {
    if (e.target.id === 'modal-status') {
        document.getElementById('modal-status').style.display = 'none';
    }
});

document.getElementById('form-status').addEventListener('submit', function(e) {
    e.preventDefault(); // não deixa recarregar

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json()) // agora espera JSON
    .then(data => {
        if (data.sucesso) {
            // Atualiza a célula de status na tabela
            const id = document.getElementById('agendamento-id').value;
            const novoStatus = document.getElementById('status').value;
            const linha = document.querySelector(`button[data-id="${id}"]`).closest('tr');
            linha.querySelector('td:nth-child(5)').textContent = novoStatus;

            alert('Status Alterado com Sucesso!!!');

            // Fecha o modal
            document.getElementById('modal-status').style.display = 'none';

            location.reload();
            
        } else {
            alert("Erro ao atualizar status: " + (data.erro || ""));
        }
    })
    .catch(err => {
        alert("Falha na requisição: " + err);
    });
});
